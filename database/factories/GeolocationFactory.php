<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Geolocation>
 */
class GeolocationFactory extends Factory
{

   
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        
            return [
                'user_id' =>rand(1,30),
                'lat' => fake()->latitude(),
                'lon' => fake()->longitude(),
            ];
        
       
    }
}
