<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Request as RequestFacade;
use Illuminate\Support\Facades\URL;

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
        //

        $headers = request()->headers;

        // if ($headers->has('x-forwarded-proto') && $headers->get('x-forwarded-proto') === 'https') {
        //     // HTTPS via forwarded header
        //     $scheme = $headers->get('x-forwarded-proto');
        //     $host = $headers->get('x-forwarded-host');
        // } else {
        //     // Local HTTP
            $scheme = 'http';
            $host = 'localhost'; 
        // }

        $root = $scheme . '://' . $host . '/maif';
        URL::forceRootUrl($root);
        URL::forceScheme($scheme);
    }
}
