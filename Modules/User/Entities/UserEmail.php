<?php
namespace Modules\User\Entities;

use Illuminate\Database\Eloquent\Model;

class UserEmail extends Model
{
    protected $table = 'users_emails';

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'type',
        'email_uuid',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
