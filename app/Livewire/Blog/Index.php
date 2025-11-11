<?php

namespace App\Livewire\Blog;

use App\Models\BlogPost;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public ?int $tagId = null;

    public function mount(): void
    {
        //
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedTagId(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'tagId']);
        $this->resetPage();
    }

    public function getTagsProperty(): Collection
    {
        return Tag::query()
            ->has('blogPosts')
            ->orderBy('name')
            ->get();
    }

    public function getPostsProperty(): mixed
    {
        return BlogPost::query()
            ->with(['author', 'tags'])
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->when($this->search, function (Builder $query) {
                $query->where(function (Builder $q) {
                    $q->where('title', 'like', "%{$this->search}%")
                        ->orWhere('excerpt', 'like', "%{$this->search}%")
                        ->orWhere('content', 'like', "%{$this->search}%");
                });
            })
            ->when($this->tagId, function (Builder $query) {
                $query->whereHas('tags', function (Builder $q) {
                    $q->where('tags.id', $this->tagId);
                });
            })
            ->latest('published_at')
            ->paginate(12);
    }

    public function render(): mixed
    {
        return view('livewire.blog.index', [
            'posts' => $this->posts,
            'tags' => $this->tags,
        ]);
    }
}
