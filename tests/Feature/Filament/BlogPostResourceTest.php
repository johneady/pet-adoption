<?php

declare(strict_types=1);

use App\Filament\Resources\BlogPosts\Pages\CreateBlogPost;
use App\Filament\Resources\BlogPosts\Pages\EditBlogPost;
use App\Filament\Resources\BlogPosts\Pages\ListBlogPosts;
use App\Models\BlogPost;
use App\Models\Tag;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

beforeEach(function () {
    Filament::setCurrentPanel('admin');
    $this->admin = User::factory()->admin()->create();
});

test('blog post resource table shows all blog posts', function () {
    $blogPosts = BlogPost::factory()->count(5)->create();

    actingAs($this->admin);

    Livewire::test(ListBlogPosts::class)
        ->assertCanSeeTableRecords($blogPosts)
        ->assertCountTableRecords(5);
});

test('blog post resource table can search by title', function () {
    $blogPost = BlogPost::factory()->create(['title' => 'Laravel Tips and Tricks']);
    BlogPost::factory()->create(['title' => 'PHP Best Practices']);

    actingAs($this->admin);

    Livewire::test(ListBlogPosts::class)
        ->searchTable('Laravel')
        ->assertCanSeeTableRecords([$blogPost])
        ->assertCountTableRecords(1);
});

test('blog post resource can create a new blog post', function () {
    actingAs($this->admin);

    Livewire::test(CreateBlogPost::class)
        ->set('data.title', 'New Blog Post')
        ->set('data.slug', 'new-blog-post')
        ->set('data.excerpt', 'This is an excerpt')
        ->set('data.content', 'This is the content of the blog post.')
        ->set('data.status', 'draft')
        ->call('create')
        ->assertHasNoFormErrors();

    expect(BlogPost::where('title', 'New Blog Post')->exists())->toBeTrue();
});

test('blog post creation automatically sets user_id to current user', function () {
    actingAs($this->admin);

    Livewire::test(CreateBlogPost::class)
        ->set('data.title', 'Test Post')
        ->set('data.slug', 'test-post')
        ->set('data.content', 'Test content')
        ->set('data.status', 'draft')
        ->call('create')
        ->assertHasNoFormErrors();

    $blogPost = BlogPost::where('slug', 'test-post')->first();
    expect($blogPost->user_id)->toBe($this->admin->id);
});

test('blog post resource can create a blog post with tags', function () {
    $tag1 = Tag::factory()->create(['name' => 'Laravel']);
    $tag2 = Tag::factory()->create(['name' => 'PHP']);

    actingAs($this->admin);

    Livewire::test(CreateBlogPost::class)
        ->set('data.title', 'Blog Post with Tags')
        ->set('data.slug', 'blog-post-with-tags')
        ->set('data.content', 'Content here')
        ->set('data.status', 'draft')
        ->set('data.tags', [$tag1->id, $tag2->id])
        ->call('create')
        ->assertHasNoFormErrors();

    $blogPost = BlogPost::where('slug', 'blog-post-with-tags')->first();
    expect($blogPost->tags()->count())->toBe(2);
    expect($blogPost->tags->pluck('name')->toArray())->toContain('Laravel', 'PHP');
});

test('blog post resource validates required fields on create', function () {
    actingAs($this->admin);

    Livewire::test(CreateBlogPost::class)
        ->fillForm([
            'title' => '',
            'slug' => '',
            'content' => '',
        ])
        ->call('create')
        ->assertHasFormErrors(['title', 'slug', 'content']);
});

test('blog post resource validates unique slug on create', function () {
    BlogPost::factory()->create(['slug' => 'existing-slug']);

    actingAs($this->admin);

    Livewire::test(CreateBlogPost::class)
        ->fillForm([
            'title' => 'New Post',
            'slug' => 'existing-slug',
            'content' => 'Content',
            'status' => 'draft',
        ])
        ->call('create')
        ->assertHasFormErrors(['slug']);
});

test('blog post resource can edit an existing blog post', function () {
    $blogPost = BlogPost::factory()->create([
        'title' => 'Original Title',
    ]);

    actingAs($this->admin);

    Livewire::test(EditBlogPost::class, ['record' => $blogPost->id])
        ->set('data.title', 'Updated Title')
        ->call('save')
        ->assertHasNoFormErrors();

    expect($blogPost->fresh()->title)->toBe('Updated Title');
});

test('blog post resource can filter by status', function () {
    $draftPost = BlogPost::factory()->draft()->create();
    $publishedPost = BlogPost::factory()->published()->create();

    actingAs($this->admin);

    Livewire::test(ListBlogPosts::class)
        ->filterTable('status', 'draft')
        ->assertCanSeeTableRecords([$draftPost])
        ->assertCanNotSeeTableRecords([$publishedPost]);
});

test('blog post resource can filter by tags', function () {
    $tag1 = Tag::factory()->create(['name' => 'Laravel']);
    $tag2 = Tag::factory()->create(['name' => 'PHP']);

    $post1 = BlogPost::factory()->create();
    $post1->tags()->attach($tag1);

    $post2 = BlogPost::factory()->create();
    $post2->tags()->attach($tag2);

    actingAs($this->admin);

    Livewire::test(ListBlogPosts::class)
        ->filterTable('tags', [$tag1->id])
        ->assertCanSeeTableRecords([$post1])
        ->assertCanNotSeeTableRecords([$post2]);
});

test('changing status to published auto-fills published_at if not set', function () {
    actingAs($this->admin);

    Livewire::test(CreateBlogPost::class)
        ->fillForm([
            'title' => 'Auto Published Date Test',
            'slug' => 'auto-published-date',
            'content' => 'Test content',
            'status' => 'draft',
        ])
        ->set('data.status', 'published')
        ->assertSet('data.published_at', fn ($value) => $value !== null);
});

test('blog post resource can update tags on edit', function () {
    $tag1 = Tag::factory()->create(['name' => 'Laravel']);
    $tag2 = Tag::factory()->create(['name' => 'PHP']);
    $tag3 = Tag::factory()->create(['name' => 'Testing']);

    $blogPost = BlogPost::factory()->create();
    $blogPost->tags()->attach([$tag1->id, $tag2->id]);

    actingAs($this->admin);

    Livewire::test(EditBlogPost::class, ['record' => $blogPost->id])
        ->set('data.tags', [$tag2->id, $tag3->id])
        ->call('save')
        ->assertHasNoFormErrors();

    $blogPost->refresh();
    expect($blogPost->tags()->count())->toBe(2);
    expect($blogPost->tags->pluck('name')->toArray())->toContain('PHP', 'Testing');
    expect($blogPost->tags->pluck('name')->toArray())->not->toContain('Laravel');
});

test('blog post resource shows correct count of records', function () {
    BlogPost::factory()->count(10)->create();

    actingAs($this->admin);

    Livewire::test(ListBlogPosts::class)
        ->assertCountTableRecords(10);
});

test('published blog posts can be changed to archived', function () {
    $blogPost = BlogPost::factory()->published()->create();

    actingAs($this->admin);

    Livewire::test(EditBlogPost::class, ['record' => $blogPost->id])
        ->assertSchemaStateSet([
            'status' => 'published',
        ])
        ->set('data.status', 'archived')
        ->call('save')
        ->assertHasNoFormErrors();

    expect($blogPost->fresh()->status)->toBe('archived');
});

test('draft blog posts can be changed to published', function () {
    $blogPost = BlogPost::factory()->draft()->create();

    actingAs($this->admin);

    Livewire::test(EditBlogPost::class, ['record' => $blogPost->id])
        ->assertSchemaStateSet([
            'status' => 'draft',
        ])
        ->set('data.status', 'published')
        ->call('save')
        ->assertHasNoFormErrors();

    $blogPost->refresh();
    expect($blogPost->status)->toBe('published');
    expect($blogPost->published_at)->not->toBeNull();
});
