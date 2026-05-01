<?php

namespace App\Http\Controllers;

use App\Models\KnowledgeDocument;
use App\Services\DocumentParser;
use App\Services\RagService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class KnowledgeDocumentController extends Controller
{
    private const PER_PAGE_OPTIONS = [5, 10, 25, 50, 100];

    private const DOCUMENT_RULES = [
        'title' => ['nullable', 'string', 'max:255'],
        'content' => ['nullable', 'string', 'min:20'],
        'file' => ['nullable', 'file', 'mimes:txt,md,markdown,csv,json,log,pdf', 'max:15000'],
    ];

    private const DOCUMENT_MESSAGES = [
        'title.max' => 'Document title must be 255 characters or fewer.',
        'content.min' => 'Pasted content must be at least 20 characters long.',
        'file.mimes' => 'Upload a supported text file: TXT, MD, MARKDOWN, CSV, JSON, PDF or LOG.',
        'file.file' => 'The uploaded document must be a valid file.',
        'file.max' => 'Document size must be less than 15MB.',
    ];

    public function __construct(
        private readonly DocumentParser $parser
    ) {}

    /** Knowledge base page (HTML) */
    public function page(Request $request)
    {
        $userId = $request->user()->id;
        $perPage = $this->resolvePerPage($request);
        $documents = KnowledgeDocument::forUser($userId)
            ->withCount('chunks')
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return view('knowledge', [
            'state' => [
                'documents' => $this->transformDocuments($documents->items()),
                'pagination' => $this->paginationMeta($documents),
            ],
        ]);
    }

    /** List documents (JSON API) */
    public function index(Request $request)
    {
        $perPage = $this->resolvePerPage($request);

        $documents = KnowledgeDocument::forUser($request->user()->id)
            ->withCount('chunks')
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return response()->json([
            'documents' => $this->transformDocuments($documents->items()),
            'pagination' => $this->paginationMeta($documents),
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
            'message' => 'Knowledge document indexed successfully.',
            'document' => [
                'id' => $document->id,
                'title' => $document->title,
                'source_name' => $document->source_name,
                'source_type' => $document->source_type,
                'chunk_count' => $document->chunks_count,
                'created_at' => optional($document->created_at)->toIso8601String(),
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
            'message' => 'Knowledge document re-indexed successfully.',
            'document' => [
                'id' => $document->id,
                'title' => $document->title,
                'source_name' => $document->source_name,
                'source_type' => $document->source_type,
                'chunk_count' => $document->chunks_count,
                'created_at' => optional($document->created_at)->toIso8601String(),
            ],
        ]);
    }

    /** Delete a document (JSON API) */
    public function destroy(Request $request, KnowledgeDocument $knowledgeDocument)
    {
        abort_unless($knowledgeDocument->user_id === $request->user()->id, 404);

        $knowledgeDocument->delete();

        return response()->json(['message' => 'Knowledge document deleted.']);
    }

    private function resolveDocumentPayload(Request $request, ?KnowledgeDocument $existingDocument = null): array
    {
        $validated = $request->validate(self::DOCUMENT_RULES, self::DOCUMENT_MESSAGES);

        if (! $request->filled('content') && ! $request->hasFile('file')) {
            abort(response()->json([
                'message' => 'Provide either pasted content or a supported text file.',
            ], 422));
        }

        $sourceName = $existingDocument?->source_name;
        $sourceType = 'text';
        $content = trim((string) ($validated['content'] ?? ''));

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $content = $this->parser->parse($file);
            $sourceName = $file->getClientOriginalName();
            $sourceType = 'file';
        }

        if ($content === '') {
            abort(response()->json(['message' => 'The provided document is empty or unreadable.'], 422));
        }

        $title = trim((string) ($validated['title'] ?? ''));
        $title = $title !== ''
            ? $title
            : ($existingDocument?->title ?: ($sourceName ?: Str::limit(Str::squish($content), 60, '...')));

        return [$title, $content, $sourceName, $sourceType];
    }

    private function transformDocuments(iterable $documents): array
    {
        return collect($documents)->map(fn (KnowledgeDocument $d) => [
            'id' => $d->id,
            'title' => $d->title,
            'source_name' => $d->source_name,
            'source_type' => $d->source_type,
            'chunk_count' => $d->chunks_count,
            'created_at' => optional($d->created_at)->toIso8601String(),
        ])->values()->all();
    }

    private function paginationMeta($paginator): array
    {
        return [
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
            'from' => $paginator->firstItem(),
            'to' => $paginator->lastItem(),
        ];
    }

    private function resolvePerPage(Request $request): int
    {
        $perPage = (int) $request->integer('per_page', self::PER_PAGE_OPTIONS[0]);

        return in_array($perPage, self::PER_PAGE_OPTIONS, true)
            ? $perPage
            : self::PER_PAGE_OPTIONS[0];
    }
}
