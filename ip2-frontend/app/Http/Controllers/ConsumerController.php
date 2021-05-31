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
    const EVENTSUBSCRIBE = "eventSubscribe";

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
        try {
            $connection = new AMQPStreamConnection(env('RABBITMQ_HOST'), env('RABBITMQ_PORT'), env('RABBITMQ_USER'), env('RABBITMQ_PASSWORD'));
            $channel = $connection->channel();
        } catch (\Exception $e) {
            error_log("No connection");
        }


        $message->ack();
        $string = $message->getBody();


        $doc = new \DOMDocument();
        try {
            $doc->loadXML($string);
        } catch (\Exception $e) {
            error_log("Is not a valid XML");
            return;
        }


        //get type
        $type = $doc->documentElement->tagName;
        $XSD = "";
        switch ($doc->documentElement->tagName) {
            case ConsumerController::EVENT:
                $XSD = $eventXSD;
                break;
            case ConsumerController::USER:
                $XSD = $userXSD;
                break;
            case ConsumerController::EVENTSUBSCRIBE:
                $XSD = $eventSubscribeXSD;
                break;
            case "error":
                return;
        }
        if (!$doc->SchemaValidate($XSD)) {
            error_log("foutive xml");
            return;
        }
        
        $header = $doc->getElementsByTagName("header")[0];
        //Ignore own Message
        if ($header->getElementsByTagName("origin")[0]->nodeValue == "FrontEnd") return;
        //Check where it comse from
        if ($header->getElementsByTagName("origin")[0]->nodeValue != "UUID") {
            //reciefed from other app
            $header->getElementsByTagName("origin")[0]->nodeValue = "FrontEnd";
            $header->getElementsByTagName("sourceEntityId")[0]->nodeValue = "";
            if ($type == ConsumerController::EVENT) $header->getElementsByTagName("organiserSourceEntityId")[0]->nodeValue = "";
            $header->getElementsByTagName("timestamp")[0]->nodeValue = $XSDate;

            //Send to UUID
            $ROUTEKEY = "UUID";
            $data = new AMQPMessage($doc->saveXML());
            $channel->basic_publish($data, 'direct_logs', $ROUTEKEY);
        } else {
            //Got message from UUID
            //No sourceEntityId but filled UUID
            if ($header->getElementsByTagName("sourceEntityId")[0]->nodeValue == "" && $header->getElementsByTagName("UUID")[0]->nodeValue != "") {
                if ($header->getElementsByTagName("method")[0]->nodeValue == "CREATE" || $header->getElementsByTagName("method")[0]->nodeValue == "SUBSCRIBE") {

                    
                    //Message confirming it is not in our DB
                    //Set correct route
                    $ROUTEKEY = "UUID";

                    //Execute request on correct type (Create obj)
                    switch ($type) {
                        case ConsumerController::USER:
                            $id = UserController::storeRecievedUser($doc);
                            break;

                        case ConsumerController::EVENT:
                            $id = EventController::storeRecievedEvent($doc);
                            break;

                        case ConsumerController::EVENTSUBSCRIBE:
                            $id = EventSubscriberController::storeRecievedEventSubscribe($doc);
                            break;
                    }
                    $header->getElementsByTagName("sourceEntityId")[0]->nodeValue = $id;

                    //Update got saved to UUID Master
                } else if ($header->getElementsByTagName("method")[0]->nodeValue == "UPDATE" || $header->getElementsByTagName("method")[0]->nodeValue == "DELETE" || $header->getElementsByTagName("method")[0]->nodeValue == "UNSUBSCRIBE") {
                    //Set correct route
                    $ROUTEKEY = $type;
                    if($header->getElementsByTagName("method")[0]->nodeValue == "UNSUBSCRIBE"){
                        $ROUTEKEY = ConsumerController::EVENT;
                    }
                    $header->getElementsByTagName("sourceEntityId")[0]->nodeValue = "";
                    if ($type == ConsumerController::EVENT) $header->getElementsByTagName("organiserSourceEntityId")[0]->nodeValue = "";
                }
            } else if ($header->getElementsByTagName("sourceEntityId")[0]->nodeValue != "" && $header->getElementsByTagName("UUID")[0]->nodeValue != "") {

                if ($header->getElementsByTagName("method")[0]->nodeValue == "UPDATE") {


                    //Message confirming it is not in our DB
                    //Set correct route
                    $ROUTEKEY = "UUID";

                    //Execute request on correct type (Update obj)
                    switch ($type) {
                        case ConsumerController::USER:
                            UserController::updateRecievedUser($doc);
                            break;

                        case ConsumerController::EVENT:
                            EventController::updateRecievedEvent($doc);
                            break;

                        case ConsumerController::EVENTSUBSCRIBE:
                            EventSubscriberController::updateRecievedEventSubscibe($doc);
                            break;
                    }

                    //Create got saved to UUID Master
                } else if ($header->getElementsByTagName("method")[0]->nodeValue == "CREATE" || $header->getElementsByTagName("method")[0]->nodeValue == "SUBSCRIBE") {
                    //Message confirming it is saved in our DB
                    //Send to User queue
                    $header->getElementsByTagName("sourceEntityId")[0]->nodeValue = "";
                    if ($type == ConsumerController::EVENT) $header->getElementsByTagName("organiserSourceEntityId")[0]->nodeValue = "";

                    //Set correct route
                    $ROUTEKEY = $type;

                    if($header->getElementsByTagName("method")[0]->nodeValue == "SUBSCRIBE"){
                        $ROUTEKEY = ConsumerController::EVENT;
                    }
                } else if ($header->getElementsByTagName("method")[0]->nodeValue == "DELETE" || $header->getElementsByTagName("method")[0]->nodeValue == "UNSUBSCRIBE") {
                    //Message confirming it is not in our DB

                    //Execute request on correct type (Delete obj)
                    switch ($type) {
                        case ConsumerController::USER:
                            UserController::deleteRecievedUser($doc);
                            break;

                        case ConsumerController::EVENT:
                            EventController::deleteRecievedEvent($doc);
                            break;

                        case ConsumerController::EVENTSUBSCRIBE:
                            EventSubscriberController::deleteRecievedEventSubscibe($doc);
                            break;
                    }
                    //Dont send if deleted
                    return;
                }
            } else if ($header->getElementsByTagName("sourceEntityId")[0]->nodeValue != "" && $header->getElementsByTagName("UUID")[0]->nodeValue == "") {
                //Delete got saved in UUID Master
                $header->getElementsByTagName("sourceEntityId")[0]->nodeValue = "";
                if ($type == ConsumerController::EVENT) $header->getElementsByTagName("organiserSourceEntityId")[0]->nodeValue = "";
                $ROUTEKEY = $type;
            }

            //Send to RabbitMQ
            $header->getElementsByTagName("origin")[0]->nodeValue = "FrontEnd";
            $header->getElementsByTagName("timestamp")[0]->nodeValue = $XSDate;
            $data = new AMQPMessage($doc->saveXML());
            $channel->basic_publish($data, 'direct_logs', $ROUTEKEY);
        }
    }
}
