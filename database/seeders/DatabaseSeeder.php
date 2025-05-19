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
        $data->name = 'Kepala Toko';
        $data->email = 'kepalatoko@gmail.com';
        $data->username = 'kepalatoko';
        $data->role = 'kepalatoko';
        $data->password = bcrypt('password');
        $data->save();

        $data = new User();
        $data->name = 'Pimpinan';
        $data->email = 'pimpinan@gmail.com';
        $data->username = 'pimpinan';
        $data->role = 'pimpinan';
        $data->password = bcrypt('password');
        $data->save();

        $data = new User();
        $data->name = 'Baker';
        $data->email = 'baker@gmail.com';
        $data->username = 'baker';
        $data->role = 'baker';
        $data->password = bcrypt('password');
        $data->save();
    }
}
