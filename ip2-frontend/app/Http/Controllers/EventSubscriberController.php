<?php

namespace App\Http\Controllers;

use App\Models\EventSubscriber;
use App\Models\Event;

use DateTime;
use DateTimeZone;
use Illuminate\Http\Request;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class EventSubscriberController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $eventSubscriber = EventSubscriber::all()->toArray();
        return array_reverse($eventSubscriber);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //

        $eventSubscriber = new EventSubscriber([
            'user_id' => env('USER_ID_SUB'),
            'event_id' => $request->input('event_id')
        ]);
        $eventSubscriber->save();

        if (EventSubscriberController::sendXMLtoUUID($eventSubscriber, "subscribe")) {
            return response()->json('EventSubscriber created!');
        }
        return response()->json('EventSubscriber creation failed');

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\EventSubscriber  $eventSubscriber
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $eventSubscribers = DB::table('users')
        ->join('event_subscribers', 'users.id', '=','event_subscribers.user_id')
        ->select('event_subscribers.id','users.firstName', 'users.lastName')
        ->where('event_id','=',$id)->get();

        return response()->json($eventSubscribers);
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\EventSubscriber  $eventSubscriber
     * @return \Illuminate\Http\Response
     */
    public function edit(EventSubscriber $eventSubscriber)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\EventSubscriber  $eventSubscriber
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EventSubscriber $eventSubscriber)
    {
        //
    }

    public function checkIfSubscribed(Request $request){

        $user_id = $request->input('user_id');
        $event_id = $request->input('event_id');
        $subscriber = DB::table('event_subscribers')->where('user_id','=',$user_id)->where('event_id','=',$event_id)->get();
        error_log($subscriber);
        if($subscriber->count()==0)
        {
            return 0;
        }
        else
        {
            return 1;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\EventSubscriber  $eventSubscriber
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $user_id = $request->input('user_id');
        $event_id = $request->input('event_id');
        $eventSubscriber= EventSubscriber::where('user_id','=',$user_id)->where('event_id','=',$event_id)->first();


        if (EventSubscriberController::sendXMLtoUUID($eventSubscriber, "unsubscribe")) {
            DB::table('event_subscribers')->where('user_id','=',$user_id)->where('event_id','=',$event_id)->delete();
            return response()->json('EventSubscriber deleted');
        }
        return response()->json('EventSubscriber deletion failed');
    }

    public static function deletion($id)
    {

        $eventSubscriber= EventSubscriber::find($id);


        if (EventSubscriberController::sendXMLtoUUID($eventSubscriber, "unsubscribe")) {
            $eventSubscriber->delete();
            return response()->json('EventSubscriber deleted');
        }
        return response()->json('EventSubscriber deletion failed');
    }

    public function sendXMLtoUUID(EventSubscriber $eventSubscriber, string $type)
    {
        //Seting type to all cap
        $type = strtoupper($type);

        //XML/XSD paths
        $xmlPath = "XML-XSD/eventSubscribe.xml";
        $XSDPath = "XML-XSD/eventSubscribe.xsd";

        //Loading in the XML
        $xml = new \DOMDocument();
        $xml->load($xmlPath);

        //Changing Header Value
        $header = $xml->getElementsByTagName("header")[0];

        $header->getElementsByTagName("method")[0]->nodeValue = $type;
        $header->getElementsByTagName("sourceEntityId")[0]->nodeValue = $eventSubscriber->id;

        //Changing Body values
        $body = $xml->getElementsByTagName("body")[0];
        $body->getElementsByTagName("attendeeSourceEntityId")[0]->nodeValue = $eventSubscriber->user_id;
        $body->getElementsByTagName("eventSourceEntityId")[0]->nodeValue = $eventSubscriber->event_id;

        //Publish event to event queue
        ConsumerController::sendXMLToRabbitMQ($xml, "UUID");
        return true;
    }

    public static function storeRecievedEventSubscribe(\DOMDocument $doc)
    {

        $body = $doc->getElementsByTagName("body")[0];
        $eventSubscriber = new EventSubscriber([
            'user_id' => $body->getElementsByTagName("attendeeSourceEntityId")[0]->nodeValue,
            'event_id' => $body->getElementsByTagName("eventSourceEntityId")[0]->nodeValue
        ]);
        $eventSubscriber->save();

        return $eventSubscriber->id;
    }

    public static function updateRecievedEventSubscibe(\DOMDocument $doc)
    {
        $body = $doc->getElementsByTagName("body")[0];

        $EventSubscriber = EventSubscriber::find($body->getElementsByTagName("sourceEntityId")[0]);
        $newEventSubscriber = [
            'user_id' => $body->getElementsByTagName("attendeeSourceEntityId")[0]->nodeValue,
            'event_id' => $body->getElementsByTagName("eventSourceEntityId")[0]->nodeValue
        ];
        $EventSubscriber->update($newEventSubscriber);
    }

    public static function deleteRecievedEventSubscibe(\DOMDocument $doc)
    {
        $header = $doc->getElementsByTagName("header")[0];
        $EventSubscriber = EventSubscriber::find($header->getElementsByTagName("sourceEntityId")[0]->nodeValue);
        $EventSubscriber->delete();
    }
}
