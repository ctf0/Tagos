<?php

use Faker\Factory;
use Illuminate\Database\Seeder;

class TagsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $faker = Factory::create();
        $model = app(config('tags.model'));

        for ($i=0; $i < 5; ++$i) {
            $model->create([
                'name' => $faker->unique()->safeColorName(),
            ]);
        }
    }
}
