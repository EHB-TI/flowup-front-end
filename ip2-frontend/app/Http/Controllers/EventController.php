<?php

namespace App\Http\Controllers;
use PhpAmqpLib\Message\AMQPMessage;
use Illuminate\Http\Request;
use App\Models\Event;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use DateTime;
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
        $event->save();
        $this->publishToEventQueue($event, "create");
        return response()->json('Event created!');
    }

    public function show($id)
    {
        $event = Event::find($id);
        return response()->json($event);
    }

    public function update($id, Request $request)
    {
        $event = Event::find($id);
        $event->update($request->all());

        return response()->json('Event updated!');
    }

    public function destroy($id)
    {
        $event = Event::find($id);
        $event->delete();

        return response()->json('Event deleted');
    }

    public static function recieveEvent(AMQPMessage $message){
        error_log('in controller');
        $message->ack();
        $string = $message->getBody();
        $doc = new \DOMDocument();
        $doc->loadXML($string);
        $xsd = "XML-XSD/event.xsd";
        $bool = $doc->SchemaValidate($xsd);
        if($bool){
            $event = new Event([
                'name' => $doc->body->name,
                'startsAtDate' => $doc->body->startsAtDate,
                'startsAtTime' => $doc->body->startsAtTime,
                'endsAtDate' => $doc->body->endsAtDate,
                'endsAtTime' => $doc->body->endsAtTime,
                'location' => $doc->body->location
            ]);

            $event->save();
        }else{
            error_log('error');
        }
    }
}
