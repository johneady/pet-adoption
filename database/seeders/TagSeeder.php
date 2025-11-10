<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            ['name' => 'News', 'slug' => 'news'],
            ['name' => 'Events', 'slug' => 'events'],
            ['name' => 'Success Stories', 'slug' => 'success-stories'],
            ['name' => 'Featured', 'slug' => 'featured'],
        ];

        foreach ($tags as $tag) {
            Tag::create($tag);
        }
    }
}
