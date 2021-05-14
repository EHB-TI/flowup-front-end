<?php

namespace App\Http\Controllers;
use PhpAmqpLib\Message\AMQPMessage;
use Illuminate\Http\Request;
use App\Models\Event;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use DateTime;
use DateTimeZone;
use Exception;

class EventController extends Controller
{
    //
    public function index()
    {
        $events = Event::all()->toArray();
        return array_reverse($events);
    }

    public function store(Request $request)
    {
        $event = new Event([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'startsAt' => $request->input('startsAt'),
            'endsAt' => $request->input('endsAt'),            
            'location' => $request->input('location')
        ]);

        if($this->publishToEventQueue($event, "create")){
          $event->save();
          return response()->json('Event created!');
        }
        return response()->json('Event creation failed!');
    }

    public function show($id)
    {
        $event = Event::find($id);
        return response()->json($event);
    }

    public function update($id, Request $request)
    {
        $event = Event::find($id);

        if($this->publishToEventQueue($event, "update")){
          $event->update($request->all());
          return response()->json('Event updated!');
        }
        return response()->json('Event update failed!');
    }

    public function destroy($id)
    {
        $event = Event::find($id);
        $event->delete();

        return response()->json('Event deleted');
    }

    public function publishToEventQueue(Event $event,string $type){
      //Geting DateTimes in right format
        $now =  new DateTime("now",new DateTimeZone("Europe/Brussels"));
        $XSDate = $now->format(\DateTime::RFC3339);
        $startsAt = date_create_from_format("Y-m-d H:i:s",$event->startsAt)->format(\DateTime::RFC3339);
        $endsAt = date_create_from_format("Y-m-d H:i:s",$event->endsAt)->format(\DateTime::RFC3339);

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

        //Changing Body values
        $body = $xml->getElementsByTagName("body")[0];

        $body->getElementsByTagName("name")[0]->nodeValue = $event->name;
        $body->getElementsByTagName("startEvent")[0]->nodeValue = $startsAt;
        $body->getElementsByTagName("endEvent")[0]->nodeValue = $endsAt;
        $body->getElementsByTagName("location")[0]->nodeValue = $event->location;
        $body->getElementsByTagName("description")[0]->nodeValue = $event->description;
        
        //Validate XML whit XSD
        if (!$xml->schemaValidate($XSDPath)) {
            $error = libxml_get_last_error();
            error_log($error);
            return false;
        }

        //Publish event to event queue
        error_log($xml->saveXML());
        $ROUTEKEY = "event";
        $connection = new AMQPStreamConnection('10.3.56.6', 5672, 'guest', 'guest');
        $channel = $connection->channel();
        
        $data = new AMQPMessage($xml->saveXML());
        $channel->basic_publish($data, 'direct_logs', $ROUTEKEY);
        return true;
    }
     public static function recieveEvent(AMQPMessage $message){
        $message->ack();
        $string = $message->getBody();
        $doc = new \DOMDocument();
        $doc->loadXML($string);
        $XSDPath = "public/XML-XSD/event.xsd";
        if($doc->SchemaValidate($XSDPath)){
            $body = $doc->getElementsByTagName("body")[0];
             $event = new Event([
                'name' => $body->getElementsByTagName("name")[0]->nodeValue, 
                'startsAt' => $body->getElementsByTagName("startEvent")[0]->nodeValue,
                'endsAt' => $body->getElementsByTagName("endEvent")[0]->nodeValue,
                'location' => $body->getElementsByTagName("location")[0]->nodeValue,
                'description' => $body->getElementsByTagName("description")[0]->nodeValue,
            ]);
           

            $event->save();
        }else{
            error_log('error');
        }
    }
}
