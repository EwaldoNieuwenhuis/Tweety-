<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Tweet;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('PRAGMA foreign_keys = OFF;');
        User::truncate();
        Tweet::truncate();
        DB::table('follows')->truncate();
        DB::table('likes')->truncate();
        DB::statement('PRAGMA foreign_keys = ON;');

        $user1 = User::create([
            'username' => 'pinkygirl',
            'name' => 'Pinky Developer',
            'email' => 'pinky@example.com',
            'password' => 'password',
            'avatar' => 'avatars/user1.png',
            'banner' => 'banners/banner1.png',
            'bio' => 'Lover of all things pink and coding!',
        ]);

        $user2 = User::create([
            'username' => 'athletic',
            'name' => 'Athletic Dude',
            'email' => 'dude@example.com',
            'password' => 'password',
            'avatar' => 'avatars/user2.png',
            'banner' => 'banners/banner1.png',
            'bio' => 'Just running around the city.',
        ]);

        $user3 = User::create([
            'username' => 'doggo',
            'name' => 'Cool Doggo',
            'email' => 'doggo@example.com',
            'password' => 'password',
            'avatar' => 'avatars/user3.png',
            'banner' => 'banners/banner1.png',
            'bio' => 'Woof woof! I am the coolest dog on this site.',
        ]);

        // Generate 50 extra users
        User::factory()->count(50)->create();
        $allUsers = User::all();

        // Let the generated users follow random others
        foreach ($allUsers as $u) {
            // Randomly follow 5 to 15 other users
            $randomFollows = $allUsers->random(rand(5, 15));
            foreach ($randomFollows as $f) {
                if ($u->id !== $f->id && !$u->isFollowing($f)) {
                    $u->follow($f);
                }
            }
        }

        // Generate tweets for everyone
        foreach ($allUsers as $u) {
            Tweet::factory()->count(rand(2, 6))->create(['user_id' => $u->id]);
        }

        $allTweets = Tweet::all();

        // Generate randomized likes/dislikes
        foreach ($allTweets as $tweet) {
            $likers = $allUsers->random(rand(0, 10)); // up to 10 interactions per tweet
            foreach ($likers as $liker) {
                $liked = rand(0, 1) === 1;
                // Use insertOrIgnore in case of duplicates from rand or existing relationship
                DB::table('likes')->insertOrIgnore([
                    'user_id' => $liker->id,
                    'tweet_id' => $tweet->id,
                    'liked' => $liked,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
