<?php


namespace App\Repositories;

use App\Models\Post;

class PostRepository extends BaseRepository
{
    /** @var Post */
    private $postModel;

    public function model(): string
    {
        return Post::class;
    }


}
