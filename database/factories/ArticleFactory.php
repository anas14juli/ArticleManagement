<?php

namespace Database\Factories;

use App\Models\article;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Generator as Faker;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\article>
 */
class ArticleFactory extends Factory
{
    protected $model = article::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence,
            'text' => $this->faker->paragraph,
            'approval' => $this->faker->boolean,
            'user_id' => User::all()->random()->id,
        ];
    }
}
