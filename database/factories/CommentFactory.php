<?php

namespace Database\Factories;

use App\Models\article;
use App\Models\comment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\comment>
 */
class CommentFactory extends Factory
{
    protected $model = comment::class;

    public function definition(): array
    {
        return [
            'text' => $this->faker->paragraph,
            'user_id' => User::all()->random()->id,
            'article_id' => article::all()->random()->id,
        ];
    }
}
