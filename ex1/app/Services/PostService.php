<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\PostRepository;

class PostService
{
    /** @var PostRepository */
    private $postRepo;


    private $user;

    public function __construct(User $user, PostRepository $postRepo)
    {
        $this->postRepo = $postRepo;
        $this->user = $user;
    }

    public function index()
    {
        return $this->postRepo->getAll();
    }

    public function store($data)
    {
        return $this->postRepo->create([
            'user_id' => $this->user->id,
            'title' => $data['title'],
            'detail' => $data['detail']
        ]);
    }

    public function update($id, $data)
    {
        return $this->postRepo->update($id,$data);
    }

    public function delete($id, $data)
    {
        return $this->postRepo->destroy($id);
    }

}
