<?php

namespace Twenty20\Translation\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Twenty20\Translation\Services\TranslationService;

use function Laravel\Prompts\info;
use function Laravel\Prompts\text;

class TranslationCommand extends Command
{
    public $signature = 'translation:sync';

    public $description = 'My command';

    public function handle()
    {
        $translationService = app(TranslationService::class);

        $languages = text(
            label: 'Enter target languages (comma-separated, e.g., fr,es,de):',
            required: true
        );
        $languages = array_map('trim', explode(',', $languages));

        // Ensure the 'en' directory exists
        $enLangPath = lang_path('en');
        if (!File::exists($enLangPath)) {
            File::makeDirectory($enLangPath, 0755, true);
            info('Created "en" language directory.');
        }

        // Ensure the 'en' directory exists
        $enLangPath = lang_path('en');
        if (!File::exists($enLangPath)) {
            File::makeDirectory($enLangPath, 0755, true);
            $this->info('Created "en" language directory.');
        }

        // Get all files in the 'en' directory
        $englishFiles = File::allFiles($enLangPath);

        if (empty($englishFiles)) {
            $this->warn('No translation files found in the "en" directory.');
            return;
        }

        // Process each file in the 'en' directory
        foreach ($englishFiles as $file) {
            $group = $file->getFilenameWithoutExtension(); // Get the group name (e.g., 'auth')
            $translations = require $file->getPathname(); // Load the translations from the file

            // For each target language, create or update the language file
            foreach ($languages as $language) {
                $languageFilePath = lang_path("{$language}/{$group}.php");

                // Initialize the translations array for the target language
                $translatedTranslations = [];

                // Translate each key-value pair
                foreach ($translations as $key => $value) {
                    $translatedText = $translationService->translate($value, $language, 'en');
                    $translatedTranslations[$key] = $translatedText;
                }

                // Ensure the target language directory exists
                if (!File::exists(dirname($languageFilePath))) {
                    File::makeDirectory(dirname($languageFilePath), 0755, true);
                }

                // Write the translated translations to the language file
                $content = "<?php\n\nreturn " . var_export($translatedTranslations, true) . ";\n";
                File::put($languageFilePath, $content);

                $this->info("Created/updated translations for {$language}/{$group}.php");
            }
        }

        $this->info('Translations synced and translated successfully.');
    }
}
