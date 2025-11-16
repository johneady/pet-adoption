<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Configure mail settings from database
        $this->configureMailSettings();
    }

    /**
     * Configure mail from address, name, and reply-to from settings table.
     */
    protected function configureMailSettings(): void
    {
        try {
            $fromAddress = Setting::get('mail_from_address', config('mail.from.address'));
            $fromName = Setting::get('mail_from_name', config('mail.from.name'));
            $replyTo = Setting::get('mail_reply_to');

            Config::set('mail.from.address', $fromAddress);
            Config::set('mail.from.name', $fromName);

            if ($replyTo) {
                Config::set('mail.reply_to.address', $replyTo);
            }
        } catch (\Exception $e) {
            // Silently fail if settings table doesn't exist yet (e.g., during migrations or tests)
            // The config will fall back to the default values from config/mail.php
        }
    }
}
