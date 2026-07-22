<?php

namespace Modules\Setting\Repositories;

use Modules\Setting\Entities\Comment;

class CommentRepository
{
    public function all()
    {
        return Comment::latest()->get();
    }

    public function find($id)
    {
        return Comment::find($id);
    }

    public function create(array $data)
    {
        return Comment::create($data);
    }

    public function update($id, array $data)
    {
        $comment = Comment::find($id);
        if (!$comment) {
            return false;
        }

        $comment->update($data);
        return $comment;
    }

    public function delete($id)
    {
        $comment = Comment::find($id);
        if (!$comment) {
            return false;
        }

        return $comment->delete();
    }

    public function getActive()
    {
        return Comment::where('status', 1)->latest()->get();
    }

    public function getByUser($userId)
    {
        return Comment::where('user_id', $userId)->latest()->get();
    }

    public function paginate($perPage = 15)
    {
        return Comment::orderBy('id', 'desc')->paginate($perPage);
    }

}
