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
        $name = fake()->randomElement(array_column(BocUrl::cases(), 'name'));

        return [
            'name' => fake()->randomElement(array_column(BocUrl::cases(), 'name')),
            'url' => BocUrl::{$name}->value,
            'content' => '<html>'.fake()->paragraph().'</html>',
            'created_at' => \Carbon\Carbon::now(),
        ];
    }
}