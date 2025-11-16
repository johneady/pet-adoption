<?php

declare(strict_types=1);

use App\Livewire\Blog\Index;
use App\Livewire\Blog\Show;
use App\Models\BlogPost;
use App\Models\Tag;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\get;

// Blog Index Tests

test('can visit blog index page', function () {
    $response = get(route('blog.index'));

    $response->assertSuccessful();
    $response->assertSee('Blog');
});

test('displays published blog posts', function () {
    $user = User::factory()->create();
    $posts = BlogPost::factory()->count(3)->published()->create([
        'user_id' => $user->id,
    ]);

    Livewire::test(Index::class)
        ->assertSee($posts[0]->title)
        ->assertSee($posts[1]->title)
        ->assertSee($posts[2]->title);
});

test('does not display draft blog posts', function () {
    $user = User::factory()->create();
    $publishedPost = BlogPost::factory()->published()->create([
        'user_id' => $user->id,
        'title' => 'Published Post',
    ]);
    $draftPost = BlogPost::factory()->draft()->create([
        'user_id' => $user->id,
        'title' => 'Draft Post',
    ]);

    Livewire::test(Index::class)
        ->assertSee('Published Post')
        ->assertDontSee('Draft Post');
});

test('does not display archived blog posts', function () {
    $user = User::factory()->create();
    $publishedPost = BlogPost::factory()->published()->create([
        'user_id' => $user->id,
        'title' => 'Published Post',
    ]);
    $archivedPost = BlogPost::factory()->archived()->create([
        'user_id' => $user->id,
        'title' => 'Archived Post',
    ]);

    Livewire::test(Index::class)
        ->assertSee('Published Post')
        ->assertDontSee('Archived Post');
});

test('can search blog posts by title', function () {
    $user = User::factory()->create();
    $post1 = BlogPost::factory()->published()->create([
        'user_id' => $user->id,
        'title' => 'Laravel Tips and Tricks',
    ]);
    $post2 = BlogPost::factory()->published()->create([
        'user_id' => $user->id,
        'title' => 'PHP Best Practices',
    ]);

    Livewire::test(Index::class)
        ->set('search', 'Laravel')
        ->assertSee('Laravel Tips and Tricks')
        ->assertDontSee('PHP Best Practices');
});

test('can search blog posts by excerpt', function () {
    $user = User::factory()->create();
    $post1 = BlogPost::factory()->published()->create([
        'user_id' => $user->id,
        'title' => 'First Post',
        'excerpt' => 'This post is about Laravel framework',
    ]);
    $post2 = BlogPost::factory()->published()->create([
        'user_id' => $user->id,
        'title' => 'Second Post',
        'excerpt' => 'This post is about PHP development',
    ]);

    Livewire::test(Index::class)
        ->set('search', 'Laravel framework')
        ->assertSee('First Post')
        ->assertDontSee('Second Post');
});

test('can search blog posts by content', function () {
    $user = User::factory()->create();
    $post1 = BlogPost::factory()->published()->create([
        'user_id' => $user->id,
        'title' => 'First Post',
        'content' => 'This content mentions Livewire extensively',
    ]);
    $post2 = BlogPost::factory()->published()->create([
        'user_id' => $user->id,
        'title' => 'Second Post',
        'content' => 'This content is about databases',
    ]);

    Livewire::test(Index::class)
        ->set('search', 'Livewire')
        ->assertSee('First Post')
        ->assertDontSee('Second Post');
});

test('can filter blog posts by tag', function () {
    $user = User::factory()->create();
    $tag1 = Tag::factory()->create(['name' => 'Laravel']);
    $tag2 = Tag::factory()->create(['name' => 'PHP']);

    $post1 = BlogPost::factory()->published()->create([
        'user_id' => $user->id,
        'title' => 'Laravel Post',
    ]);
    $post1->tags()->attach($tag1);

    $post2 = BlogPost::factory()->published()->create([
        'user_id' => $user->id,
        'title' => 'PHP Post',
    ]);
    $post2->tags()->attach($tag2);

    Livewire::test(Index::class)
        ->set('tagId', $tag1->id)
        ->assertSee('Laravel Post')
        ->assertDontSee('PHP Post');
});

test('can clear all filters', function () {
    $user = User::factory()->create();
    $tag = Tag::factory()->create(['name' => 'Laravel']);
    BlogPost::factory()->published()->create([
        'user_id' => $user->id,
        'title' => 'Test Post',
    ]);

    Livewire::test(Index::class)
        ->set('search', 'test')
        ->set('tagId', $tag->id)
        ->call('clearFilters')
        ->assertSet('search', '')
        ->assertSet('tagId', null);
});

test('resets page when search changes', function () {
    $user = User::factory()->create();
    BlogPost::factory()->count(15)->published()->create([
        'user_id' => $user->id,
    ]);

    Livewire::test(Index::class)
        ->set('search', 'test')
        ->assertSet('search', 'test');
});

test('resets page when tag filter changes', function () {
    $user = User::factory()->create();
    $tag = Tag::factory()->create(['name' => 'Laravel']);
    BlogPost::factory()->count(15)->published()->create([
        'user_id' => $user->id,
    ]);

    Livewire::test(Index::class)
        ->set('tagId', $tag->id)
        ->assertSet('tagId', $tag->id);
});

test('displays author name on blog posts', function () {
    $user = User::factory()->create(['name' => 'John Doe']);
    $post = BlogPost::factory()->published()->create([
        'user_id' => $user->id,
    ]);

    Livewire::test(Index::class)
        ->assertSee('John Doe');
});

test('displays published date on blog posts', function () {
    $user = User::factory()->create();
    $publishedAt = now()->subDays(5);
    $post = BlogPost::factory()->published()->create([
        'user_id' => $user->id,
        'published_at' => $publishedAt,
    ]);

    Livewire::test(Index::class)
        ->assertSee($publishedAt->format('M j, Y'));
});

// Blog Show Tests

test('can visit blog post detail page', function () {
    $user = User::factory()->create();
    $post = BlogPost::factory()->published()->create([
        'user_id' => $user->id,
        'slug' => 'test-post',
        'title' => 'Test Post',
    ]);

    $response = get(route('blog.show', $post->slug));

    $response->assertSuccessful();
    $response->assertSee('Test Post');
});

test('displays blog post information', function () {
    $user = User::factory()->create(['name' => 'Jane Doe']);
    $post = BlogPost::factory()->published()->create([
        'user_id' => $user->id,
        'slug' => 'test-post',
        'title' => 'Test Post Title',
        'excerpt' => 'This is an excerpt',
        'content' => 'This is the main content',
    ]);

    Livewire::test(Show::class, ['slug' => $post->slug])
        ->assertSee('Test Post Title')
        ->assertSee('This is an excerpt')
        ->assertSee('This is the main content')
        ->assertSee('Jane Doe')
        ->assertSee($post->published_at->format('M j, Y'));
});

test('displays blog post tags', function () {
    $user = User::factory()->create();
    $post = BlogPost::factory()->published()->create([
        'user_id' => $user->id,
        'slug' => 'test-post',
    ]);

    $tag1 = Tag::factory()->create(['name' => 'Laravel']);
    $tag2 = Tag::factory()->create(['name' => 'PHP']);
    $post->tags()->attach([$tag1->id, $tag2->id]);

    Livewire::test(Show::class, ['slug' => $post->slug])
        ->assertSee('Laravel')
        ->assertSee('PHP');
});

test('displays breadcrumb navigation on blog post', function () {
    $user = User::factory()->create();
    $post = BlogPost::factory()->published()->create([
        'user_id' => $user->id,
        'slug' => 'test-post',
        'title' => 'Test Post',
    ]);

    Livewire::test(Show::class, ['slug' => $post->slug])
        ->assertSee('Home')
        ->assertSee('Blog')
        ->assertSee('Test Post');
});

test('returns 404 for non-existent blog post', function () {
    get(route('blog.show', 'non-existent-slug'))
        ->assertNotFound();
});

test('returns 404 for draft blog post', function () {
    $user = User::factory()->create();
    $draftPost = BlogPost::factory()->draft()->create([
        'user_id' => $user->id,
        'slug' => 'draft-post',
    ]);

    get(route('blog.show', 'draft-post'))
        ->assertNotFound();
});

test('returns 404 for archived blog post', function () {
    $user = User::factory()->create();
    $archivedPost = BlogPost::factory()->archived()->create([
        'user_id' => $user->id,
        'slug' => 'archived-post',
    ]);

    get(route('blog.show', 'archived-post'))
        ->assertNotFound();
});
