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
        $foundIn = fake()->randomElement([
            BocUrl::Archive,
            BocUrl::YearIndex,
            BocUrl::BulletinIndex,
        ]);

        $page = Page::factory()->create([
            'name' => $foundIn->name,
            'url' => $foundIn->value,
        ]);

        $url = BocUrl::{$foundIn->name}->contains()?->value;
        $url = str_replace('{year}', fake()->year(), $url);
        $url = str_replace('{bulletin}', fake()->numerify(), $url);
        $url = str_replace('{article}', fake()->numerify(), $url);

        return [
            'url' => $url,
            'page_id' => $page->id,
            'created_at' => \Carbon\Carbon::now(),
        ];
    }

    /**
     * Indicate when the link download started.
     */
    public function downloadStarted(): static
    {
        return $this->state(fn (array $attributes) => [
            'download_started_at' => \Carbon\Carbon::now(),
        ]);
    }

    /**
     * Indicate that the link has been disallowed by a robots.txt rule.
     */
    public function disallowed(): static
    {
        return $this->state(fn (array $attributes) => [
            'disallowed_at' => \Carbon\Carbon::now(),
        ]);
    }
}
