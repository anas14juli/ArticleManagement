<?php

namespace App\Repository;

interface ArticleRepositoryInterface extends EloquentRepositoryInterface
{
    public function getAllPublishedArticles();
    public function showArticleText(int $id);

}
