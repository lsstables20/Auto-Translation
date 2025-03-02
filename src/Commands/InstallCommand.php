<?php

namespace Twenty20\Translation\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

use function Laravel\Prompts\info;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

class InstallCommand extends Command
{
    protected $signature = 'translation:install';

    protected $description = 'Install the Translation package';

    public function handle()
    {
        // Ask the user which translation service they want to use
        $service = select(
            'Which translation service would you like to use?',
            ['google', 'deepl', 'openai'],
            default: 'google'
        );

        // Ask for the API key
        $apiKey = text(
            label: "Enter your {$service} API key:",
            required: true
        );

        // Update the .env file
        $this->updateEnvFile($service, $apiKey);

        info('Translation package installed successfully.');
    }

    protected function updateEnvFile($service, $apiKey)
    {
        $envPath = base_path('.env');

        // Read the .env file
        $envContents = File::get($envPath);

        // Remove existing keys for the selected service
        $envContents = preg_replace("/^TRANSLATION_SERVICE=.*\n/m", '', $envContents);
        $envContents = preg_replace("/^{$service}_api_key=.*\n/m", '', $envContents);

        // Add new keys
        $envContents .= "\nTRANSLATION_SERVICE={$service}\n";
        $envContents .= strtoupper($service) . "_API_KEY={$apiKey}\n";

        // Write the updated .env file
        File::put($envPath, $envContents);
    }
}
