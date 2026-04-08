<?php

namespace App\Http\Controllers;

use App\Models\KnowledgeDocument;
use App\Services\RagService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class KnowledgeDocumentController extends Controller
{
    private const DOCUMENT_RULES = [
        'title'   => ['nullable', 'string', 'max:255'],
        'content' => ['nullable', 'string', 'min:20'],
        'file'    => ['nullable', 'file', 'mimes:txt,md,markdown,csv,json,log'],
    ];

    /** Knowledge base page (HTML) */
    public function page(Request $request)
    {
        $userId    = $request->user()->id;
        $documents = KnowledgeDocument::forUser($userId)->withCount('chunks')->latest()->get();

        return view('knowledge', [
            'state' => [
                'documents' => $documents->map(fn (KnowledgeDocument $d) => [
                    'id'          => $d->id,
                    'title'       => $d->title,
                    'source_name' => $d->source_name,
                    'source_type' => $d->source_type,
                    'chunk_count' => $d->chunks_count,
                    'created_at'  => optional($d->created_at)->toIso8601String(),
                ])->values(),
            ],
        ]);
    }

    /** List documents (JSON API) */
    public function index(Request $request)
    {
        $documents = KnowledgeDocument::forUser($request->user()->id)
            ->withCount('chunks')
            ->latest()
            ->get();

        return response()->json([
            'documents' => $documents->map(fn (KnowledgeDocument $d) => [
                'id'          => $d->id,
                'title'       => $d->title,
                'source_name' => $d->source_name,
                'source_type' => $d->source_type,
                'chunk_count' => $d->chunks_count,
                'created_at'  => optional($d->created_at)->toIso8601String(),
            ])->values(),
        ]);
    }

    /** Upload and index a document (JSON API) */
    public function store(Request $request, RagService $rag)
    {
        [$title, $content, $sourceName, $sourceType] = $this->resolveDocumentPayload($request);

        $document = $rag->ingest(
            $request->user()->id,
            $title,
            $content,
            $sourceName,
            $sourceType,
        );

        return response()->json([
            'message'  => 'Knowledge document indexed successfully.',
            'document' => [
                'id'          => $document->id,
                'title'       => $document->title,
                'source_name' => $document->source_name,
                'source_type' => $document->source_type,
                'chunk_count' => $document->chunks_count,
                'created_at'  => optional($document->created_at)->toIso8601String(),
            ],
        ], 201);
    }

    /** Replace and re-index an existing document (JSON API) */
    public function reindex(Request $request, KnowledgeDocument $knowledgeDocument, RagService $rag)
    {
        abort_unless($knowledgeDocument->user_id === $request->user()->id, 404);

        [$title, $content, $sourceName, $sourceType] = $this->resolveDocumentPayload($request, $knowledgeDocument);

        $document = $rag->reindex(
            $knowledgeDocument,
            $title,
            $content,
            $sourceName,
            $sourceType,
        );

        return response()->json([
            'message'  => 'Knowledge document re-indexed successfully.',
            'document' => [
                'id'          => $document->id,
                'title'       => $document->title,
                'source_name' => $document->source_name,
                'source_type' => $document->source_type,
                'chunk_count' => $document->chunks_count,
                'created_at'  => optional($document->created_at)->toIso8601String(),
            ],
        ]);
    }

    /** Delete a document (JSON API) */
    public function destroy(Request $request, KnowledgeDocument $knowledgeDocument)
    {
        // dd($knowledgeDocument);
        abort_unless($knowledgeDocument->user_id === $request->user()->id, 404);

        $knowledgeDocument->delete();

        return response()->json(['message' => 'Knowledge document deleted.']);
    }

    private function resolveDocumentPayload(Request $request, ?KnowledgeDocument $existingDocument = null): array
    {
        $validated = $request->validate(self::DOCUMENT_RULES);

        if (!$request->filled('content') && !$request->hasFile('file')) {
            abort(response()->json([
                'message' => 'Provide either pasted content or a supported text file.',
            ], 422));
        }

        $sourceName = $existingDocument?->source_name;
        $sourceType = 'text';
        $content    = trim((string) ($validated['content'] ?? ''));

        if ($request->hasFile('file')) {
            $file       = $request->file('file');
            $content    = trim((string) file_get_contents($file->getRealPath()));
            $sourceName = $file->getClientOriginalName();
            $sourceType = 'file';
        }

        if ($content === '') {
            abort(response()->json(['message' => 'The provided document is empty.'], 422));
        }

        $title = trim((string) ($validated['title'] ?? ''));
        $title = $title !== ''
            ? $title
            : ($existingDocument?->title ?: ($sourceName ?: Str::limit(Str::squish($content), 60, '...')));

        return [$title, $content, $sourceName, $sourceType];
    }
}
