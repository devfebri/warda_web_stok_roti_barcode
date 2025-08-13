<?php

namespace Database\Factories;

use App\Models\Cheesecake;
use Illuminate\Database\Eloquent\Factories\Factory;

class CheesecakeFactory extends Factory
{
    protected $model = Cheesecake::class;

    public function definition(): array
    {
        return [
            'nama' => $this->faker->words(2, true),
            'harga' => $this->faker->numberBetween(20000, 100000),
            'stok' => $this->faker->numberBetween(1, 100),
            'baker_id' => 1, // default, sesuaikan jika ada relasi user
        ];
    }
}
