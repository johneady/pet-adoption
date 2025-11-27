<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class BlogPostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = Tag::all();

        $users = User::factory()->count(20)->withProfilePicture()->create();

        BlogPost::factory(10)->published()->create(['user_id' => fn () => $users->random()->id])->each(function (BlogPost $post) use ($tags) {
            $post->tags()->attach(
                $tags->random(rand(1, 3))->pluck('id')->toArray()
            );
        });

        BlogPost::factory(5)->draft()->create(['user_id' => fn () => $users->random()->id])->each(function (BlogPost $post) use ($tags) {
            $post->tags()->attach(
                $tags->random(rand(1, 3))->pluck('id')->toArray()
            );
        });

        BlogPost::factory(5)->archived()->create(['user_id' => fn () => $users->random()->id])->each(function (BlogPost $post) use ($tags) {
            $post->tags()->attach(
                $tags->random(rand(1, 3))->pluck('id')->toArray()
            );
        });
    }
}
