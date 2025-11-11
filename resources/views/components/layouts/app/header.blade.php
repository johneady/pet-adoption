<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">
    <flux:header container class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <a href="{{ route('dashboard') }}" class="ms-2 me-5 flex items-center space-x-2 rtl:space-x-reverse lg:ms-0"
            wire:navigate>
            <x-app-logo />
        </a>

        <flux:navbar class="-mb-px max-lg:hidden">
            @auth
                @if (auth()->user()->is_admin)
                    <flux:navbar.item icon="cog" href="/admin" :current="request()->is('admin*')">
                        {{ __('Admin') }}
                    </flux:navbar.item>
                @endif
                <flux:navbar.item icon="layout-grid" :href="route('dashboard')" :current="request()->routeIs('dashboard')"
                    wire:navigate>
                    {{ __('Dashboard') }}
                </flux:navbar.item>
            @endauth
            <flux:navbar.item icon="heart" :href="route('pets.index')" :current="request()->routeIs('pets.*')"
                wire:navigate>
                {{ __('Adopt a Pet') }}
            </flux:navbar.item>
            <flux:navbar.item icon="document-text" :href="route('blog.index')" :current="request()->routeIs('blog.*')"
                wire:navigate>
                {{ __('Blog') }}
            </flux:navbar.item>
        </flux:navbar>

        <flux:spacer />

        <!-- Desktop User Menu -->
        @auth
            <flux:dropdown position="top" align="end">
                <flux:profile class="cursor-pointer" :initials="auth()->user()->initials()" />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

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
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>{{ __('Settings') }}
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
        class="lg:hidden border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

        <a href="{{ route('dashboard') }}" class="ms-1 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
            <x-app-logo />
        </a>

        <flux:navlist variant="outline">
            <flux:navlist.group :heading="__('Platform')">
                @auth
                    <flux:navlist.item icon="layout-grid" :href="route('dashboard')"
                        :current="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </flux:navlist.item>
                    @if (auth()->user()->is_admin)
                        <flux:navlist.item icon="cog" href="/admin" :current="request()->is('admin*')">
                            {{ __('Admin') }}
                        </flux:navlist.item>
                    @endif
                @endauth
                <flux:navlist.item icon="heart" :href="route('pets.index')" :current="request()->routeIs('pets.*')"
                    wire:navigate>
                    {{ __('Adopt a Pet') }}
                </flux:navlist.item>
                <flux:navlist.item icon="document-text" :href="route('blog.index')" :current="request()->routeIs('blog.*')"
                    wire:navigate>
                    {{ __('Blog') }}
                </flux:navlist.item>
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
