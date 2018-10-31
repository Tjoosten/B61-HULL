<?php

namespace App\Composers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\View\View;

class AccountComposer
{
    /**
     * The Guard implementation.
     *
     * @var \Illuminate\Contracts\Auth\Guard
     */
    protected $auth;

    /**
     * AccountComposer constructor
     *
     * @param  \Illuminate\Contracts\Auth\Guard $auth
     * @return void
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view): void
    {
        if ($this->auth->check()) {
            $view->with('authUser', $this->auth->user());
        }
    }
}