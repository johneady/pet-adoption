<div class="px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl">
        <!-- Header -->
        <div class="relative mb-8 overflow-hidden rounded-2xl bg-cover bg-center p-8" style="background-image: url('{{ asset('images/default_blog.jpg') }}');">
            <div class="absolute inset-0 bg-zinc-900/45"></div>
            <div class="relative mx-auto max-w-4xl text-center">
                <flux:heading size="xl" class="mb-2 text-white">Blog</flux:heading>
                <flux:text class="text-lg text-white/90">
                    Read our latest articles and updates
                </flux:text>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-[280px_1fr]">
            <!-- Filters Sidebar -->
            <div class="space-y-4">
                <div class="rounded-xl border-2 border-ocean-200 bg-white p-6 shadow-sm shadow-ocean-100 dark:border-ocean-800 dark:bg-zinc-900 dark:shadow-ocean-950">
                    <div class="mb-4 flex items-center justify-between">
                        <flux:heading size="lg" class="text-ocean-900 dark:text-ocean-100">Filters</flux:heading>
                        @if($search || $tagId)
                            <flux:button wire:click="clearFilters" variant="ghost" size="sm">Clear</flux:button>
                        @endif
                    </div>

                    <div class="space-y-4">
                        <!-- Search -->
                        <div>
                            <flux:field>
                                <flux:label>Search</flux:label>
                                <flux:input wire:model.live.debounce.300ms="search" placeholder="Search posts..." />
                            </flux:field>
                        </div>

                        <!-- Tags -->
                        @if($tags->isNotEmpty())
                            <div>
                                <flux:field>
                                    <flux:label>Tag</flux:label>
                                    <flux:select wire:model.live="tagId">
                                        <option value="">All tags</option>
                                        @foreach($tags as $tag)
                                            <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                                        @endforeach
                                    </flux:select>
                                </flux:field>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Posts Grid -->
            <div>
                <div wire:loading.class="opacity-50 transition-opacity" class="min-h-screen">
                    @if($posts->count() > 0)
                        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach($posts as $post)
                                <a href="{{ route('blog.show', $post->slug) }}" wire:navigate
                                   class="group overflow-hidden rounded-xl border-2 border-ocean-200 bg-white transition-all hover:border-ocean-400 hover:shadow-lg hover:shadow-ocean-200/50 dark:border-ocean-800 dark:bg-zinc-900 dark:hover:border-ocean-600 dark:hover:shadow-ocean-900/50">
                                    <!-- Featured Image -->
                                    <div class="relative aspect-video overflow-hidden bg-gradient-to-br from-ocean-50 to-teal-50 dark:from-ocean-950 dark:to-zinc-800">
                                        @if($post->featured_image)
                                            <img src="{{ Storage::disk('public')->url($post->featured_image) }}"
                                                 alt="{{ $post->title }}"
                                                 class="h-full w-full object-cover transition-transform group-hover:scale-105"
                                                 onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                            <div class="hidden h-full w-full items-center justify-center bg-gradient-to-br from-ocean-50 to-teal-50 dark:from-ocean-950 dark:to-zinc-800">
                                                <svg class="h-24 w-24 text-ocean-300 dark:text-ocean-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            </div>
                                        @else
                                            <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-ocean-50 to-teal-50 dark:from-ocean-950 dark:to-zinc-800">
                                                <svg class="h-24 w-24 text-ocean-300 dark:text-ocean-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Post Info -->
                                    <div class="p-4">
                                        <div class="mb-2">
                                            <flux:heading size="lg" class="mb-2">{{ $post->title }}</flux:heading>

                                            <!-- Author and Date -->
                                            <div class="mb-3 flex items-center gap-2 text-sm text-zinc-600 dark:text-zinc-400">
                                                <span>{{ $post->author->name }}</span>
                                                <span>&middot;</span>
                                                <span>{{ $post->published_at->format('M j, Y') }}</span>
                                            </div>
                                        </div>

                                        <!-- Excerpt -->
                                        @if($post->excerpt)
                                            <flux:text size="sm" class="mb-3 line-clamp-3 text-zinc-600 dark:text-zinc-400">
                                                {{ $post->excerpt }}
                                            </flux:text>
                                        @endif

                                        <!-- Tags -->
                                        @if($post->tags->isNotEmpty())
                                            <div class="flex flex-wrap gap-2">
                                                @foreach($post->tags->take(3) as $tag)
                                                    <flux:badge variant="outline" size="sm">
                                                        {{ $tag->name }}
                                                    </flux:badge>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </a>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="mt-8">
                            {{ $posts->links() }}
                        </div>
                    @else
                        <!-- Empty State -->
                        <div class="flex min-h-[400px] items-center justify-center rounded-xl border-2 border-ocean-200 bg-gradient-to-br from-ocean-50 to-teal-50 p-12 dark:border-ocean-800 dark:from-ocean-950 dark:to-zinc-900">
                            <div class="text-center">
                                <svg class="mx-auto h-24 w-24 text-ocean-300 dark:text-ocean-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <flux:heading size="lg" class="mb-2 mt-4 text-ocean-900 dark:text-ocean-100">No posts found</flux:heading>
                                <flux:text class="mb-4 text-ocean-700 dark:text-ocean-300">
                                    Try adjusting your filters to see more results
                                </flux:text>
                                @if($search || $tagId)
                                    <flux:button wire:click="clearFilters" variant="primary">Clear all filters</flux:button>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Loading Indicator -->
                <div wire:loading class="fixed inset-0 z-50 flex items-center justify-center bg-black/5 backdrop-blur-sm">
                    <div class="rounded-lg border-2 border-ocean-300 bg-white px-6 py-4 shadow-lg shadow-ocean-200/50 dark:border-ocean-700 dark:bg-zinc-900 dark:shadow-ocean-900/50">
                        <div class="flex items-center gap-3">
                            <svg class="h-5 w-5 animate-spin text-ocean-600 dark:text-ocean-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <flux:text class="text-ocean-700 dark:text-ocean-300">Loading...</flux:text>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
