<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_create_post()
    {
        $response = $this->postJson('/posts', [
            'title' => 'Test Post',
            'content' => 'Test Content',
        ]);

        $response->assertUnauthorized();
    }

    public function test_user_can_create_post()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson('/posts', [
                'title' => 'Test Post',
                'content' => 'Test Content',
            ]);

        $response->assertCreated();
        $this->assertDatabaseHas('posts', ['title' => 'Test Post']);
    }

    public function test_only_published_posts_are_shown()
    {
        $published = Post::factory()->published()->create();
        $draft = Post::factory()->draft()->create();
        $scheduled = Post::factory()->scheduled()->create();

        $response = $this->getJson('/posts');

        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['id' => $published->id]);
    }

    public function test_only_owner_can_update_post()
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($otherUser)
            ->putJson("/posts/{$post->id}", [
                'title' => 'Updated Title',
            ]);

        $response->assertForbidden();
    }
}
