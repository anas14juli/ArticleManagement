<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\article;
use App\Models\comment;
use App\Policies\ArticlePolicy;
use Egulias\EmailValidator\Parser\Comment as ParserComment;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        article::class => ArticlePolicy::class,
        comment::class => ParserComment::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
        Gate::define('viewAllArticles', [ArticlePolicy::class, 'viewAllArticles']);
        Gate::define('acceptArticle', [ArticlePolicy::class, 'acceptArticle']);
        Gate::define('updateMyAricle', [ArticlePolicy::class, 'viewAllArticles']);
    }
}
