<?php

namespace App\Services;

use App\Http\Controllers\BaseController;
use App\Http\Requests\StorearticleRequest;
use App\Http\Requests\UpdatearticleRequest;
use App\Models\article;
use App\Models\comment;
use App\Repository\Eloquent\ArticleRepository;
use App\Repository\Eloquent\CommentRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ArticleService extends BaseController
{
    private $ArticleRepository;
    private $CommentRepository;
    public function __construct(ArticleRepository $ArticleRepository, CommentRepository $CommentRepository)
    {
        $this->ArticleRepository = $ArticleRepository;
        $this->CommentRepository = $CommentRepository;
    }

    public function getAllArticles() // Only for the manager to view all articles first and then accept or reject them before publishing
    {
        $user = auth('sanctum')->user();
        if (Gate::allows('viewAllArticles', $user)) {
            return $this->sendResponse($this->ArticleRepository->getAll(), 'These are all articles');
        }

        return $this->sendError('you have no permission!', ['error' => 'Unauthorised']);
    }

    public function getPublishedArticles() //Articles after they have been accepted by the manager
    {
        if (Gate::denies('viewAny', 'App\Models\Article')) {
            return $this->sendError('Something is wrong!', ['error' => 'Something is wrong']);
        }
        return $this->sendResponse($this->ArticleRepository->getAllPublishedArticles(), 'These are all Published articles');
    }
    public function createArticle(StorearticleRequest $request)
    {
        $user = auth('sanctum')->user();
        if (Gate::denies('create', 'App\Models\Article')) {
            return $this->sendError('Something is wrong!', ['error' => 'Something is wrong']);
        }
        $input = $request->all();
        $input['user_id'] = $user->id;
        $artical = $this->ArticleRepository->create($input);
        return $this->sendResponse($artical, 'successfully created');
    }

    public function showArticle($id)
    {
        if (Gate::denies('viewAny', 'App\Models\Article')) {
            return $this->sendError('Something is wrong!', ['error' => 'Something is wrong']);
        }
        return $this->sendResponse($this->ArticleRepository->showArticleText($id), 'this is the article');
    }

    public function updateArticle(UpdatearticleRequest $request, $id)
    {
        $article = $this->ArticleRepository
            ->where('user_id', auth('sanctum')->user()->id)
            ->find($id);

        if (!$article) {
            return $this->sendError('you have no permission!', ['error' => 'Unauthorised']);
        }

        $article->fill($request->only(['title', 'text']));
        $article->save();

        return $this->sendResponse($article, 'Updated deone');
    }

    public function deleteArticle($id)
    {
        $article = $this->ArticleRepository->find($id);

        if (!$article) {
            return response()->json(['error' => 'Article not found'], 404);
        }

        // Check authorization using Policies
        if (Gate::denies('delete', $article)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $article->delete();

        return response()->json(['message' => 'Article deleted successfully']);
    }

    public function acceptArticle($id, Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'approval' => ['required', 'numeric', 'in:0,1'],
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        }

        $user = auth('sanctum')->user();
        $article = $this->ArticleRepository->find($id);

        if (Gate::allows('acceptArticle', $user)) {
            $article->approval = $request->approval;
            $article->save();
            if ($request->approval == true) {
                return $this->sendResponse($article, 'accepted done');
            } else {
                return $this->sendResponse($article, 'rejected done');
            }
        }

        return $this->sendError('you have no permission!', ['error' => 'Unauthorised']);
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $articlesSeaechResults = Article::searchArticles($query);

        return $this->sendResponse($articlesSeaechResults, 'these are articles Seaech Results');
    }

    public function addComment(Request $request, $id)
    {
        $user = auth('sanctum')->user();
        $article = $this->ArticleRepository->find($id);
        $input = $request->all();
        $input['user_id'] = $user->id;
        $input['article_id'] = $article->id;
        $comment = $this->CommentRepository->create($input);
        return $this->sendResponse($comment, 'successfully added a comment');
    }

    public function getCommentsForAnArticle($id)
    {
        $comments = $this->CommentRepository->where('article_id',$id);
        return $this->sendResponse($comments, 'These are all comments for this article.');
    }
}
