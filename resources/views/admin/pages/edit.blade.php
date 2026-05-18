@extends('layouts.admin')

@section('title', 'Edit Page: ' . $page->title)

@section('breadcrumb')
    <a href="{{ route('dashboard') }}">Dashboard</a>
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
        <polyline points="9,18 15,12 9,6" />
    </svg>
    <a href="{{ route('admin.pages.index') }}">Pages</a>
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
        <polyline points="9,18 15,12 9,6" />
    </svg>
    <span class="breadcrumb-active">Edit</span>
@endsection

@section('page-title', 'Edit Page')
@section('page-subtitle', 'Update page details and content blocks.')

@push('head')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        .repeater-item {
            position: relative;
            background: rgba(255, 255, 255, 0.4);
            border: 1px solid rgba(19, 41, 72, 0.1);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            transition: all 0.2s ease;
        }
        .repeater-item:hover {
            background: rgba(255, 255, 255, 0.6);
            border-color: var(--primary);
        }
        .repeater-remove {
            position: absolute;
            top: 10px;
            right: 10px;
            color: var(--danger);
            cursor: pointer;
            padding: 5px;
            border-radius: 6px;
        }
        .repeater-remove:hover {
            background: var(--danger-light);
        }
    </style>
@endpush

@section('content')
    <div class="admin-card" x-data="pageRepeater()">
        <form method="POST" action="{{ route('admin.pages.update', $page) }}">
            @csrf
            @method('PUT')
            <div class="card-header">
                <div class="card-header-info">
                    <h2 class="card-title">Basic Information</h2>
                    <span class="header-status">Define the page structure and URL.</span>
                </div>
            </div>

            <div class="card-body">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="form-group">
                        <label class="form-label" for="title">Page Title</label>
                        <input id="title" class="form-input {{ $errors->has('title') ? 'input-error' : '' }}" 
                               name="title" type="text" value="{{ old('title', $page->title) }}" 
                               placeholder="e.g. Privacy Policy">
                        @error('title') <p class="field-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="slug">URL Key (Slug)</label>
                        <div class="relative">
                            <span aria-hidden="true" class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">/p/</span>
                            <input id="slug" class="form-input {{ $errors->has('slug') ? 'input-error' : '' }}" 
                                   style="padding-left: 2.25rem;"
                                   name="slug" type="text" value="{{ old('slug', $page->slug) }}" 
                                   placeholder="privacy-policy" x-model="slug">
                        </div>
                        @error('slug') <p class="field-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="form-group mt-6">
                    <label class="form-label" for="content_heading">Content Heading (H1)</label>
                    <input id="content_heading" class="form-input {{ $errors->has('content_heading') ? 'input-error' : '' }}" 
                           name="content_heading" type="text" value="{{ old('content_heading', $page->content_heading) }}" 
                           placeholder="Heading displayed on the page">
                    @error('content_heading') <p class="field-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-group form-group-full mt-6">
                    <input type="hidden" name="is_active" value="0">
                    <label class="setting-toggle">
                        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $page->is_active))>
                        <span class="setting-toggle-ui">
                            <span class="setting-toggle-copy">
                                <strong>Is Page Active</strong>
                                <span>If inactive, this page will return a 404 error when accessed.</span>
                            </span>
                        </span>
                    </label>
                </div>
            </div>

            <div class="card-header border-t mt-8">
                <div class="card-header-info">
                    <h2 class="card-title">Page Contents</h2>
                    <span class="header-status">Manage different versions or scheduled blocks of content.</span>
                </div>
                <button type="button" class="btn btn-primary btn-sm" @click="addContent()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Add Content Block
                </button>
            </div>

            <div class="card-body">
                <template x-for="(content, index) in contents" :key="index">
                    <div class="repeater-item">
                        <button type="button" class="repeater-remove" @click="removeContent(index)" x-show="contents.length > 1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="3 6 5 6 21 6"></polyline>
                                <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"></path>
                                <path d="M10 11v6M14 11v6M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"></path>
                            </svg>
                        </button>

                        <input type="hidden" :name="`contents[${index}][id]`" x-model="content.id">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div class="form-group">
                                <label class="form-label text-xs">Active From</label>
                                <div class="relative">
                                    <input :name="`contents[${index}][active_from]`" type="text" 
                                       class="form-input datetime-picker pr-10" placeholder="Immediately"
                                       x-model="content.active_from">
                                    <svg class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                        <line x1="16" y1="2" x2="16" y2="6"></line>
                                        <line x1="8" y1="2" x2="8" y2="6"></line>
                                        <line x1="3" y1="10" x2="21" y2="10"></line>
                                    </svg>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label text-xs">Active Until</label>
                                <div class="relative">
                                    <input :name="`contents[${index}][active_to]`" type="text" 
                                       class="form-input datetime-picker pr-10" placeholder="Forever"
                                       x-model="content.active_to">
                                    <svg class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                        <line x1="16" y1="2" x2="16" y2="6"></line>
                                        <line x1="8" y1="2" x2="8" y2="6"></line>
                                        <line x1="3" y1="10" x2="21" y2="10"></line>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label text-xs">Content (Markdown/HTML supported)</label>
                            <textarea :name="`contents[${index}][content]`" class="form-input min-h-[150px]" 
                                      placeholder="Enter page content here..." required
                                      x-model="content.content"></textarea>
                        </div>
                    </div>
                </template>

                <div x-show="contents.length === 0" class="text-center py-8 text-gray-500">
                    <p>No content blocks added. Click "Add Content Block" to begin.</p>
                </div>

                <div class="form-actions mt-8">
                    <a href="{{ route('admin.pages.index') }}" class="btn btn-ghost">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Page</button>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        @php
            $mappedContents = $page->contents->map(function($c) {
                return [
                    'id' => $c->id,
                    'content' => $c->content,
                    'active_from' => $c->active_from ? $c->active_from->format('Y-m-d H:i') : '',
                    'active_to' => $c->active_to ? $c->active_to->format('Y-m-d H:i') : '',
                ];
            })->toArray();
        @endphp
        function pageRepeater() {
            return {
                slug: '{{ old('slug', $page->slug) }}',
                contents: @json($mappedContents),
                init() {
                    if (this.contents.length === 0) {
                        this.addContent();
                    }
                    this.$nextTick(() => {
                        this.initPickers();
                    });
                },
                addContent() {
                    this.contents.push({ id: '', content: '', active_from: '', active_to: '' });
                    this.$nextTick(() => {
                        this.initPickers();
                    });
                },
                removeContent(index) {
                    this.contents.splice(index, 1);
                },
                initPickers() {
                    flatpickr(".datetime-picker", {
                        enableTime: true,
                        dateFormat: "Y-m-d H:i",
                        minDate: new Date(),
                        animate: false,
                    });
                }
            }
        }
    </script>
@endpush
