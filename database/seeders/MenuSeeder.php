<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $about = Menu::create([
            'name' => 'About',
            'slug' => 'about',
            'display_order' => 2,
            'is_visible' => true,
            'requires_auth' => false,
        ]);

        // About submenus
        Menu::create([
            'name' => 'Our Mission',
            'slug' => 'about/mission',
            'parent_id' => $about->id,
            'display_order' => 0,
            'is_visible' => true,
            'requires_auth' => false,
        ]);

        Menu::create([
            'name' => 'Our Team',
            'slug' => 'about/team',
            'parent_id' => $about->id,
            'display_order' => 1,
            'is_visible' => true,
            'requires_auth' => false,
        ]);

        Menu::create([
            'name' => 'Success Stories',
            'slug' => 'about/success-stories',
            'parent_id' => $about->id,
            'display_order' => 2,
            'is_visible' => true,
            'requires_auth' => false,
        ]);

        // Member-only menu
        Menu::create([
            'name' => 'My Account',
            'slug' => 'account',
            'display_order' => 5,
            'is_visible' => true,
            'requires_auth' => true,
        ]);
    }
}
