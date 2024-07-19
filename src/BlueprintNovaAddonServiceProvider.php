<?php

namespace Marv311\BlueprintNovaAddon;

use Blueprint\Blueprint;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Marv311\BlueprintNovaAddon\Tasks\AddIdentifierField;
use Marv311\BlueprintNovaAddon\Tasks\AddRegularFields;
use Marv311\BlueprintNovaAddon\Tasks\AddRelationshipFields;
use Marv311\BlueprintNovaAddon\Tasks\AddTimestampFields;

class BlueprintNovaAddonServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                dirname(__DIR__).'/config/nova_blueprint.php' => config_path('nova_blueprint.php'),
            ], 'nova_blueprint');
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(
            dirname(__DIR__).'/config/nova_blueprint.php',
            'blueprint-nova-config'
        );

        $this->app->singleton(NovaGenerator::class, function ($app) {
            $generator = new NovaGenerator($app['files']);

            $generator->registerTask(new AddIdentifierField());
            $generator->registerTask(new AddRegularFields());
            $generator->registerTask(new AddRelationshipFields());
            $generator->registerTask(new AddTimestampFields());

            return $generator;
        });

        $this->app->extend(Blueprint::class, function ($blueprint, $app) {
            $blueprint->registerGenerator($app[NovaGenerator::class]);

            return $blueprint;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'command.blueprint.build',
            NovaGenerator::class,
            Blueprint::class,
        ];
    }
}
