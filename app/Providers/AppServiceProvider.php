<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Request as RequestFacade;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use App\Models\Notes;

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

        if ($headers->has('x-forwarded-proto') && $headers->get('x-forwarded-proto') === 'https') {
            // HTTPS via forwarded header
            $scheme = $headers->get('x-forwarded-proto');
            $host = $headers->get('x-forwarded-host');
        } else {
            // Local HTTP
            $scheme = 'http';
            $host = '192.168.110.7'; 
        }

        $root = $scheme . '://' . $host . '/maif';
        URL::forceRootUrl($root);
        URL::forceScheme($scheme);

        // -------------------------
        // Share default description (avoids undefined variable)
        // -------------------------
        View::share([
            'description' => null,
            'id' => null,
            'joinedData' => null,
            'notes' => null,
        ]);

        // -------------------------
        // View composer for navbar
        // -------------------------
        View::composer([
            'layouts.partials._navbar',
            'layouts.partials._sidebar',
        ], function ($view) {

            $description = null;
            $notes = null;
            $joinedData = null;

            if (Auth::check()) {
                $id = Auth::user()->userid;

                $joinedData = DB::connection('dohdtr')
                    ->table('users')
                    ->leftJoin('dts.users', 'users.userid', '=', 'dts.users.username')
                    ->where('users.userid', $id)
                    ->select('users.section', 'users.division')
                    ->first();

                $notes = Notes::where('created_by', $id)->with('user')->get();

                if ($joinedData) {
                    $description = DB::connection('dts')
                        ->table('section')
                        ->where('id', $joinedData->section)
                        ->where('division', $joinedData->division)
                        ->value('description');
                }
            }

            $view->with([
                'description' => $description,
                'id' => $id,
                'joinedData' => $joinedData,
                'notes' => $notes
            ]);
        });
    }
}
