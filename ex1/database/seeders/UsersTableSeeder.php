<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('users')->delete();
        DB::table('users')->insert([
            0 => [
                'id' => 1,
                'name' => 'Van Tran',
                'email' => 'dinhvan.it92@gmail.com',
                'role' => Role::ADMIN,
                'email_verified_at' => null,
                'password' => bcrypt('@dmin123'),
            ],
            1 => [
                'id' => 2,
                'name' => 'VT',
                'email' => 'dinhvan@gmail.com',
                'role' => Role::USER,
                'email_verified_at' => null,
                'password' => bcrypt('@dmin123'),
            ],
        ]);
    }
}
