<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        // \App\Models\User::factory(10)->create();
        $name = User::count('id') ? 'Test User'. User::count('id') + 1 : 'Test User';
        $email = User::count('email') ? 'test'. User::count('id') + 1 .'@mail.com' : 'test@mail.com';
        $nric = rand(000000000001,999999999999);
        $user = User::factory()->create([
            'name' => $name,
            'email' => $email,
            'password' => bcrypt('password'),
            'referral_id' => strtoupper(uniqid()),
            'NRIC_number' => $nric,
        ]);

        $user->assignRole(['admin']);
    }
}
