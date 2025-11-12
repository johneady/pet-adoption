<x-layouts.app :title="$page->meta_title ?? $page->title">
    <div class="px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-4xl">
            <!-- Breadcrumb -->
            <div class="mb-6">
                <flux:breadcrumbs>
                    <flux:breadcrumbs.item href="/" wire:navigate>Home</flux:breadcrumbs.item>
                    <flux:breadcrumbs.item>{{ $page->title }}</flux:breadcrumbs.item>
                </flux:breadcrumbs>
            </div>

            <!-- Page Content -->
            <article class="rounded-xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
                <div class="p-8">
                    <!-- Title -->
                    <flux:heading size="2xl" class="mb-8">{{ $page->title }}</flux:heading>

                    <!-- Main Content -->
                    <div class="prose prose-zinc max-w-none dark:prose-invert prose-headings:font-semibold prose-h1:text-3xl prose-h2:text-2xl prose-h3:text-xl prose-h4:text-lg prose-p:text-zinc-700 prose-p:dark:text-zinc-300 prose-a:text-ocean-600 prose-a:no-underline hover:prose-a:underline dark:prose-a:text-ocean-400 prose-strong:font-semibold prose-ul:list-disc prose-ol:list-decimal prose-table:border-collapse prose-th:border prose-th:border-zinc-300 prose-th:bg-zinc-100 prose-th:p-2 dark:prose-th:border-zinc-600 dark:prose-th:bg-zinc-800 prose-td:border prose-td:border-zinc-300 prose-td:p-2 dark:prose-td:border-zinc-600">
                        {!! $page->content !!}
                    </div>
                </div>
            </article>
        </div>
    </div>
</x-layouts.app>
