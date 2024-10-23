<?php

namespace Database\Factories;

use App\Http\BocUrl;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Page>
 */
class PageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->randomElement([
            BocUrl::Archive,
            BocUrl::YearIndex,
            BocUrl::BulletinIndex,
        ]);

        return [
            'name' => $name->name,
            'url' => $name->value,
            'content' => fake()->randomHtml(),
            'created_at' => \Carbon\Carbon::now(),
        ];
    }
}
