<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Brand;
use Illuminate\Support\Str;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create();

        for ($i = 0; $i < 10; $i++) {
            Brand::create([
                'slug' => Str::slug($faker->words(2, true)),
                'image' => $faker->imageUrl(640, 480, 'business', true, 'Faker'),
            ]);
        }
    }
}
