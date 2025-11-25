@php
    $dynamicMenus = \App\Models\Menu::with([
        'children' => function ($query) {
            $query->visible();
            if (!auth()->check()) {
                $query->where('requires_auth', false);
            }
        },
        'children.submenuPages' => function ($query) {
            $query->published();
            if (!auth()->check()) {
                $query->where('requires_auth', false);
            }
        },
        'pages' => function ($query) {
            $query->published()->whereNull('submenu_id');
            if (!auth()->check()) {
                $query->where('requires_auth', false);
            }
        },
    ])
        ->whereNull('parent_id')
        ->visible()
        ->when(!auth()->check(), fn($query) => $query->where('requires_auth', false))
        ->get();
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head')
</head>

<body class="flex min-h-screen flex-col bg-white dark:bg-zinc-800">
    <flux:header container
        class="border-b-2 border-ocean-200 bg-gradient-to-r from-ocean-50 to-teal-50 dark:border-ocean-800 dark:from-ocean-950 dark:to-teal-950">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <a href="{{ route('home') }}" class="ms-2 me-5 flex items-center space-x-2 rtl:space-x-reverse lg:ms-0"
            wire:navigate>
            <x-app-logo />
        </a>

        <flux:navbar class="-mb-px max-lg:hidden">
            @auth
                @if (auth()->user()->is_admin)
                    <flux:navbar.item icon="cog" href="/admin" :current="request()->is('admin*')">
                        {{ __('Admin') }}
                    </flux:navbar.item>
                @else
                    <flux:navbar.item icon="layout-grid" :href="route('dashboard')" :current="request()->routeIs('dashboard')"
                        wire:navigate>
                        {{ __('Dashboard') }}
                    </flux:navbar.item>
                @endif
            @endauth
            <flux:navbar.item icon="heart" :href="route('pets.index')" :current="request()->routeIs('pets.*')"
                wire:navigate>
                {{ __('Adopt a Pet') }}
            </flux:navbar.item>
            <flux:navbar.item icon="document-text" :href="route('blog.index')" :current="request()->routeIs('blog.*')"
                wire:navigate>
                {{ __('Blog') }}
            </flux:navbar.item>

            @foreach ($dynamicMenus as $menu)
                @if ($menu->children->isNotEmpty() || $menu->pages->isNotEmpty())
                    <flux:dropdown>
                        <flux:navbar.item>
                            {{ $menu->name }}
                        </flux:navbar.item>

                        <flux:menu>
                            @foreach ($menu->children as $submenu)
                                @if ($submenu->submenuPages->isNotEmpty())
                                    <flux:menu.submenu :heading="$submenu->name">
                                        @foreach ($submenu->submenuPages as $page)
                                            <flux:menu.item :href="route('page.show', $page->slug)" wire:navigate>
                                                {{ $page->title }}
                                            </flux:menu.item>
                                        @endforeach
                                    </flux:menu.submenu>
                                @else
                                    <flux:menu.item disabled>{{ $submenu->name }}</flux:menu.item>
                                @endif
                            @endforeach

                            @if ($menu->children->isNotEmpty() && $menu->pages->isNotEmpty())
                                <flux:menu.separator />
                            @endif

                            @foreach ($menu->pages as $page)
                                <flux:menu.item :href="route('page.show', $page->slug)" wire:navigate>
                                    {{ $page->title }}
                                </flux:menu.item>
                            @endforeach
                        </flux:menu>
                    </flux:dropdown>
                @endif
            @endforeach
            @if (\App\Models\Setting::get('enable_draws', true))
                <flux:navbar.item icon="ticket" :href="route('draws.index')" :current="request()->routeIs('draws.*')"
                    wire:navigate>
                    {{ __('50/50 Draws') }}
                </flux:navbar.item>
            @endif
            @if (\App\Models\Setting::get('enable_memberships', true))
                <flux:navbar.item icon="star" :href="route('membership.plans')" :current="request()->routeIs('membership.*')"
                    wire:navigate>
                    {{ __('Membership') }}
                </flux:navbar.item>
            @endif
        </flux:navbar>

        <flux:spacer />

        <!-- Desktop User Menu -->
        @auth
            <flux:dropdown position="top" align="end">
                <button type="button" class="focus:outline-none focus:ring-2 focus:ring-ocean-500 focus:ring-offset-2 rounded-full">
                    <img src="{{ auth()->user()->profile_picture ? auth()->user()->profilePictureUrl() : url('/images/default-avatar.svg') }}" alt="{{ auth()->user()->name }}" class="size-10 cursor-pointer rounded-full object-cover">
                </button>

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <img src="{{ auth()->user()->profile_picture ? auth()->user()->profilePictureUrl() : url('/images/default-avatar.svg') }}" alt="{{ auth()->user()->name }}" class="size-8 shrink-0 rounded-full object-cover">

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('dashboard')" icon="layout-grid" wire:navigate>{{ __('Dashboard') }}
                        </flux:menu.item>
                        @if (auth()->user()->is_admin)
                            <flux:menu.item href="/admin" icon="cog">{{ __('Admin') }}
                            </flux:menu.item>
                        @endif
                        <flux:menu.item :href="route('profile.edit')" icon="user" wire:navigate>{{ __('Profile') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        @else
            <div class="flex gap-2">
                <flux:button href="{{ route('login') }}" variant="ghost" size="sm">{{ __('Sign In') }}</flux:button>
                <flux:button href="{{ route('register') }}" variant="primary" size="sm">{{ __('Sign Up') }}
                </flux:button>
            </div>
        @endauth
    </flux:header>

    <!-- Mobile Menu -->
    <flux:sidebar stashable sticky
        class="lg:hidden border-e-2 border-ocean-200 bg-gradient-to-br from-ocean-50 to-teal-50 dark:border-ocean-800 dark:from-ocean-950 dark:to-teal-950">
        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

        <a href="{{ route('home') }}" class="ms-1 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
            <x-app-logo />
        </a>

        <flux:navlist variant="outline">
            <flux:navlist.group :heading="__('Platform')">
                @auth
                    @if (auth()->user()->is_admin)
                        <flux:navlist.item icon="cog" href="/admin" :current="request()->is('admin*')">
                            {{ __('Admin') }}
                        </flux:navlist.item>
                    @else
                        <flux:navlist.item icon="layout-grid" :href="route('dashboard')" :current="request()->routeIs('dashboard')"
                            wire:navigate>
                            {{ __('Dashboard') }}
                        </flux:navlist.item>
                    @endif
                @endauth
                <flux:navlist.item icon="heart" :href="route('pets.index')" :current="request()->routeIs('pets.*')"
                    wire:navigate>
                    {{ __('Adopt a Pet') }}
                </flux:navlist.item>
                <flux:navlist.item icon="document-text" :href="route('blog.index')"
                    :current="request()->routeIs('blog.*')" wire:navigate>
                    {{ __('Blog') }}
                </flux:navlist.item>
            </flux:navlist.group>

            @foreach ($dynamicMenus as $menu)
                @if ($menu->children->isNotEmpty() || $menu->pages->isNotEmpty())
                    <flux:navlist.group :heading="$menu->name">
                        @foreach ($menu->children as $submenu)
                            @if ($submenu->submenuPages->isNotEmpty())
                                <flux:navlist.item disabled class="text-xs font-semibold">{{ $submenu->name }}
                                </flux:navlist.item>
                                @foreach ($submenu->submenuPages as $page)
                                    <flux:navlist.item :href="route('page.show', $page->slug)" wire:navigate
                                        class="ps-4">
                                        {{ $page->title }}
                                    </flux:navlist.item>
                                @endforeach
                            @endif
                        @endforeach

                        @foreach ($menu->pages as $page)
                            <flux:navlist.item :href="route('page.show', $page->slug)" wire:navigate>
                                {{ $page->title }}
                            </flux:navlist.item>
                        @endforeach
                    </flux:navlist.group>
                @endif
            @endforeach
            <flux:navlist.group :heading="__('Support')">
                @if (\App\Models\Setting::get('enable_draws', true))
                    <flux:navlist.item icon="ticket" :href="route('draws.index')" :current="request()->routeIs('draws.*')"
                        wire:navigate>
                        {{ __('50/50 Draws') }}
                    </flux:navlist.item>
                @endif
                @if (\App\Models\Setting::get('enable_memberships', true))
                    <flux:navlist.item icon="star" :href="route('membership.plans')" :current="request()->routeIs('membership.*')"
                        wire:navigate>
                        {{ __('Membership') }}
                    </flux:navlist.item>
                @endif
            </flux:navlist.group>
        </flux:navlist>

        <flux:spacer />

        <flux:navlist variant="outline">
            <flux:navlist.item icon="folder-git-2" href="https://github.com/laravel/livewire-starter-kit"
                target="_blank">
                {{ __('Repository') }}
            </flux:navlist.item>

            <flux:navlist.item icon="book-open-text" href="https://laravel.com/docs/starter-kits#livewire"
                target="_blank">
                {{ __('Documentation') }}
            </flux:navlist.item>
        </flux:navlist>
    </flux:sidebar>

    {{ $slot }}

    @fluxScripts
</body>

</html>
