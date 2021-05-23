<?php

namespace App\Http\Controllers;

use DateTime;
use DateTimeZone;
use Illuminate\Http\Request;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class ConsumerController extends Controller
{
    const EVENT = "event";
    const USER = "user";
    const EVENTSUBSCRIBE = "eventsubcribe";

    public static function recievemssg(AMQPMessage $message)
    {

        //XSD's paths
        $userXSD = "public/XML-XSD/user.xsd";
        $eventXSD = "public/XML-XSD/event.xsd";
        $eventSubscribeXSD = "public/XML-XSD/eventSubscribe.xsd";


        //Get current time
        $now =  new DateTime("now", new DateTimeZone("Europe/Brussels"));
        $XSDate = $now->format(\DateTime::RFC3339);

        //Def RabbitMQ connection
        $connection = new AMQPStreamConnection(env('RABBITMQ_HOST'), env('RABBITMQ_PORT'), env('RABBITMQ_USER'), env('RABBITMQ_PASSWORD'));
        $channel = $connection->channel();

        $message->ack();
        $string = $message->getBody();



        $doc = new \DOMDocument();
        $doc->loadXML($string);
        
        //get type
        $type = $doc->documentElement->tagName;
        $XSD = "";
        switch($doc->documentElement->tagName){
            case ConsumerController::EVENT:
                $XSD = $eventXSD;
                break;
            case ConsumerController::USER:
                $XSD = $userXSD;
                break;
            case ConsumerController::EVENTSUBSCRIBE:
                $XSD = $eventSubscribeXSD;
                break;
        }

        if ($doc->SchemaValidate($XSD)) {
            $header = $doc->getElementsByTagName("header")[0];
            //Ignore own Message
            if ($header->getElementsByTagName("origin")[0]->nodeValue == "FrontEnd") return;

            //Check where it comse from
            if ($header->getElementsByTagName("origin")[0]->nodeValue != "UUID") {
                //reciefed from other app
                $header->getElementsByTagName("origin")[0]->nodeValue = "FrontEnd";
                $header->getElementsByTagName("sourceEntityId")[0]->nodeValue = "";
                $header->getElementsByTagName("timestamp")[0]->nodeValue = $XSDate;

                //Send to UUID
                $ROUTEKEY = "UUID";
                $data = new AMQPMessage($doc->saveXML());
                $channel->basic_publish($data, 'direct_logs', $ROUTEKEY);
            } else {
                //Got message from UUID
                //No sourceEntityId but filled UUID
                if ($header->getElementsByTagName("sourceEntityId")[0]->nodeValue == "" && $header->getElementsByTagName("UUID")[0]->nodeValue != "") {
                    //Message confirming it is not in our DB
                    //Set correct route
                    $ROUTEKEY = "UUID";

                    //Execute request on correct type (Create obj, Update obj or Delete obj)
                    switch ($type) {
                        case ConsumerController::USER:
                            switch ($header->getElementsByTagName("method")[0]->nodeValue) {
                                case "CREATE":
                                    $id = UserController::storeRecievedUser($doc);
                                    $header->getElementsByTagName("sourceEntityId")[0]->nodeValue = $id;
                                    break;
                                case "UPDATE":
                                    //TODO Execute an user update
                                    break;
                                case "DELETE":
                                    //TODO Execute an user delete
                                    break;
                            }
                            break;

                        case ConsumerController::EVENT:
                            switch ($header->getElementsByTagName("method")[0]->nodeValue) {
                                case "CREATE":
                                    $id = EventController::storeRecievedEvent($doc);
                                    $header->getElementsByTagName("sourceEntityId")[0]->nodeValue = $id;
                                    break;
                                case "UPDATE":
                                    //TODO Execute an event update
                                    break;
                                case "DELETE":
                                    //TODO Execute an event delete
                                    break;
                            }
                            break;


                        case ConsumerController::EVENTSUBSCRIBE:
                            switch ($header->getElementsByTagName("method")[0]->nodeValue) {
                                case "CREATE":
                                    //TODO Store en eventSubscribe you recieved
                                    break;
                                case "UPDATE":
                                    //TODO Execute an eventSubscribe update
                                    break;
                                case "DELETE":
                                    //TODO Execute an eventSubscribe delete
                                    break;
                            }
                            break;
                    }
                } else if ($header->getElementsByTagName("sourceEntityId")[0]->nodeValue != "" && $header->getElementsByTagName("UUID")[0]->nodeValue != "") {
                    //Message confirming it is saved in our DB
                    //Send to User queue
                    $header->getElementsByTagName("sourceEntityId")[0]->nodeValue = "";

                    //Set correct route
                    $ROUTEKEY = $type;
                }

                $header->getElementsByTagName("origin")[0]->nodeValue = "FrontEnd";
                $header->getElementsByTagName("timestamp")[0]->nodeValue = $XSDate;
                $data = new AMQPMessage($doc->saveXML());
                $channel->basic_publish($data, 'direct_logs', $ROUTEKEY);
            }
        } else {
            error_log("foutive xml");
        }
    }
}
