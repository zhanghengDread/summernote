<?php

namespace ZhanghengDread\Summernote;

use Encore\Admin\Admin;
use Encore\Admin\Form;
use Illuminate\Support\ServiceProvider;

class SummernoteServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot(Summernote1 $extension)
    {
        if (! Summernote1::boot()) {
            return ;
        }

        if ($views = $extension->views()) {
            $this->loadViewsFrom($views, 'laravel-admin-summernote');
        }

        if ($this->app->runningInConsole() && $assets = $extension->assets()) {
            $this->publishes(
                [$assets => public_path('vendor/laravel-admin-ext/summernote')],
                'laravel-admin-summernote'
            );
        }

        Admin::booting(function () {
            $name = Summernote1::config('field_name', 'summernote1');
            Form::extend($name, Editor::class);
        });

        Admin::booted(function () {
            if ($lang = Summernote1::config('config.lang')) {
                Admin::js("vendor/laravel-admin-ext/summernote/dist/lang/summernote-{$lang}.js");
            }
        });
    }
}