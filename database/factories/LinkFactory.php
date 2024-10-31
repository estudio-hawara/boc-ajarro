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
        return [
            'url' => fake()->url(),
            'page_id' => Page::factory(),
            'created_at' => \Carbon\Carbon::now(),
        ];
    }

    /**
     * Indicate that the link is of a certain type.
     */
    public function ofType(BocUrl $type): static
    {
        return $this->state(function (array $attributes) use ($type) {
            $params = [
                'year' => fake()->year(),
                'bulletin' => fake()->numerify(),
                'article' => fake()->numerify(),
            ];

            $foundIn = $type->foundIn();

            $pageUrl = $foundIn?->value ?? '';
            $pageUrl = str_replace('{year}', $params['year'], $pageUrl);
            $pageUrl = str_replace('{bulletin}', $params['bulletin'], $pageUrl);
            $pageUrl = str_replace('{article}', $params['article'], $pageUrl);

            $page = Page::factory(state: [
                'name' => $foundIn->name,
                'url' => $pageUrl,
            ]);

            $linkUrl = $type?->value ?? '';
            $linkUrl = str_replace('{year}', $params['year'], $linkUrl);
            $linkUrl = str_replace('{bulletin}', $params['bulletin'], $linkUrl);
            $linkUrl = str_replace('{article}', $params['article'], $linkUrl);

            return [
                'type' => $type->name,
                'url' => $linkUrl,
                'page_id' => $page,
            ];
        });
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
