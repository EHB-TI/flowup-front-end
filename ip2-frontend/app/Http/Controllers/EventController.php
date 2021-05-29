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
        $events = Event::orderBy('startEvent', 'desc')->paginate(25)->toArray();
        return array_reverse($events);
    }

    public function store(Request $request)
    {
        $event = new Event([
            'name' => $request->input('name'),
            'user_id' => $request->input('user_id'),
            'startEvent' => $request->input('startEvent'),
            'endEvent' => $request->input('endEvent'),     
            'description' => $request->input('description'),       
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

    public function showByUser($id)
    {
        $event = Event::where('user_id', '=', $id)->get();
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

    //ErrorHandling
    public function checkName(Request $request)
    {
      $this->validate($request, [
        'name' => 'required|max:30'
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
        'endEvent' => 'required'
      ]);
    }

    //

    //RabbitMQ
    public function publishToEventQueue(Event $event,string $type){
      //Geting DateTimes in right format
        $now =  new DateTime("now",new DateTimeZone("Europe/Brussels"));
        $XSDate = $now->format(\DateTime::RFC3339);
        $startEvent = date_create_from_format("Y-m-d H:i:s",$event->startEvent)->format(\DateTime::RFC3339);
        $endEvent = date_create_from_format("Y-m-d H:i:s",$event->endEvent)->format(\DateTime::RFC3339);

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
        $body->getElementsByTagName("startEvent")[0]->nodeValue = $startEvent;
        $body->getElementsByTagName("endEvent")[0]->nodeValue = $endEvent;
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
        $connection = new AMQPStreamConnection(env('RABBITMQ_HOST'), env('RABBITMQ_PORT'), env('RABBITMQ_USER'), env('RABBITMQ_PASSWORD'));
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
            $startEvent = date_create_from_format(\DateTime::RFC3339,$body->getElementsByTagName("startEvent")[0]->nodeValue);
            $endEvent = date_create_from_format(\DateTime::RFC3339,$body->getElementsByTagName("endEvent")[0]->nodeValue);
            
            $event = new Event([
                'name' => $body->getElementsByTagName("name")[0]->nodeValue, 
                'user_id' => 9,
                'startEvent' =>  $startEvent,
                'endEvent' => $endEvent,
                'startEvent' => $body->getElementsByTagName("startEvent")[0]->nodeValue,
                'endEvent' => $body->getElementsByTagName("endEvent")[0]->nodeValue,
                'location' => $body->getElementsByTagName("location")[0]->nodeValue,
                'description' => $body->getElementsByTagName("description")[0]->nodeValue,
            ]);

            error_log('got here');
           

            $event->save();
        }else{
            error_log('error');
        }
    }
}
