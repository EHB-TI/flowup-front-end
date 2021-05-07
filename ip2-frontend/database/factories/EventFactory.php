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
            'uuid' => $this->faker->uuid,
            'name' => $this->faker->text($maxNbChars = 30) ,

            'description' => $this->faker->text($maxNbChars = 500),
                           

            'location' => $this->faker->city,
            'startsAtDate' => $this->faker->dateTimeThisYear->format('Y-m-d'),
            'startsAtTime' => $this->faker->time($format = 'H:i', $min = 'now'),
            'endsAtDate' => $this->faker->dateTimeThisYear->format('Y-m-d'),
            'endsAtTime' => $this->faker->time($format = 'H:i', $min = 'now'),
        ];
    }
}
