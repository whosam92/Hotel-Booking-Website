<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use App\Models\Page;
use App\Models\Room;
use App\Models\Setting;
use App\Models\Owner; // ✅ Ensure Owner model is imported at the top


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();

        $page_data = Page::where('id',1)->first();
        $room_data = Room::get();
        $setting_data = Setting::where('id',1)->first();

        $this->app->bind('owner', function ($app) {
            return new Owner();
        });

        view()->share('global_page_data', $page_data);
        view()->share('global_room_data', $room_data);
        view()->share('global_setting_data', $setting_data);

    }

}
