<?php

namespace Tests\Feature;

use App\Http\Controllers\ConsumerController;
use App\Models\Event;
use App\Models\EventSubscriber;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use function PHPUnit\Framework\assertEmpty;
use function PHPUnit\Framework\assertTrue;

class RabbitMQTest extends TestCase
{

    use RefreshDatabase;

    public function test_consumer_can_consume_a_XML_from_other_app()
    {
        $testXML = "<?xml version=\"1.0\" encoding=\"utf-8\"?>

        <user>
          <header>
            <UUID>333ade47-03d1-40bb-9912-9a6c86a60169</UUID>
            <method>CREATE</method>
            <origin>AD</origin>
            <version>1</version>
            <sourceEntityId></sourceEntityId>
            <timestamp>2021-05-25T12:00:00+01:00</timestamp>
          </header>
          <body>
            <firstname>Tibo</firstname>
            <lastname>De Munck</lastname>
            <email>tibo.de.munck@student.dhb.be</email>
            <birthday>1998-06-03</birthday>
            <role>student</role>
            <study>Dig-X</study>
          </body>
        </user>";
        $return = ConsumerController::consumerHandeling($testXML);
        if(array_key_exists ("error",$return)) error_log($return["error"]);
        $xml = $return["xml"];
        
        $header = $xml->getElementsByTagName("header")[0];
        assertTrue($return["route"] == "UUID");
        assertTrue($header->getElementsByTagName("sourceEntityId")[0]->nodeValue == "");

    }

    public function test_consumer_can_consume_a_create_event_from_other_app()
    {
        $user = USER::factory()->create();
        $testXML = "<?xml version=\"1.0\" encoding=\"utf-8\"?>
        
        <event>
            <header>
                <UUID>5698cd59-3acc-4f15-9ce2-83545cbfe0ba</UUID>
                <sourceEntityId></sourceEntityId>
                <organiserUUID>333ade47-03d1-40bb-9912-9a6c86a60169</organiserUUID>
                <organiserSourceEntityId>$user->id</organiserSourceEntityId>
                <method>CREATE</method>
                <origin>UUID</origin>
                <version>2</version>
                <timestamp>2021-05-25T12:00:00+01:00</timestamp>
            </header>
            <body>
                <name>DhB Goes Tomorrowland</name>
                <startEvent>2021-05-25T12:00:00</startEvent>
                <endEvent>2021-05-27T02:00:00</endEvent>
                <description>Desiderius hogeschool Brussel gaat op uistap met alle studenten naar Tomorrowland, Poggers!</description>
                <location>Avenue Montana 34, 1180 Bruxelles</location>
            </body>
        </event>";
        $return = ConsumerController::consumerHandeling($testXML);
        if(array_key_exists ("error",$return)) error_log($return["error"]);
        $createdEvent = Event::find(1);
        assertTrue($return["route"] == "UUID");
        assertTrue($createdEvent->name == "DhB Goes Tomorrowland");
        $createdEvent->delete();
        $user->delete();
    }

    public function test_consumer_can_consume_a_update_event_XML()
    {
        $user = USER::factory()->create();
        $event = Event::factory()->create(["user_id" => $user->id]);
        $testXML = "<?xml version=\"1.0\" encoding=\"utf-8\"?>

        <event>
            <header>
                <UUID>5698cd59-3acc-4f15-9ce2-83545cbfe0ba</UUID>
                <sourceEntityId>$event->id</sourceEntityId>
                <organiserUUID>333ade47-03d1-40bb-9912-9a6c86a60169</organiserUUID>
                <organiserSourceEntityId>$user->id</organiserSourceEntityId>
                <method>UPDATE</method>
                <origin>UUID</origin>
                <version>2</version>
                <timestamp>2021-05-25T12:00:00+01:00</timestamp>
            </header>
            <body>
                <name>DhB Goes Tomorrowland</name>
                <startEvent>2021-05-25T12:00:00</startEvent>
                <endEvent>2021-05-27T02:00:00</endEvent>
                <description>Desiderius hogeschool Brussel gaat op uistap met alle studenten naar Tomorrowland, Poggers!</description>
                <location>Avenue Montana 34, 1180 Bruxelles</location>
            </body>
        </event>";
        $return = ConsumerController::consumerHandeling($testXML);
        if(array_key_exists ("error",$return)) error_log($return["error"]);
        
        $updatedEvent = Event::find($event->id);
        assertTrue($return["route"] == "UUID");
        assertTrue($updatedEvent->name == "DhB Goes Tomorrowland");
        $updatedEvent->delete();
        $user->delete();
    }

    public function test_consumer_can_consume_a_delete_event_XML()
    {
        $user = USER::factory()->create();
        $event = Event::factory()->create(["user_id" => $user->id]);
        $testXML = "<?xml version=\"1.0\" encoding=\"utf-8\"?>

        <event>
            <header>
                <UUID>5698cd59-3acc-4f15-9ce2-83545cbfe0ba</UUID>
                <sourceEntityId>$event->id</sourceEntityId>
                <organiserUUID>333ade47-03d1-40bb-9912-9a6c86a60169</organiserUUID>
                <organiserSourceEntityId>$user->id</organiserSourceEntityId>
                <method>DELETE</method>
                <origin>UUID</origin>
                <version>2</version>
                <timestamp>2021-05-25T12:00:00+01:00</timestamp>
            </header>
            <body>
                <name>DhB Goes Tomorrowland</name>
                <startEvent>2021-05-25T12:00:00</startEvent>
                <endEvent>2021-05-27T02:00:00</endEvent>
                <description>Desiderius hogeschool Brussel gaat op uistap met alle studenten naar Tomorrowland, Poggers!</description>
                <location>Avenue Montana 34, 1180 Bruxelles</location>
            </body>
        </event>";
        $return = ConsumerController::consumerHandeling($testXML);
        if(array_key_exists ("error",$return)) error_log($return["error"]);
        
        assertEmpty(Event::find($event->id));
        if(Event::find($event->id) != null){
            Event::find($event->id)->delete();
        }
        $user->delete();
    }

    public function test_consumer_can_consume_a_create_user_XML()
    {
        $testXML = "<?xml version=\"1.0\" encoding=\"utf-8\"?>

        <user>
          <header>
            <UUID>333ade47-03d1-40bb-9912-9a6c86a60169</UUID>
            <method>CREATE</method>
            <origin>UUID</origin>
            <version>1</version>
            <sourceEntityId></sourceEntityId>
            <timestamp>2021-05-25T12:00:00+01:00</timestamp>
          </header>
          <body>
            <firstname>Tibo</firstname>
            <lastname>De Munck</lastname>
            <email>tibo.de.munck@student.dhb.be</email>
            <birthday>1998-06-03</birthday>
            <role>student</role>
            <study>Dig-X</study>
          </body>
        </user>";
        $return = ConsumerController::consumerHandeling($testXML);
        if(array_key_exists ("error",$return)) error_log($return["error"]);
        
        $createdUser = User::find(1);
        assertTrue($return["route"] == "UUID");
        assertTrue($createdUser->firstName == "Tibo");
        $createdUser->delete();
    }

    public function test_consumer_can_consume_a_update_user_XML()
    {
        $user = User::factory()->create();
        $testXML = "<?xml version=\"1.0\" encoding=\"utf-8\"?>

        <user>
          <header>
            <UUID>333ade47-03d1-40bb-9912-9a6c86a60169</UUID>
            <method>UPDATE</method>
            <origin>UUID</origin>
            <version>2</version>
            <sourceEntityId>$user->id</sourceEntityId>
            <timestamp>2021-05-25T12:00:00+01:00</timestamp>
          </header>
          <body>
            <firstname>Tibo</firstname>
            <lastname>De Munck</lastname>
            <email>tibo.de.munck@student.dhb.be</email>
            <birthday>1998-06-03</birthday>
            <role>student</role>
            <study>Dig-X</study>
          </body>
        </user>";
        $return = ConsumerController::consumerHandeling($testXML);
        if(array_key_exists ("error",$return)) error_log($return["error"]);
        
        $updatedUser = User::find($user->id);
        assertTrue($return["route"] == "UUID");
        assertTrue($updatedUser->lastName == "De Munck");
        $updatedUser->delete();
    }

    public function test_consumer_can_consume_a_delete_user_XML()
    {
        $user = User::factory()->create();
        $testXML = "<?xml version=\"1.0\" encoding=\"utf-8\"?>

        <user>
          <header>
            <UUID>333ade47-03d1-40bb-9912-9a6c86a60169</UUID>
            <method>DELETE</method>
            <origin>UUID</origin>
            <version>2</version>
            <sourceEntityId>$user->id</sourceEntityId>
            <timestamp>2021-05-25T12:00:00+01:00</timestamp>
          </header>
          <body>
            <firstname>Tibo</firstname>
            <lastname>De Munck</lastname>
            <email>tibo.de.munck@student.dhb.be</email>
            <birthday>1998-06-03</birthday>
            <role>student</role>
            <study>Dig-X</study>
          </body>
        </user>";
        $return = ConsumerController::consumerHandeling($testXML);
        if(array_key_exists ("error",$return)) error_log($return["error"]);
        
        assertEmpty(Event::find($user->id));
        if(Event::find($user->id) != null){
            Event::find($user->id)->delete();
        }
    }

    public function test_consumer_can_consume_a_create_eventSubscribe_from_other_app()
    {
        $user = USER::factory()->create();
        $user2 = USER::factory()->create();
        $event = Event::factory()->create(["user_id" => $user->id]);
        $testXML = "<?xml version=\"1.0\" encoding=\"utf-8\"?>

        <eventSubscribe>
            <header>
                <UUID>333ade47-03d1-40bb-9912-9a6c86a60169</UUID>
                <method>SUBSCRIBE</method>
                <origin>UUID</origin>
                <version>1</version>
                <sourceEntityId></sourceEntityId>
                <timestamp>2021-05-25T12:00:00+01:00</timestamp>
            </header>
            <body>
                <eventUUID>5698cd59-3acc-4f15-9ce2-83545cbfe0ba</eventUUID>
                <eventSourceEntityId>$event->id</eventSourceEntityId>
                <attendeeUUID>64e1e5fb-bdae-4d74-aa55-f3a34528ae04</attendeeUUID>
                <attendeeSourceEntityId>$user2->id</attendeeSourceEntityId>
            </body>
        </eventSubscribe>";
        $return = ConsumerController::consumerHandeling($testXML);
        if(array_key_exists ("error",$return)) error_log($return["error"]);
        $createdEventSubscription = EventSubscriber::find(1);
        assertTrue($return["route"] == "UUID");
        assertTrue($createdEventSubscription->user_id == $user2->id);
        $createdEventSubscription->delete();
        $event->delete();
        $user->delete();
        $user2->delete();
        
    }


    public function test_consumer_can_consume_a_delete_eventSubscribe_XML()
    {
        $user = USER::factory()->create();
        $user2 = USER::factory()->create();
        $event = Event::factory()->create(["user_id" => $user->id]);
        $eventSubscribe = new EventSubscriber(["user_id" => $user->id, "event_id" => $event->id]);
        $eventSubscribe->save();
        $testXML = "<?xml version=\"1.0\" encoding=\"utf-8\"?>

        <eventSubscribe>
            <header>
                <UUID>333ade47-03d1-40bb-9912-9a6c86a60169</UUID>
                <method>UNSUBSCRIBE</method>
                <origin>UUID</origin>
                <version>1</version>
                <sourceEntityId>$eventSubscribe->id</sourceEntityId>
                <timestamp>2021-05-25T12:00:00+01:00</timestamp>
            </header>
            <body>
                <eventUUID>5698cd59-3acc-4f15-9ce2-83545cbfe0ba</eventUUID>
                <eventSourceEntityId>$event->id</eventSourceEntityId>
                <attendeeUUID>64e1e5fb-bdae-4d74-aa55-f3a34528ae04</attendeeUUID>
                <attendeeSourceEntityId>$user2->id</attendeeSourceEntityId>
            </body>
        </eventSubscribe>";
        $return = ConsumerController::consumerHandeling($testXML);
        if(array_key_exists ("error",$return)) error_log($return["error"]);
        
        assertEmpty(EventSubscriber::find($eventSubscribe->id));
        if(Event::find($eventSubscribe->id) != null){
            Event::find($eventSubscribe->id)->delete();
        }
        $user->delete();
    }

    public function test_in_case_of_error_XML_gets_created(){
        $xml = ConsumerController::errorArrayToXML([
            "code" => 1002,
            "objectOrigin" => "FrontEnd",
            "objectUUID" => "333ade47-03d1-40bb-9912-9a6c86a60169",
            "objectSourceId" => 3
        ]);
        $body = $xml->getElementsByTagName("body")[0];
        assertTrue($body->getElementsByTagName("description")[0]->nodeValue == "Incorrect message, incorrect XML");


        $xml2 = ConsumerController::errorArrayToXML([
            "code" => 1000
        ]);
        $header = $xml2->getElementsByTagName("header")[0];
        assertTrue($header->getElementsByTagName("code")[0]->nodeValue == "1000");


        $xml3 = ConsumerController::errorArrayToXML([
            "code" => 1003,
            "objectOrigin" => "FrontEnd",
            "objectUUID" => "333ade47-03d1-40bb-9912-9a6c86a60169",
            "objectSourceId" => 3
        ]);
        $body = $xml3->getElementsByTagName("body")[0];
        assertTrue($body->getElementsByTagName("objectSourceId")[0]->nodeValue == 3);
        assertTrue($body->getElementsByTagName("objectUUID")[0]->nodeValue == "333ade47-03d1-40bb-9912-9a6c86a60169");

        $this->expectException(\Exception::class);

        ConsumerController::errorArrayToXML([
            "code" => 100200
        ]);
    }

}
