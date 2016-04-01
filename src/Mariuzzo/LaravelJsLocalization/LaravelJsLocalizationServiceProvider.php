<?php namespace Mariuzzo\LaravelJsLocalization;

use Illuminate\Support\ServiceProvider;

/**
 * The LaravelJsLocalizationServiceProvider class.
 *
 * @author Rubens Mariuzzo <rubens@mariuzzo.com>
 */
class LaravelJsLocalizationServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;


    public function boot()
    {
        $this->publishes(
            [
                __DIR__ . '/Config/laravel-js-localization.php' => config_path('laravel-js-localization.php'),
            ], 'config'
        );
    }


    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/Config/laravel-js-localization.php', 'laravel-js-localization'
        );

        $this->app['localization.js'] = $this->app->share(
            function ($app) {
                $files = $app['files'];
                $langs = [];
                $lang  = $app['path.base'] . '/resources/lang';

                $add_lang = config("laravel-js-localization.directories", false);

                if (is_array($add_lang)) {
                    $langs = array_merge([], $add_lang, [$lang]);
                }

                $langs = array_unique($langs);

                $generator = new Generators\LangJsGenerator($files, $langs);

                return new Commands\LangJsCommand($generator);
            }
        );

        $this->commands('localization.js');
    }


    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['localization.js'];
    }
}
