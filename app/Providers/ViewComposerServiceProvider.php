<?php

namespace App\Providers;

use App\Composers\AccountComposer;
use Illuminate\Support\ServiceProvider;

/**
 * Class ViewComposerServiceProvider
 *
 * @package App\Providers
 */
class ViewComposerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        view()->composer('*', AccountComposer::class);
    }
}
