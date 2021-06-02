<?php

namespace App\Http\Controllers;

use PhpAmqpLib\Message\AMQPMessage;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\EventSubscriber;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Illuminate\Support\Facades\DB;
use DateTime;
use DateTimeZone;
use Exception;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
  //
    public function index()
    {
        $events = Event::where('endEvent','>=',date('Y-m-d'))->orderBy('startEvent', 'asc')->paginate(25)->toArray();
        return array_reverse($events);
    }

  public function store(Request $request)
  {
    $event = EventController::saveEvent($request);
    if ($this->sendXMLtoUUID($event, "create")) {
      return response()->json('Event created!');
    }
    return response()->json('Event creation failed!');
  }

  public function show($id)
  {
    $event = Event::find($id);
    return response()->json($event);
  }

  public function showEventsYouAttend($request){
    $user_id = 4;

    $events = DB::table('events')
        ->join('event_subscribers', 'events.id', '=','event_subscribers.event_id')
        ->where('endEvent','>=',date('Y-m-d'))
        ->where('event_subscribers.user_id','=',$user_id)->orderBy('startEvent', 'asc')->get();

    //$events = EventSubscriber::where('user_id','=',$user_id);
    return response()->json($events);
  }

  public function showByUser($request)
  {
    $user_id = 3;
    $event = Event::where('user_id', '=', $user_id)->orderBy('startEvent', 'asc')->get();
    return response()->json($event);
  }

  public function update($id, Request $request)
  {
    $event = EventController::editEvent($id, $request);
    if ($this->sendXMLtoUUID($event, "update")) {
      return response()->json('Event updated!');
    }
    return response()->json('Event update failed!');
  }

  public static function destroy($id)
  {
    $event = EventController::deleteEvent($id);

    if (EventController::sendXMLtoUUID($event, "delete")) {
      return response()->json('Event deleted');
    }
  }


  //ErrorHandling
  public function checkName(Request $request)
  {
    $this->validate($request, [
      'name' => 'required|min:3|max:30|regex:/^[a-zA-Z0-9 _.-]*$/'
      //pl : any kind of letter from any language
      //s : space

    ]);
  }

  public function checkDescription(Request $request)
  {
    $this->validate($request, [
      'description' => 'required'
    ]);
  }

  public function checkLocation(Request $request)
  {
    $this->validate($request, [
      'location' => 'required'
    ]);
  }

  public function checkDate(Request $request)
  {
    $this->validate($request, [
      'startEvent' => 'required',
      'endEvent' => 'required|after:startEvent'
    ]);
  }

  //

  //RabbitMQ
  public static function sendXMLtoUUID(Event $event, string $type)
  {
    //Geting DateTimes in right format
    $now =  new DateTime("now", new DateTimeZone("Europe/Brussels"));
    $XSDate = $now->format(\DateTime::RFC3339);
    $startEvent = date_create_from_format("Y-m-d H:i:s", $event->startEvent)->format(\DateTime::RFC3339);
    $endEvent = date_create_from_format("Y-m-d H:i:s", $event->endEvent)->format(\DateTime::RFC3339);

    //Seting type to all cap
    $type = strtoupper($type);

    //XML/XSD paths
    $xmlPath = "XML-XSD/event.xml";
    $XSDPath = "XML-XSD/event.xsd";

    //Loading in the XML
    $xml = new \DOMDocument();
    $xml->load($xmlPath);

    //Changing Header Value
    $header = $xml->getElementsByTagName("header")[0];

    $header->getElementsByTagName("method")[0]->nodeValue = $type;
    $header->getElementsByTagName("timestamp")[0]->nodeValue = $XSDate;
    error_log($event->id);
    $header->getElementsByTagName("sourceEntityId")[0]->nodeValue = $event->id;

    //Changing Body values
    $body = $xml->getElementsByTagName("body")[0];

    $body->getElementsByTagName("name")[0]->nodeValue = $event->name;
    $body->getElementsByTagName("startEvent")[0]->nodeValue = $startEvent;
    $body->getElementsByTagName("endEvent")[0]->nodeValue = $endEvent;
    $body->getElementsByTagName("location")[0]->nodeValue = $event->location;
    $body->getElementsByTagName("description")[0]->nodeValue = $event->description;

    error_log($xml->saveXML());
    //Validate XML whit XSD
    if (!$xml->schemaValidate($XSDPath)) {
      $error = libxml_get_last_error();
      error_log($error);
      return false;
    }

    //Publish event to event queue
    ConsumerController::sendXMLToRabbitMQ($xml, "UUID");
    
    return true;
  }

  public static function storeRecievedEvent(\DOMDocument $doc)
  {
    $body = $doc->getElementsByTagName("body")[0];
    $header = $doc->getElementsByTagName("header")[0];
    $startEvent = date_create_from_format("Y-m-d\TH:i:s", $body->getElementsByTagName("startEvent")[0]->nodeValue);
    $endEvent = date_create_from_format("Y-m-d\TH:i:s", $body->getElementsByTagName("endEvent")[0]->nodeValue);

    $event = new Event([
      'name' => $body->getElementsByTagName("name")[0]->nodeValue,
      'user_id' => $header->getElementsByTagName("organiserSourceEntityId")[0]->nodeValue,
      'startEvent' =>  $startEvent,
      'endEvent' => $endEvent,
      'location' => $body->getElementsByTagName("location")[0]->nodeValue,
      'description' => $body->getElementsByTagName("description")[0]->nodeValue,
    ]);


    $event->save();



    return $event->id;
  }

  public static function updateRecievedEvent(\DOMDocument $doc)
  {
    $body = $doc->getElementsByTagName("body")[0];
    $header = $doc->getElementsByTagName("header")[0];
    $startEvent = date_create_from_format(\DateTime::RFC3339, $body->getElementsByTagName("startEvent")[0]->nodeValue);
    $endEvent = date_create_from_format(\DateTime::RFC3339, $body->getElementsByTagName("endEvent")[0]->nodeValue);
    $event = Event::find($header->getElementsByTagName("sourceEntityId")[0]->nodeValue);
    $updateEvent = [
      'name' => $body->getElementsByTagName("name")[0]->nodeValue,
      'startEvent' =>  $startEvent,
      'endEvent' => $endEvent,
      'location' => $body->getElementsByTagName("location")[0]->nodeValue,
      'description' => $body->getElementsByTagName("description")[0]->nodeValue,
    ];
    $event->update($updateEvent);
  }

  public static function deleteRecievedEvent(\DOMDocument $doc)
  {
    $header = $doc->getElementsByTagName("header")[0];
    $event = Event::find($header->getElementsByTagName("sourceEntityId")[0]->nodeValue);
    $event->delete();
  }
  public static function saveEvent(Request $request)
  {
    $event = new Event([
      'name' => $request->input('name'),
      'user_id' => $request->input('user_id'),
      'startEvent' => $request->input('startEvent'),
      'endEvent' => $request->input('endEvent'),
      'description' => $request->input('description'),
      'location' => $request->input('location')
    ]);
    $event->save();
    return $event;
  }

  public static function editEvent($id, Request $request)
  {
    $event = Event::find($id);
    $event->update($request->all());
    return $event;
  }

  public static function deleteEvent($id)
  {
    $event = Event::find($id);
    $event->delete();
    return $event;
  }
}

