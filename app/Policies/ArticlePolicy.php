<?php

namespace App\Policies;

use App\Models\User;
use App\Models\article;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class ArticlePolicy
{
    use HandlesAuthorization;

    public function before(User $user)
    {
        if ($user->role === 'Manager') {
            return true;
        }
    }

    public function acceptArticle(User $user)
    {
        return $user->role === 'Manager';
    }

    public function viewAllArticles(User $user)
    {
        return $user->role === 'Manager';
    }

    public function updateMyAricle(User $user, Article $article)
    {
        return $user->id === $article->user_id;
    }

    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, Article $article)
    {
        return true;
    }

    public function create(User $user)
    {
        return true;
    }

    public function update(User $user, Article $article)
    {
        return true;
    }

    public function delete(User $user, Article $article)
    {
        if ($user->role === 'manager') {
            return true;
        }

        return Response::deny('You are not authorized to delete this article.');
    }
}
