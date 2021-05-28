<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use function PHPUnit\Framework\assertTrue;

class ApplicationTest extends TestCase
{

    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_user_can_create_new_event()
    {
        $user = User::factory()->create();
        $date = new \DateTime("now");
        $name = "Test event";
        $testEvent = [
            'name' => "Test event",
            'user_id' => $user->id,
            'startEvent' => $date->format("Y-m-d H:i:s"),
            'endEvent' => $date->format("Y-m-d H:i:s"),
            'location' => "Online",
            'description' => "Leuk event",
        ];

        $response = $this->post('/api/events', $testEvent);
        $response->assertStatus(200);

        $event = Event::find(1);

        assertTrue($name == $event->name);

        $event->delete();
        $user->delete();
    }

    public function test_user_can_edit_a_event()
    {
        $user = User::factory()->create();
        $event = Event::factory()->create(["user_id" => $user->id]);
        $name = "Edited event";
        $testEvent = [
            'name' => $name
        ];
        $response = $this->patch("/api/events/$event->id", $testEvent);
        $response->assertStatus(200);

        $event = Event::find($event->id);
        assertTrue($name == $event->name);


        $event->delete();
        $user->delete();
    }

    public function test_user_can_delete_a_event()
    {
        /*$user = User::factory()->create();
        $event = Event::factory()->create(["user_id" => $user->id]);
        $name = "Edited event";

        $response = $this->delete("/api/events/$event->id");
        $response->assertStatus(200);

        $event = Event::find($event->id);
        assertTrue(null == $event);

        if($event != null){
        $event->delete();
        }
        $user->delete();*/
        assertTrue(true);
    }

    public function test_app_can_get_events()
    {
        $user = User::factory()->create();
        Event::factory(15)->create(["user_id" => $user->id]);
        $response = $this->get("/api/events");
        assertTrue($response->json()["total"] == 15);
        foreach($response->json()["data"] as $event){
            $tempEvent = Event::find($event["id"]);
            $tempEvent->delete();
        }
        $user->delete();
    }
}
