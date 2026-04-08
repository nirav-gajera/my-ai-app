@extends('layouts.admin')

@section('title', 'Knowledge Base')

@section('breadcrumb')
    <a href="{{ route('dashboard') }}">Dashboard</a>
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
        <polyline points="9,18 15,12 9,6" /></svg>
    <span class="breadcrumb-active">Knowledge Base</span>
@endsection

@section('page-title', 'Knowledge Base')
@section('page-subtitle', 'Index documents and manage your RAG knowledge sources.')

@section('content')

<div id="knowledge-page" data-state='@json($state)' data-routes='@json([
        "knowledgeDocuments" => route("knowledge-documents.store"),
        "knowledgeDocumentsBase" => url("/knowledge-documents"),
     ])'>

    <div class="knowledge-layout">

        {{-- Upload form --}}
        <div class="admin-card">
            <div class="card-header">
                <h2 class="card-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="16 16 12 12 8 16" />
                        <line x1="12" y1="12" x2="12" y2="21" />
                        <path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3" />
                    </svg>
                    Index New Document
                </h2>
            </div>
            <div class="card-body">
                <form class="upload-form" id="knowledge-form">
                    <div class="form-group">
                        <label class="form-label" for="document-title">Document title</label>
                        <input id="document-title" name="title" type="text" class="form-input" maxlength="255" placeholder="Friendly name (optional)">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="document-file">Upload a file</label>
                        <input id="document-file" name="file" type="file" class="form-input" accept=".txt,.md,.markdown,.csv,.json,.log">
                        <p class="field-hint">Accepted: .txt .md .csv .json .log</p>
                    </div>

                    <div class="form-divider">
                        <span>or paste content</span>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="document-content">Paste text content</label>
                        <textarea id="document-content" name="content" class="form-input" rows="8" placeholder="Paste policies, FAQs, playbooks, or any text you want to make searchable..."></textarea>
                    </div>

                    <button class="btn btn-primary btn-full" id="upload-button" type="submit">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="16 16 12 12 8 16" />
                            <line x1="12" y1="12" x2="12" y2="21" />
                            <path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3" />
                        </svg>
                        Index Document
                    </button>

                    <p class="upload-hint" id="upload-status">
                        {{-- Documents are split into chunks and embedded for semantic search. --}}
                    </p>
                </form>
            </div>
        </div>

        {{-- Document list --}}
        <div class="admin-card">
            <div class="card-header">
                <h2 class="card-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z" />
                    </svg>
                    Indexed Sources
                </h2>
                <span class="count-badge" id="document-count">0</span>
            </div>
            <div class="card-body">
                <div class="document-list" id="document-list">
                    <p class="empty-state">No knowledge documents indexed yet. Upload a file or paste content to get started.</p>
                </div>
                <p class="delete-hint mt-2" id="delete-status">

                </p>
            </div>
        </div>

    </div>
</div>
{{-- Delete Document Modal --}}
<div class="confirm-modal" id="doc-delete-modal" style="display:none">
    <div class="confirm-modal-backdrop" id="doc-modal-backdrop"></div>

    <div class="confirm-modal-box">
        <h3 class="confirm-modal-title">Delete document?</h3>

        <p class="confirm-modal-body" id="doc-delete-text">
            This action cannot be undone.
        </p>

        <div class="confirm-modal-actions">
            <button type="button" class="btn btn-ghost" id="doc-modal-cancel">
                Cancel
            </button>

            <button type="button" class="btn btn-danger-solid" id="doc-confirm-delete">
                Delete
            </button>
        </div>
    </div>
</div>

<div class="confirm-modal" id="doc-reindex-modal" style="display:none">
    <div class="confirm-modal-backdrop" id="doc-reindex-backdrop"></div>
    <div class="confirm-modal-box doc-reindex-box">
        <h3 class="confirm-modal-title">Replace and re-index document</h3>
        <p class="confirm-modal-body" id="doc-reindex-text">
            Upload a replacement file or paste new content. Existing chunks and embeddings will be regenerated.
        </p>

        <form class="upload-form reindex-form" id="doc-reindex-form">
            <div class="form-group">
                <label class="form-label" for="reindex-document-title">Document title</label>
                <input id="reindex-document-title" name="title" type="text" class="form-input" maxlength="255" placeholder="Keep current title or enter a new one">
            </div>

            <div class="form-group">
                <label class="form-label" for="reindex-document-file">Upload replacement file</label>
                <input id="reindex-document-file" name="file" type="file" class="form-input" accept=".txt,.md,.markdown,.csv,.json,.log">
                <p class="field-hint">Accepted: .txt .md .csv .json .log</p>
            </div>

            <div class="form-divider">
                <span>or paste replacement content</span>
            </div>

            <div class="form-group">
                <label class="form-label" for="reindex-document-content">Paste new text content</label>
                <textarea id="reindex-document-content" name="content" class="form-input" rows="7" placeholder="Paste the updated document content here..."></textarea>
            </div>

            <p class="upload-hint" id="doc-reindex-status"></p>

            <div class="confirm-modal-actions">
                <button type="button" class="btn btn-ghost" id="doc-reindex-cancel">
                    Cancel
                </button>

                <button type="submit" class="btn btn-primary" id="doc-reindex-submit">
                    Re-index Document
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

