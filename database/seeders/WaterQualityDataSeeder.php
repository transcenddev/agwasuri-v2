<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WaterQualityData;
use Faker\Factory as Faker;

class WaterQualityDataSeeder extends Seeder
{
    /**
     * Seed the water quality data.
     */
    public function run(): void
    {
        $faker = Faker::create();

        for ($i = 0; $i < 50; $i++) {
            $temperature = $faker->randomFloat(2, 15, 35);
            $recorded_at = $faker->dateTimeBetween('-1 years', 'now');

            $basePhLevel = 7.0;
            $baseDO = 5.0;
            $baseSalinity = 30.0;

            $simulatedPhLevel = $basePhLevel + ($temperature - 25) * 0.1 + $faker->randomFloat(2, -1, 1);
            $simulatedDO = $baseDO + ($temperature - 25) * 0.05 + $faker->randomFloat(2, -1, 1);
            $simulatedSalinity = $baseSalinity + ($temperature - 25) * 0.2 + $faker->randomFloat(2, -5, 5);

            WaterQualityData::create([
                'recorded_at'      => $recorded_at,
                'temperature'      => round($temperature, 2),
                'ph_level'         => round($simulatedPhLevel, 2),
                'dissolved_oxygen' => round($simulatedDO, 2),
                'salinity'         => round($simulatedSalinity, 2),
                'user_id'          => 2,
            ]);
        }

        for ($i = 0; $i < 50; $i++) {
            $temperature = $faker->randomFloat(2, 15, 35);
            $recorded_at = $faker->dateTimeBetween('-1 years', 'now');

            $basePhLevel = 7.0;
            $baseDO = 5.0;
            $baseSalinity = 30.0;

            $simulatedPhLevel = $basePhLevel + ($temperature - 25) * 0.1 + $faker->randomFloat(2, -1, 1);
            $simulatedDO = $baseDO + ($temperature - 25) * 0.05 + $faker->randomFloat(2, -1, 1);
            $simulatedSalinity = $baseSalinity + ($temperature - 25) * 0.2 + $faker->randomFloat(2, -5, 5);

            WaterQualityData::create([
                'recorded_at'      => $recorded_at,
                'temperature'      => round($temperature, 2),
                'ph_level'         => round($simulatedPhLevel, 2),
                'dissolved_oxygen' => round($simulatedDO, 2),
                'salinity'         => round($simulatedSalinity, 2),
                'user_id'          => 3,
            ]);
        }
    }
}
