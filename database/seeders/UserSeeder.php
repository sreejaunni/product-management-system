<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        $faker = Faker::create();

        // Create 5 users
        $users = User::factory()->count(5)->make(); // Using factory to create users without saving

        // Assign roles to each user
        $users->each(function ($user) use ($faker) {
            // Save the user to the database
            $user->save();

            // Randomly pick a role
            $role = Role::inRandomOrder()->first(); // Fetch a random role

            // Attach the role to the user
            $user->roles()->attach($role->id);
        });
    }
}
