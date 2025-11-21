<div class="px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-4xl">
        <!-- Breadcrumb -->
        <div class="mb-6">
            <flux:breadcrumbs>
                <flux:breadcrumbs.item href="/" wire:navigate>Home</flux:breadcrumbs.item>
                <flux:breadcrumbs.item href="{{ route('blog.index') }}" wire:navigate>Blog</flux:breadcrumbs.item>
                <flux:breadcrumbs.item>{{ $post->title }}</flux:breadcrumbs.item>
            </flux:breadcrumbs>
        </div>

        <!-- Article -->
        <article class="rounded-xl border-2 border-ocean-200 bg-white dark:border-ocean-800 dark:bg-zinc-900">
            <!-- Featured Image -->
            @if($post->featured_image)
                <div class="relative aspect-video overflow-hidden rounded-t-xl bg-linear-to-br from-ocean-50 to-teal-50 dark:from-ocean-950 dark:to-zinc-800">
                    <img src="{{ Storage::url($post->featured_image) }}"
                         alt="{{ $post->title }}"
                         class="h-full w-full object-cover"
                         onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="hidden h-full w-full items-center justify-center bg-linear-to-br from-ocean-50 to-teal-50 dark:from-ocean-950 dark:to-zinc-800">
                        <svg class="h-32 w-32 text-ocean-300 dark:text-ocean-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
            @endif

            <!-- Content -->
            <div class="p-8">
                <!-- Title -->
                <flux:heading size="2xl" class="mb-4 text-ocean-900 dark:text-ocean-100">{{ $post->title }}</flux:heading>

                <!-- Meta Information -->
                <div class="mb-6 flex items-center gap-4 border-b-2 border-ocean-200 pb-6 dark:border-ocean-800">
                    <div class="flex items-center gap-3">
                        <img src="{{ $post->author->profile_picture ? $post->author->profilePictureUrl() : url('/images/default-avatar.svg') }}" alt="{{ $post->author->name }}" class="size-10 rounded-full object-cover ring-2 ring-ocean-200 dark:ring-ocean-800">
                        <div>
                            <flux:text class="font-semibold text-ocean-900 dark:text-ocean-100">{{ $post->author->name }}</flux:text>
                            <flux:text size="sm" class="text-ocean-600 dark:text-ocean-400">
                                {{ $post->published_at->format('M j, Y') }}
                            </flux:text>
                        </div>
                    </div>
                </div>

                <!-- Excerpt -->
                @if($post->excerpt)
                    <div class="mb-6 rounded-lg border-2 border-ocean-200 bg-ocean-50 p-4 dark:border-ocean-700 dark:bg-ocean-950/30">
                        <flux:text size="lg" class="font-medium text-ocean-700 dark:text-ocean-300">
                            {{ $post->excerpt }}
                        </flux:text>
                    </div>
                @endif

                <!-- Main Content -->
                <div class="prose prose-zinc max-w-none dark:prose-invert prose-headings:font-semibold prose-headings:text-ocean-900 dark:prose-headings:text-ocean-100 prose-h1:text-3xl prose-h2:text-2xl prose-h3:text-xl prose-p:text-ocean-700 prose-p:dark:text-ocean-300 prose-a:text-teal-600 prose-a:no-underline hover:prose-a:text-teal-700 hover:prose-a:underline dark:prose-a:text-teal-400 dark:hover:prose-a:text-teal-300 prose-strong:font-semibold prose-ul:list-disc prose-ol:list-decimal">
                    {!! $post->content !!}
                </div>

                <!-- Tags -->
                @if($post->tags->isNotEmpty())
                    <div class="mt-8 border-t-2 border-ocean-200 pt-6 dark:border-ocean-800">
                        <flux:text size="sm" class="mb-3 font-semibold text-ocean-900 dark:text-ocean-100">Tagged with:</flux:text>
                        <div class="flex flex-wrap gap-2">
                            @foreach($post->tags as $tag)
                                <a href="{{ route('blog.index', ['tagId' => $tag->id]) }}" wire:navigate>
                                    <flux:badge variant="outline" class="cursor-pointer border-ocean-300 text-ocean-700 hover:bg-ocean-100 dark:border-ocean-700 dark:text-ocean-300 dark:hover:bg-ocean-950/50">
                                        {{ $tag->name }}
                                    </flux:badge>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </article>

        <!-- Back to Blog -->
        <div class="mt-8">
            <flux:button href="{{ route('blog.index') }}" variant="ghost" wire:navigate icon="arrow-left">
                Back to all posts
            </flux:button>
        </div>
    </div>
</div>
