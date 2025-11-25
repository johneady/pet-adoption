<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky stashable class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

            <a href="{{ route('dashboard') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
                <x-app-logo />
            </a>

            <flux:navlist variant="outline">
                <flux:navlist.group :heading="__('Platform')" class="grid">
                    <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>{{ __('Dashboard') }}</flux:navlist.item>
                    <flux:navlist.item icon="heart" :href="route('pets.index')" :current="request()->routeIs('pets.*')" wire:navigate>{{ __('Adopt a Pet') }}</flux:navlist.item>
                </flux:navlist.group>
            </flux:navlist>

            <flux:spacer />

            <flux:navlist variant="outline">
                <flux:navlist.item icon="folder-git-2" href="https://github.com/laravel/livewire-starter-kit" target="_blank">
                {{ __('Repository') }}
                </flux:navlist.item>

                <flux:navlist.item icon="book-open-text" href="https://laravel.com/docs/starter-kits#livewire" target="_blank">
                {{ __('Documentation') }}
                </flux:navlist.item>
            </flux:navlist>

            <!-- Desktop User Menu -->
            @auth
                <flux:dropdown class="hidden lg:block" position="bottom" align="start">
                    <flux:profile
                        :name="auth()->user()->name"
                        :initials="auth()->user()->initials()"
                        icon:trailing="chevrons-up-down"
                    />

                    <flux:menu class="w-[220px]">
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
                            @if (auth()->user()->is_admin)
                                <flux:menu.item href="/admin" icon="cog">{{ __('Admin') }}</flux:menu.item>
                            @endif
                            <flux:menu.item :href="route('profile.edit')" icon="user" wire:navigate>{{ __('Profile') }}</flux:menu.item>
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
                <div class="hidden lg:flex gap-2">
                    <flux:button href="{{ route('login') }}" variant="ghost" size="sm">{{ __('Sign In') }}</flux:button>
                    <flux:button href="{{ route('register') }}" variant="primary" size="sm">{{ __('Sign Up') }}</flux:button>
                </div>
            @endauth
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            @auth
                <flux:dropdown position="top" align="end">
                    <button type="button" class="focus:outline-none focus:ring-2 focus:ring-ocean-500 focus:ring-offset-2 rounded-full">
                        <img src="{{ auth()->user()->profile_picture ? auth()->user()->profilePictureUrl() : url('/images/default-avatar.svg') }}" alt="{{ auth()->user()->name }}" class="size-8 cursor-pointer rounded-full object-cover">
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
                            @if (auth()->user()->is_admin)
                                <flux:menu.item href="/admin" icon="cog">{{ __('Admin') }}</flux:menu.item>
                            @endif
                            <flux:menu.item :href="route('profile.edit')" icon="user" wire:navigate>{{ __('Profile') }}</flux:menu.item>
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
                    <flux:button href="{{ route('register') }}" variant="primary" size="sm">{{ __('Sign Up') }}</flux:button>
                </div>
            @endauth
        </flux:header>

        {{ $slot }}

        @fluxScripts
    </body>
</html>
