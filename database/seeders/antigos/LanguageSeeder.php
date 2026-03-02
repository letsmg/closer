<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
 use App\Models\Language;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
   

    public function run()
    {
        $languages = [
            ['codigo' => 'pt', 'nome' => 'Português'],
            ['codigo' => 'en', 'nome' => 'English'],
            ['codigo' => 'es', 'nome' => 'Español'],
            ['codigo' => 'fr', 'nome' => 'Français'],
            ['codigo' => 'de', 'nome' => 'Deutsch'],
            ['codigo' => 'it', 'nome' => 'Italiano'],
            ['codigo' => 'ja', 'nome' => '日本語'],
            ['codigo' => 'ko', 'nome' => '한국어'],
            ['codigo' => 'zh', 'nome' => '中文'],
        ];

        foreach ($languages as $lang) {
            Language::create($lang);
        }
    }
}
