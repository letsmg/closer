<?php

namespace Database\Factories;

use App\Models\Language;
use Illuminate\Database\Eloquent\Factories\Factory;

class LanguageFactory extends Factory
{
    protected $model = Language::class;

    public function definition()
    {
        $languages = [
            'English' => 'en',
            'Spanish' => 'es',
            'French' => 'fr',
            'German' => 'de',
            'Italian' => 'it',
            'Portuguese' => 'pt',
            'Russian' => 'ru',
            'Chinese' => 'zh',
            'Japanese' => 'ja',
            'Korean' => 'ko',
            'Arabic' => 'ar',
            'Hindi' => 'hi',
            'Dutch' => 'nl',
            'Swedish' => 'sv',
            'Norwegian' => 'no',
            'Danish' => 'da',
            'Finnish' => 'fi',
            'Polish' => 'pl',
            'Turkish' => 'tr',
            'Greek' => 'el',
        ];

        $languageName = $this->faker->unique()->randomElement(array_keys($languages));
        
        return [
            'name' => $languageName,
            'code' => $languages[$languageName],
            'active' => true,
        ];
    }

    /**
     * Create an active language
     */
    public function active()
    {
        return $this->state(fn (array $attributes) => [
            'active' => true,
        ]);
    }

    /**
     * Create an inactive language
     */
    public function inactive()
    {
        return $this->state(fn (array $attributes) => [
            'active' => false,
        ]);
    }

    /**
     * Create English
     */
    public function english()
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'English',
            'code' => 'en',
            'active' => true,
        ]);
    }

    /**
     * Create Spanish
     */
    public function spanish()
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Spanish',
            'code' => 'es',
            'active' => true,
        ]);
    }

    /**
     * Create French
     */
    public function french()
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'French',
            'code' => 'fr',
            'active' => true,
        ]);
    }

    /**
     * Create German
     */
    public function german()
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'German',
            'code' => 'de',
            'active' => true,
        ]);
    }

    /**
     * Create Italian
     */
    public function italian()
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Italian',
            'code' => 'it',
            'active' => true,
        ]);
    }

    /**
     * Create Portuguese
     */
    public function portuguese()
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Portuguese',
            'code' => 'pt',
            'active' => true,
        ]);
    }

    /**
     * Create Russian
     */
    public function russian()
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Russian',
            'code' => 'ru',
            'active' => true,
        ]);
    }

    /**
     * Create Chinese
     */
    public function chinese()
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Chinese',
            'code' => 'zh',
            'active' => true,
        ]);
    }

    /**
     * Create Japanese
     */
    public function japanese()
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Japanese',
            'code' => 'ja',
            'active' => true,
        ]);
    }

    /**
     * Create Korean
     */
    public function korean()
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Korean',
            'code' => 'ko',
            'active' => true,
        ]);
    }

    /**
     * Create Arabic
     */
    public function arabic()
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Arabic',
            'code' => 'ar',
            'active' => true,
        ]);
    }

    /**
     * Create Hindi
     */
    public function hindi()
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Hindi',
            'code' => 'hi',
            'active' => true,
        ]);
    }

    /**
     * Create Dutch
     */
    public function dutch()
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Dutch',
            'code' => 'nl',
            'active' => true,
        ]);
    }

    /**
     * Create Swedish
     */
    public function swedish()
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Swedish',
            'code' => 'sv',
            'active' => true,
        ]);
    }

    /**
     * Create Norwegian
     */
    public function norwegian()
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Norwegian',
            'code' => 'no',
            'active' => true,
        ]);
    }

    /**
     * Create Danish
     */
    public function danish()
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Danish',
            'code' => 'da',
            'active' => true,
        ]);
    }

    /**
     * Create Finnish
     */
    public function finnish()
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Finnish',
            'code' => 'fi',
            'active' => true,
        ]);
    }

    /**
     * Create Polish
     */
    public function polish()
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Polish',
            'code' => 'pl',
            'active' => true,
        ]);
    }

    /**
     * Create Turkish
     */
    public function turkish()
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Turkish',
            'code' => 'tr',
            'active' => true,
        ]);
    }

    /**
     * Create Greek
     */
    public function greek()
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Greek',
            'code' => 'el',
            'active' => true,
        ]);
    }

    /**
     * Create with specific name and code
     */
    public function withNameAndCode(string $name, string $code)
    {
        return $this->state(fn (array $attributes) => [
            'name' => $name,
            'code' => $code,
        ]);
    }

    /**
     * Create with specific name
     */
    public function name(string $name)
    {
        return $this->state(fn (array $attributes) => [
            'name' => $name,
        ]);
    }

    /**
     * Create with specific code
     */
    public function code(string $code)
    {
        return $this->state(fn (array $attributes) => [
            'code' => $code,
        ]);
    }
}
