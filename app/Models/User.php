<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'banner',
        'avatar',
        'username',
        'name',
        'email',
        'password',
        'bio',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function timeline(){
        //include all the User's tweets as well as the tweets of the people he follows 
        //in desc order
        $ids = $this->follows()->pluck('id');

        return Tweet::whereIn('user_id', $ids)
        ->withLikes()
        ->orWhere('user_id', $this->id)
        ->latest()->paginate(15);
    }

    public function setPasswordAttribute($value){

        $this->attributes['password'] = bcrypt($value);
    }

    public function getAvatar(){
        if (\Illuminate\Support\Str::startsWith($this->avatar ?? '', 'http')) return $this->avatar;
        return "/storage/".$this->avatar;
    }

    public function getBanner(){
        if (\Illuminate\Support\Str::startsWith($this->banner ?? '', 'http')) return $this->banner;
        return "/storage/".$this->banner;
    }

    public function isFollowing(User $user){

        return $this->follows()->where('following_user_id', $user->id)->exists();
    }

    public function follows()
    {
        return $this->belongsToMany(User::class, 'follows', 'user_id', 'following_user_id');
    }

    public function follow(User $user){

        return $this->follows()->save($user);
    }
    
    public function unfollow(User $user){
        return $this->follows()->detach($user);
    }

    public function toggleFollow(User $user){
        $this->follows()->toggle($user);
    }
    
    public function path($append = ''){
        $path = route('profile', $this->username);

        return $append ? "{$path}/{$append}" : "$path";
    }

    public function tweets()
    {
        return $this->hasMany(Tweet::class)->latest();
    }
    
    public function latestTweet(){
        return $this->hasMany(Tweet::class)->latest()->first();
    }

    /**
     * Get all of the comments for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function likes()
    {
        return $this->hasMany(Like::class);
    }
}
