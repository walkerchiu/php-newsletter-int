<?php

namespace WalkerChiu\Newsletter;

use Illuminate\Support\ServiceProvider;

class NewsletterServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfig();
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Publish config files
        $this->publishes([
           __DIR__ .'/config/newsletter.php' => config_path('wk-newsletter.php'),
        ], 'config');

        // Publish migration files
        $from = __DIR__ .'/database/migrations/';
        $to   = database_path('migrations') .'/';
        $this->publishes([
            $from .'create_wk_newsletter_table.php'
                => $to .date('Y_m_d_His', time()) .'_create_wk_newsletter_table.php'
        ], 'migrations');

        $this->loadViewsFrom(__DIR__.'/views', 'php-newsletter');
        $this->publishes([
           __DIR__.'/views' => resource_path('views/vendor/php-newsletter'),
        ]);

        $this->loadTranslationsFrom(__DIR__.'/translations', 'php-newsletter');
        $this->publishes([
            __DIR__.'/translations' => resource_path('lang/vendor/php-newsletter'),
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                config('wk-newsletter.command.cleaner')
            ]);
        }

        config('wk-core.class.newsletter.setting')::observe(config('wk-core.class.newsletter.settingObserver'));
        config('wk-core.class.newsletter.settingLang')::observe(config('wk-core.class.newsletter.settingLangObserver'));
        config('wk-core.class.newsletter.article')::observe(config('wk-core.class.newsletter.articleObserver'));
    }

    /**
     * Register the blade directives
     *
     * @return void
     */
    private function bladeDirectives()
    {
    }

    /**
     * Merges user's and package's configs.
     *
     * @return void
     */
    private function mergeConfig()
    {
        if (!config()->has('wk-newsletter')) {
            $this->mergeConfigFrom(
                __DIR__ .'/config/newsletter.php', 'wk-newsletter'
            );
        }

        $this->mergeConfigFrom(
            __DIR__ .'/config/newsletter.php', 'newsletter'
        );
    }

    /**
     * Merge the given configuration with the existing configuration.
     *
     * @param String  $path
     * @param String  $key
     * @return void
     */
    protected function mergeConfigFrom($path, $key)
    {
        if (
            !(
                $this->app instanceof CachesConfiguration
                && $this->app->configurationIsCached()
            )
        ) {
            $config = $this->app->make('config');
            $content = $config->get($key, []);

            $config->set($key, array_merge(
                require $path, $content
            ));
        }
    }
}
