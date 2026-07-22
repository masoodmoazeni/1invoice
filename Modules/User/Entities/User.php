<?php
namespace Modules\User\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory;

    protected $table = 'users';

    protected $fillable = [
        'invited_id',
        'firstname',
        'lastname',
        'email',
        'mobile',
        'password',
        'image',
        'active_email',
        'status',
        'google_id',
        'verification_token'
    ];

    protected $hidden = [
        'password','google_id'
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_users');
    }

    public function hasRole($roles)
    {
        if (is_array($roles)) {
            return $this->roles()->whereIn('name', $roles)->exists();
        }
        return $this->roles()->where('name', $roles)->exists();
    }

    public function inviter()
    {
        return $this->belongsTo(User::class, 'invited_id');
    }

    public function invitedUsers()
    {
        return $this->hasMany(User::class, 'invited_id');
    }

    public function getUserType()
    {
        if ($this->hasRole('admin')) {
            return 'admin';
        }
        if ($this->hasRole('broker')) {
            return 'broker';
        }
        return 'user';
    }

    protected $appends = ['image_url'];

    public function getImageUrlAttribute(): string
    {
        if (!$this->image) {
            return asset('images/default-avatar.jpg');
        }

        if (is_string($this->image) && filter_var($this->image, FILTER_VALIDATE_URL)) {
            return $this->image;
        }

        return asset("images/{$this->id}/profile/{$this->image}");
    }
}
