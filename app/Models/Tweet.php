<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Models\User;

class Tweet extends Model
{
    use HasFactory;

    /**
     * Get the user that owns the Tweet
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'foreign_key', 'local_key');
    }

    public function scopeWithLikes(Builder $query){

        // YOU CAN'T USE CAPITOL LETTERING
        //----------------------------------------
        //      this will fail
        //      $query = leftJoinSub(
        //     'SELECT tweet_id, sum(liked) as likes, 
        //      sum(!liked) as dislikes FROM `likes` GROUP BY tweet_id ',
        //     'likes',
        //     'likes.tweet_id',
        //     'tweets.id'
        // );
        //-----------------------------------------
        
        //this is good
        $query->leftJoinSub(
            'select tweet_id, sum(liked) likes, sum(not liked) dislikes from likes group by tweet_id',
            'likes',
            'likes.tweet_id',
            'tweets.id'
        );

        // query
        // LEFT JOIN (SELECT tweet_id, sum(liked) as likes,
        // sum(!liked) as dislikes 
        // FROM `likes`
        // GROUP BY tweet_id ) likes on likes.tweet_id = tweets.id 
        // ORDER BY `likes`.`likes` DESC

    }

    /**
     * Get all of the likes for the Tweet
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function getImage(){
        if (\Illuminate\Support\Str::startsWith($this->image ?? '', 'http')) return $this->image;
        return "/storage/".$this->image;
    }

    //like the tweet
    public function like($user= null, $liked = true){
  
        if(!$user) $user = auth()->user();
        
        if($this->isLikedBy($user) && $liked){
            $this->likes()->where('user_id', $user->id)->delete();
            return;
        }
        
        if($this->isDislikedBy($user) && !$liked){
            $this->likes()->where('user_id', $user->id)->delete();
            return;
        }
        
        //if there is a tweet with that user id update it
        //else create a like record in database
        $this->likes()->updateOrCreate([
            'user_id' => $user->id, 
        ],[
            'liked' => $liked
        ]);
        
    }

    //dislike the tweet
    public function dislike($user = null){

        return $this->like($user, false);
    }

    function isLikedBy(User $user){
        return (bool)
        $user->likes
        ->where('tweet_id', $this->id)
        ->where('liked', true)
        ->count();
    }

    function isDislikedBy(User $user){
        return (bool)
        $user->likes
        ->where('tweet_id', $this->id)
        ->where('liked', false)
        ->count();
    }

    function bodyWithMentions(){

        //loop over body if there is a mention make it an <a href> to the user

        $finalbody = "";
        $collect=false;
        $txt = $this->body." ";
        $mention="";
        for($i=0; $i<strlen($txt);$i++){
            //check if there is a mention
            //else add it to a string
            if($collect){
                if($txt[$i]==" "){
                    //end of mention
                    //get id of user that is mentioned
                    $user = User::where('username', $mention)->first();
                    if($user){
                        //check if there's a user
                        $link = "<a href='/profiles/".$user->username."' class=' text-blue-400'>@".$user->username."</a>";
                        $finalbody = $finalbody.$link." ";
                    }
                    else{
                        $finalbody = $finalbody."@".$mention;
                    }
                    //empty mention 
                    //collect is false
                    $mention = "";
                    $collect = false;
                }
                else{
                    //there is a mention
                    //collect mention
                    $mention = $mention.$txt[$i];
                }
            }
            else{
                if($txt[$i] != '@'){
                    $finalbody = $finalbody.$txt[$i];
                }
                
            }

            if($txt[$i]=='@'){
                $collect=true;
            }
        }
        return $finalbody;
    }
}
