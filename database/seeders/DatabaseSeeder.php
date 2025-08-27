<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $data = new User();
        $data->name = 'Pimpinan';
        $data->email = 'pimpinan@gmail.com';
        $data->username = 'pimpinan';
        $data->role = 'pimpinan';
        $data->password = bcrypt('password');
        $data->save();

        $data = new User();
        $data->name = 'Admin';
        $data->email = 'admin@gmail.com';
        $data->username = 'admin';
        $data->role = 'admin';
        $data->password = bcrypt('password');
        $data->save();

        $data = new User();
        $data->name = 'Warda';
        $data->email = 'baker@gmail.com';
        $data->username = 'baker1';
        $data->role = 'baker';
        $data->password = bcrypt('password');
        $data->save();
        $data = new User();
        $data->name = 'Putri';
        $data->email = 'baker@gmail.com';
        $data->username = 'baker2';
        $data->role = 'baker';
        $data->password = bcrypt('password');
        $data->save();

        $data = new User();
        $data->name = 'karyawan';
        $data->email = 'karyawan@gmail.com';
        $data->username = 'karyawan';
        $data->role = 'karyawan';
        $data->password = bcrypt('password');
        $data->save();


    }
}
