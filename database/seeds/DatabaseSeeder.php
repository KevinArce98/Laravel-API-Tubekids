<?php

use Illuminate\Database\Seeder;
use App\Video;
use App\User;
use App\Kid;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
    	// $this->call(UsersTableSeeder::class);

        User::flushEventListeners();
        Video::flushEventListeners();
        Kid::flushEventListeners();


    	$countUsers = 50;
    	$countKids = 100;
    	$countVideos = 100;

    	factory(User::class, $countUsers)->create();
    	factory(Kid::class, $countKids)->create();
    	factory(Video::class, $countVideos)->create();
    
    }
}
