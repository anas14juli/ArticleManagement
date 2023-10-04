<?php

namespace App\Repository\Eloquent;

use App\Models\article;
use App\Repository\ArticleRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ArticleRepository extends BaseRepository implements ArticleRepositoryInterface
{

    public function __construct(article $model)
    {
        parent::__construct($model);
    }

    public function getAll()
    {
        return article::all();
    }

    public function getAllPublishedArticles()
    {
        return article::where('approval', true)->get();
    }

    public function showArticleText($id)
    {
        $result = Article::find($id)
            ->with('users')
            ->first();

        return $data = [
            'title' => $result->title,
            'text' => $result->text,
            'name' => $result->users ? $result->users->name : null
        ];
    }
}
