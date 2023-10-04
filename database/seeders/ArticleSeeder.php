<?php

namespace Database\Seeders;

use App\Models\article;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    public function run()
    {
        article::factory(Article::class, 10)->create();
    }
}
