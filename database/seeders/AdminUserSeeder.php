<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $user = User::where('email', 'developer@maaldatalabs.com')->first();
        if(!$user){
            $user = User::create([
                'name' => 'Developer',
                'email' => 'developer@maaldatalabs.com',
                'password' => bcrypt('password'),
            ]);
        }else{
            $user->password = bcrypt('12345678');
            $user->save();
        }

        $user->assignRole('admin');
    }
}
