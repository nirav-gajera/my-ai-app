<?php

namespace App\Http\Controllers;

use App\Models\KnowledgeDocument;
use App\Services\RagService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class KnowledgeDocumentController extends Controller
{
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
        $validated = $request->validate([
            'title'   => ['nullable', 'string', 'max:255'],
            'content' => ['nullable', 'string', 'min:20'],
            'file'    => ['nullable', 'file', 'mimes:txt,md,markdown,csv,json,log'],
        ]);

        if (!$request->filled('content') && !$request->hasFile('file')) {
            return response()->json([
                'message' => 'Provide either pasted content or a supported text file.',
            ], 422);
        }

        $sourceName = null;
        $sourceType = 'text';
        $content    = trim((string) ($validated['content'] ?? ''));

        if ($request->hasFile('file')) {
            $file       = $request->file('file');
            $content    = trim((string) file_get_contents($file->getRealPath()));
            $sourceName = $file->getClientOriginalName();
            $sourceType = 'file';
        }

        if ($content === '') {
            return response()->json(['message' => 'The provided document is empty.'], 422);
        }

        $title = trim((string) ($validated['title'] ?? ''));
        $title = $title !== '' ? $title : ($sourceName ?: Str::limit(Str::squish($content), 60, '...'));

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

    /** Delete a document (JSON API) */
    public function destroy(Request $request, KnowledgeDocument $knowledgeDocument)
    {
        // dd($knowledgeDocument);
        abort_unless($knowledgeDocument->user_id === $request->user()->id, 404);

        $knowledgeDocument->delete();

        return response()->json(['message' => 'Knowledge document deleted.']);
    }
}
