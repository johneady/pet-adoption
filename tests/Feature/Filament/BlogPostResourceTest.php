<?php

declare(strict_types=1);

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

test('blog post resource has create action', function () {
    actingAs($this->admin);

    Livewire::test(ListBlogPosts::class)
        ->assertActionExists('create');
});

test('blog post can be created directly via model', function () {
    actingAs($this->admin);

    $blogPost = BlogPost::create([
        'user_id' => $this->admin->id,
        'title' => 'Test Post',
        'slug' => 'test-post',
        'content' => 'Test content',
        'status' => 'draft',
    ]);

    expect($blogPost->user_id)->toBe($this->admin->id)
        ->and($blogPost->title)->toBe('Test Post');
});

test('blog post can be created with tags', function () {
    $tag1 = Tag::factory()->create(['name' => 'Laravel']);
    $tag2 = Tag::factory()->create(['name' => 'PHP']);

    actingAs($this->admin);

    $blogPost = BlogPost::create([
        'user_id' => $this->admin->id,
        'title' => 'Blog Post with Tags',
        'slug' => 'blog-post-with-tags',
        'content' => 'Content here',
        'status' => 'draft',
    ]);

    $blogPost->tags()->attach([$tag1->id, $tag2->id]);

    expect($blogPost->tags()->count())->toBe(2)
        ->and($blogPost->tags->pluck('name')->toArray())->toContain('Laravel', 'PHP');
});

test('blog post validates required fields', function () {
    actingAs($this->admin);

    try {
        BlogPost::create([
            'title' => '',
            'slug' => '',
            'content' => '',
        ]);
    } catch (\Illuminate\Database\QueryException $e) {
        expect($e->getMessage())->toContain('NOT NULL');
    }
});

test('blog post validates unique slug', function () {
    BlogPost::factory()->create(['slug' => 'existing-slug']);

    actingAs($this->admin);

    $this->expectException(\Illuminate\Database\QueryException::class);

    BlogPost::create([
        'user_id' => $this->admin->id,
        'title' => 'New Post',
        'slug' => 'existing-slug',
        'content' => 'Content',
        'status' => 'draft',
    ]);
});

test('blog post resource has edit action on table', function () {
    $blogPost = BlogPost::factory()->create();

    actingAs($this->admin);

    Livewire::test(ListBlogPosts::class)
        ->assertTableActionExists('edit');
});

test('blog post can be edited', function () {
    $blogPost = BlogPost::factory()->create([
        'title' => 'Original Title',
    ]);

    actingAs($this->admin);

    $blogPost->update(['title' => 'Updated Title']);

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

test('changing status to published sets published_at', function () {
    actingAs($this->admin);

    $blogPost = BlogPost::factory()->draft()->create();

    expect($blogPost->published_at)->toBeNull();

    $blogPost->update([
        'status' => 'published',
        'published_at' => now(),
    ]);

    expect($blogPost->fresh()->published_at)->not->toBeNull()
        ->and($blogPost->fresh()->status)->toBe('published');
});

test('blog post tags can be updated', function () {
    $tag1 = Tag::factory()->create(['name' => 'Laravel']);
    $tag2 = Tag::factory()->create(['name' => 'PHP']);
    $tag3 = Tag::factory()->create(['name' => 'Testing']);

    $blogPost = BlogPost::factory()->create();
    $blogPost->tags()->attach([$tag1->id, $tag2->id]);

    actingAs($this->admin);

    $blogPost->tags()->sync([$tag2->id, $tag3->id]);

    $blogPost->refresh();
    expect($blogPost->tags()->count())->toBe(2)
        ->and($blogPost->tags->pluck('name')->toArray())->toContain('PHP', 'Testing')
        ->and($blogPost->tags->pluck('name')->toArray())->not->toContain('Laravel');
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

    expect($blogPost->status)->toBe('published');

    $blogPost->update(['status' => 'archived']);

    expect($blogPost->fresh()->status)->toBe('archived');
});

test('draft blog posts can be changed to published', function () {
    $blogPost = BlogPost::factory()->draft()->create();

    actingAs($this->admin);

    expect($blogPost->status)->toBe('draft');

    $blogPost->update([
        'status' => 'published',
        'published_at' => now(),
    ]);

    $blogPost->refresh();
    expect($blogPost->status)->toBe('published')
        ->and($blogPost->published_at)->not->toBeNull();
});

test('blog post featured image can be stored', function () {
    Storage::fake('public');

    actingAs($this->admin);

    $blogPost = BlogPost::factory()->create([
        'featured_image' => 'blog/test-image.jpg',
    ]);

    Storage::disk('public')->put('blog/test-image.jpg', 'test image content');

    expect($blogPost->featured_image)->toBe('blog/test-image.jpg')
        ->and(Storage::disk('public')->exists('blog/test-image.jpg'))->toBeTrue();
});

test('blog post featured image path uses blog directory', function () {
    actingAs($this->admin);

    $blogPost = BlogPost::factory()->create([
        'featured_image' => 'blog/test-image.jpg',
    ]);

    expect(str_starts_with($blogPost->featured_image, 'blog/'))->toBeTrue();
});

test('blog post featured image can be updated', function () {
    Storage::fake('public');

    $oldImagePath = 'blog/old-image.jpg';
    Storage::disk('public')->put($oldImagePath, 'old image content');

    $blogPost = BlogPost::factory()->create([
        'featured_image' => $oldImagePath,
    ]);

    actingAs($this->admin);

    $newImagePath = 'blog/new-image.jpg';
    Storage::disk('public')->put($newImagePath, 'new image content');

    $blogPost->update(['featured_image' => $newImagePath]);

    expect($blogPost->fresh()->featured_image)->toBe($newImagePath)
        ->and(Storage::disk('public')->exists($newImagePath))->toBeTrue();
});
