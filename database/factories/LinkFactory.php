<?php

namespace Database\Factories;

use App\Http\BocUrl;
use App\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Link>
 */
class LinkFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->randomElement(array_column(BocUrl::cases(), 'name'));
        $url = BocUrl::{$name}->value;
        $url = str_replace('{year}', fake()->year(), $url);
        $url = str_replace('{bulletin}', fake()->numerify(), $url);
        $url = str_replace('{page}', fake()->numerify(), $url);

        return [
            'url' => $url,
            'page_id' => Page::factory(),
            'created_at' => \Carbon\Carbon::now(),
        ];
    }
}
