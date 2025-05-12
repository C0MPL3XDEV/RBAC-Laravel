<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;
use Psy\Util\Str;
use function Laravel\Prompts\table;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        for ($i = 0; $i < 20; $i++) {
            DB::table('product')->insert([
                'name' => $faker->words(3, true),
                'description' => $faker->sentence(10),
                'price' => $faker->randomFloat(2, 5, 500),
                'stock' => $faker->numberBetween(0, 200),
                'sku' => 'SKU-'.strtoupper($faker->unique()->bothify('???#####??')),
                'category' => $faker->randomElement(['Elettronica', 'Casa', 'Abbigliamento', 'Sport']),
                'is_active' => $faker->boolean(80),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
