<?php

namespace App\Http\Controllers;

use App\Models\EventSubscriber;
use App\Models\Event;
use DateTime;
use DateTimeZone;
use Illuminate\Http\Request;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

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
            'user_id' => $request->input('user_id'),
            'event_id' => $request->input('event_id')
        ]);
        $eventSubscriber->save();

        if (EventSubscriberController::sendXMLtoUUID($eventSubscriber, "create")) {
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
        //
        $eventSubscribers = EventSubscriber::Where('event_id', '=', $id)->get();
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\EventSubscriber  $eventSubscriber
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $eventSubscriber = EventSubscriber::find($id);


        if (EventSubscriberController::sendXMLtoUUID($eventSubscriber, "delete")) {
            $eventSubscriber->delete();
            return response()->json('EventSubscriber deleted');
        }
        return response()->json('EventSubscriber deletion failed');
    }

    public function sendXMLtoUUID(EventSubscriber $eventSubscriber, string $type)
    {
        //Geting DateTimes in right format
        $now =  new DateTime("now", new DateTimeZone("Europe/Brussels"));
        $XSDate = $now->format(\DateTime::RFC3339);

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
        $body->getElementsByTagName("attendeeSourceEntityId")[0]->nodeValue =   $eventSubscriber->user_id;
        $body->getElementsByTagName("eventSourceEntityId")[0]->nodeValue =   $eventSubscriber->event_id;

        //Validate XML whit XSD
        if (!$xml->schemaValidate($XSDPath)) {
            $error = libxml_get_last_error();
            error_log($error);
            return false;
        }

        //Publish event to event queue
        error_log($xml->saveXML());
        $ROUTEKEY = "UUID";
        $connection = new AMQPStreamConnection(env('RABBITMQ_HOST'), env('RABBITMQ_PORT'), env('RABBITMQ_USER'), env('RABBITMQ_PASSWORD'));
        $channel = $connection->channel();

        $data = new AMQPMessage($xml->saveXML());
        $channel->basic_publish($data, 'direct_logs', $ROUTEKEY);
        return true;
    }

    public static function storeRecievedEventSubscribe(\DOMDocument $doc)
    {

        $body = $doc->getElementsByTagName("body")[0];
        $eventSubscriber = new EventSubscriber([
            'user_id' => $body->getElementsByTagName("attendeeSourceEntityId")[0],
            'event_id' => $body->getElementsByTagName("eventSourceEntityId")[0]
        ]);
        $eventSubscriber->save();

        return $eventSubscriber->id;
    }

    public static function updateRecievedEventSubscibe(\DOMDocument $doc)
    {
        $body = $doc->getElementsByTagName("body")[0];

        $EventSubscriber = EventSubscriber::find($body->getElementsByTagName("sourceEntityId")[0]);
        $newEventSubscriber = [
            'user_id' => $body->getElementsByTagName("attendeeSourceEntityId")[0],
            'event_id' => $body->getElementsByTagName("eventSourceEntityId")[0]
        ];
        $EventSubscriber->update($newEventSubscriber);
    }

    public static function deleteRecievedEventSubscibe(\DOMDocument $doc)
    {
        $header = $doc->getElementsByTagName("header")[0];
        $EventSubscriber = EventSubscriber::find($header->getElementsByTagName("sourceEntityId")[0]);
        $EventSubscriber->delete();
    }
}
