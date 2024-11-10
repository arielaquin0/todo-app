<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $statuses = ['Completed', 'Pending'];

        for ($i = 0; $i < 18; $i++) {
            DB::table('tasks')->insert([
                'title' => $faker->unique()->sentence(3),
                'description' => $faker->text(120),
                'status' => $faker->randomElement($statuses),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
