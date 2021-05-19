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
        return [
            'user_id' => $this->faker->numberBetween(1,10),
            'name' => $this->faker->text($maxNbChars = 32),
            'startEvent' => $this->faker->dateTime($max = 'now'),
            'endEvent' => $this->faker->dateTime($max = 'now'),
            'description' => $this->faker->text($maxNbChars = 500),
            'location' => $this->faker->city,
        ];
    }
}
