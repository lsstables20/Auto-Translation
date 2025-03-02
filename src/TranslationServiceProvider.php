<?php

namespace Twenty20\Translation;

use Spatie\LaravelPackageTools\Package;
use Twenty20\Translation\Commands\InstallCommand;
use Twenty20\Translation\Commands\TranslationCommand;
use Twenty20\Translation\Services\TranslationService;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class TranslationServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */

        $package
            ->name('translation')
            ->hasConfigFile('translation')
            ->hasMigration('create_translation_table')
            ->hasCommands([
                TranslationCommand::class,
                InstallCommand::class,
            ]);
    }

    public function packageRegistered()
    {
        $this->app->singleton(TranslationService::class, function ($app) {
            $service = config('translation-sync.default_service'); // e.g., 'google'
            $apiKey = config('translation-sync.services.' . $service . '.api_key');
            return new TranslationService($service, $apiKey);
        });
    }

    public function boot()
    {
        parent::boot();

        $this->publishes([
            __DIR__.'/../config/translation.php' => config_path('translation.php'),
        ], 'translation-sync-config');
    }
}
