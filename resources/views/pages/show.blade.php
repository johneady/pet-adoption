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
            <article class="rounded-xl border-2 border-ocean-200 bg-white dark:border-ocean-800 dark:bg-zinc-900">
                <div class="p-8">
                    <!-- Main Content -->
                    <div
                        class="prose prose-zinc max-w-none dark:prose-invert prose-headings:font-semibold prose-headings:text-ocean-900 dark:prose-headings:text-ocean-100 prose-h1:text-3xl prose-h2:text-2xl prose-h3:text-xl prose-h4:text-lg prose-p:text-ocean-700 prose-p:dark:text-ocean-300 prose-a:text-teal-600 prose-a:no-underline hover:prose-a:text-teal-700 hover:prose-a:underline dark:prose-a:text-teal-400 dark:hover:prose-a:text-teal-300 prose-strong:font-semibold prose-ul:list-disc prose-ol:list-decimal prose-table:border-collapse prose-th:border-2 prose-th:border-ocean-300 prose-th:bg-ocean-100 prose-th:p-2 dark:prose-th:border-ocean-600 dark:prose-th:bg-ocean-900 prose-td:border-2 prose-td:border-ocean-300 prose-td:p-2 dark:prose-td:border-ocean-600">
                        {!! $page->content !!}
                    </div>
                </div>
            </article>
        </div>
    </div>
</x-layouts.app>
