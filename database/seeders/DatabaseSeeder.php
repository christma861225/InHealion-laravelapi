<?php

namespace Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call('\Database\Seeders\UsersRoleTableSeeder');
        $this->call('\Database\Seeders\UserTableSeeder');
    }
}
