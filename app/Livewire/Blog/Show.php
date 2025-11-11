<?php

namespace App\Livewire\Blog;

use App\Models\BlogPost;
use Livewire\Component;

class Show extends Component
{
    public BlogPost $post;

    public function mount(string $slug): void
    {
        $this->post = BlogPost::query()
            ->with(['author', 'tags'])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->firstOrFail();
    }

    public function render(): mixed
    {
        return view('livewire.blog.show');
    }
}
