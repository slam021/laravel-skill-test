<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Command;

class PublishScheduledPosts extends Command
{
    protected $signature = 'posts:publish-scheduled';

    protected $description = 'Publish scheduled posts that are due';

    public function handle()
    {
        Post::where('is_draft', true)
            ->where('published_at', '<=', now())
            ->update(['is_draft' => false]);

        $this->info('Scheduled posts published successfully.');
    }
}
