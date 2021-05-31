<?php

namespace Database\Factories;

use App\Models\event;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;


class EventFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = event::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $startEvent = $this->faker->dateTimeBetween($startDate = 'now', $endDate = '+ 6months', $timezone = null);

        return [
            'user_id' => $this->faker->numberBetween(1,10),
            'name' => $this->faker->text($maxNbChars = 32),
            'startEvent' => $startEvent,
            'endEvent' => $this->faker->dateTimeBetween($startDate = $startEvent , $endDate = '+ 6 months', $timezone = null),
            'description' => $this->faker->text($maxNbChars = 500),
            'location' => $this->faker->city,
        ];
    }
}
