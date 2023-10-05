<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\NewArticleNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;


class SendArticleNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public function __construct()
    {
        //
    }


    public function handle(): void
    {
        $article = [
            'id' => 1,
            'title' => 'A new article needs revision',
            'text' => 'this is the content.'
        ];

        $admins = User::where('role', 'Manager')->get();

        Notification::send($admins, new NewArticleNotification($article['title'], $article['text']));
    }
}
