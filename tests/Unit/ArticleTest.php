<?php

namespace Tests\Unit;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


class ArticleTest extends TestCase
{

    use RefreshDatabase;

    //Testing ,Note: You must fill out data first
    public function testGetAllArticles_Unauthorized()
    {
        $user = User::factory()->create(['role' => 'Manager']);
        $this->actingAs($user);

        $response = $this->json('GET', 'articles');

        $response->assertStatus(403);
    }

    public function testGetAllArticles_Authorized()
    {
        $user = User::factory()->create(['role' => 'Manager']);
        $this->actingAs($user);

        $response = $this->json('GET', '/api/Articles');

        $response->assertStatus(200);
    }

    public function testCreateArticle_InvalidData()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->json('POST', '/api/Articles', [
            'title' => '',
            'text' => 'Short Text',
        ]);

        $response->assertStatus(422);
    }

    public function testCreateArticle_ValidData()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->json('POST', '/api/Articles', [
            'title' => 'Test Title',
            'text' => 'Test Text',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'title',
                'text',
                'user_id',
                'created_at',
                'updated_at',
            ],
        ]);
    }

    public function testShowArticle_Unauthorized()
    {
        $user = User::factory()->create(['role' => 'Manager']);
        $this->actingAs($user);

        $response = $this->json('GET', '/api/Articles/1');

        $response->assertStatus(403);
    }

    public function testUpdateArticle_Unauthorized()
    {
        $user = User::factory()->create(['role' => 'Manager']);
        $this->actingAs($user);

        $response = $this->json('POST', '/api/updateArticle/1', ['title' => 'New Title', 'text' => 'New Text']);

        $response->assertStatus(403);
    }

    public function testDeleteArticle_NotFound()
    {
        $user = User::factory()->create(['role' => 'Manager']);
        $this->actingAs($user);

        $response = $this->json('DELETE', '/api/Articles/1');

        $response->assertStatus(404);
    }

    public function testAcceptArticle_Unauthorized()
    {
        $user = User::factory()->create(['role' => 'Manager']);
        $this->actingAs($user);

        $response = $this->json('POST', '/api/acceptArticle/1', ['approval' => 1]);

        $response->assertStatus(403);
    }

    public function testSearch_NoResults()
    {
        $response = $this->json('POST', '/api/search', ['query' => 'Nonexistent Article']);

        $response->assertStatus(200);
        $response->assertJson([]);
    }

    public function testAddComment_Unauthorized()
    {
        $user = User::factory()->create(['role' => 'manager']);
        $this->actingAs($user);

        $response = $this->json('POST', '/api/addComment/1', ['comment' => 'New Comment']);

        $response->assertStatus(403);
    }

    public function testGetCommentsForAnArticle_NotFound()
    {
        $response = $this->json('GET', '/api/getCommentsForAnArticle/1');

        $response->assertStatus(404);
    }

    public function testGetPopularArticle_NotFound()
    {
        $response = $this->json('GET', '/api/getPopularArticle/1');

        $response->assertStatus(404);
    }
    //End of Testing
}
