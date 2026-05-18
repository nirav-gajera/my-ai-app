<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\PageContent;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->input('per_page', 10);
        $search = $request->input('search');
        $status = $request->input('status');

        $pages = Page::query()
            ->when($search, function ($query, $search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            })
            ->when($status !== null, function ($query) use ($status) {
                if ($status === 'active') {
                    $query->where('is_active', true);
                } elseif ($status === 'inactive') {
                    $query->where('is_active', false);
                }
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.pages.index', compact('pages'));
    }

    public function create()
    {
        return view('admin.pages.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:pages,slug',
            'content_heading' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'contents' => 'nullable|array',
            'contents.*.content' => 'required|string',
            'contents.*.active_from' => 'nullable|date',
            'contents.*.active_to' => 'nullable|date',
        ]);

        $page = Page::create([
            'title' => $data['title'],
            'slug' => $data['slug'],
            'content_heading' => $data['content_heading'] ?? $data['title'],
            'is_active' => $request->boolean('is_active'),
        ]);

        if (! empty($data['contents'])) {
            foreach ($data['contents'] as $contentData) {
                $page->contents()->create([
                    'content' => $contentData['content'],
                    'active_from' => empty($contentData['active_from']) ? null : $contentData['active_from'],
                    'active_to' => empty($contentData['active_to']) ? null : $contentData['active_to'],
                ]);
            }
        }

        return redirect()->route('admin.pages.index')
            ->with('success', 'Page created successfully!');
    }

    public function edit(Page $page)
    {
        $page->load('contents');

        return view('admin.pages.edit', compact('page'));
    }

    public function update(Request $request, Page $page)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:pages,slug,'.$page->id,
            'content_heading' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'contents' => 'nullable|array',
            'contents.*.id' => 'nullable|integer',
            'contents.*.content' => 'required|string',
            'contents.*.active_from' => 'nullable|date',
            'contents.*.active_to' => 'nullable|date',
        ]);

        $page->update([
            'title' => $data['title'],
            'slug' => $data['slug'],
            'content_heading' => $data['content_heading'] ?? $data['title'],
            'is_active' => $request->boolean('is_active'),
        ]);

        // Sync contents
        $contentIds = [];
        if (! empty($data['contents'])) {
            foreach ($data['contents'] as $contentData) {
                $activeFrom = empty($contentData['active_from']) ? null : $contentData['active_from'];
                $activeTo = empty($contentData['active_to']) ? null : $contentData['active_to'];

                if (! empty($contentData['id'])) {
                    $content = PageContent::find($contentData['id']);
                    if ($content && $content->page_id == $page->id) {
                        $content->update([
                            'content' => $contentData['content'],
                            'active_from' => $activeFrom,
                            'active_to' => $activeTo,
                        ]);
                        $contentIds[] = $content->id;
                    }
                } else {
                    $newContent = $page->contents()->create([
                        'content' => $contentData['content'],
                        'active_from' => $activeFrom,
                        'active_to' => $activeTo,
                    ]);
                    $contentIds[] = $newContent->id;
                }
            }
        }

        // Delete removed contents
        $page->contents()->whereNotIn('id', $contentIds)->delete();

        try {
            \App\Jobs\NotifyUsersOfPageUpdate::dispatch($page)->delay(now()->addSeconds(2));
        } catch (\Exception $e) {
            \Log::info("Error dispatching job", [
                "message" => $e->getMessage(),
                "trace" => $e->getTraceAsString(),
            ]);
        }

        return redirect()->route('admin.pages.index')
            ->with('success', 'Page updated successfully!');
    }

    public function destroy(Page $page)
    {
        $page->delete();

        return redirect()->route('admin.pages.index')
            ->with('success', 'Page deleted successfully!');
    }

    /**
     * Public show method to display page by slug
     */
    public function showPublic(string $slug)
    {
        $page = Page::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $content = $page->getCurrentContent();

        if (! $content) {
            abort(404, 'Page content not active for current time.');
        }

        return view('pages.show', compact('page', 'content'));
    }
}
