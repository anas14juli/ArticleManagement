<?php

namespace App\Http\Controllers;

use App\Models\article;
use App\Http\Requests\StorearticleRequest;
use App\Http\Requests\UpdatearticleRequest;
use App\Services\ArticleService;
use Illuminate\Http\Request;

class ArticleController extends BaseController
{
    private $MyServices;
    public function __construct(ArticleService $MyServices)
    {
        $this->MyServices = $MyServices;
    }
    public function index()
    {
        return $this->MyServices->getAllArticles();
    }

    public function store(StorearticleRequest $request)
    {
        return $this->MyServices->createArticle($request);
    }

    public function show($id)
    {
        return $this->MyServices->showArticle($id);
    }

    public function updateArticle(UpdatearticleRequest $request, $id)
    {
        return $this->MyServices->updateArticle($request, $id);
    }

    public function destroy($id)
    {
        return $this->MyServices->deleteArticle($id);
    }

    public function getPublishedArticles()
    {
        return $this->MyServices->getPublishedArticles();
    }

    public function acceptArticle($id, Request $request)
    {
        return $this->MyServices->acceptArticle($id, $request);
    }

    public function search(Request $request)
    {
        return $this->MyServices->search($request);
    }

    public function addComment(Request $request, $id)
    {
        return $this->MyServices->addComment($request, $id);
    }

    public function getCommentsForAnArticle($id)
    {
        return $this->MyServices->getCommentsForAnArticle($id);
    }

    public function getPopularArticle(int $articleId)
    {
        return $this->MyServices->getPopularArticle($articleId);

    }
}
