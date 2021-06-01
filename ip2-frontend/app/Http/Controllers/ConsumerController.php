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
        $message->ack();
        $string = $message->getBody();
        
        try{
        $rabbitMQinfo = ConsumerController::consumerHandeling($string);
        } catch(\Exception $e){
            error_log($e);
        }
        If($rabbitMQinfo == null){
        }
        error_log($rabbitMQinfo["xml"]);
        //Send to RabbitMQ
        if($rabbitMQinfo != null){
            ConsumerController::sendXMLToRabbitMQ($rabbitMQinfo["xml"], $rabbitMQinfo["route"]);
        }
    }


    public static function errorLoggingToMonitoring($errorInfo)
    {

        
        $xml = ConsumerController::errorArrayToXML($errorInfo);
        //Publish event to event queue
        ConsumerController::sendXMLToRabbitMQ($xml, "logging");
        return true;
    }

    public static function sendXMLToRabbitMQ(\DOMDocument $doc, $routkey)
    {
        //Get current time
        $now =  new DateTime("now", new DateTimeZone("Europe/Brussels"));
        $XSDate = $now->format(\DateTime::RFC3339);

        //Set default header information
        $header = $doc->getElementsByTagName("header")[0];
        $header->getElementsByTagName("origin")[0]->nodeValue = "FrontEnd";
        $header->getElementsByTagName("timestamp")[0]->nodeValue = $XSDate;

        //Make connection to RabbitMQ
        $connection = new AMQPStreamConnection(env('RABBITMQ_HOST'), env('RABBITMQ_PORT'), env('RABBITMQ_USER'), env('RABBITMQ_PASSWORD'));
        $channel = $connection->channel();

        //Send xml to RabbitMQ
        $data = new AMQPMessage($doc->saveXML());
        $channel->basic_publish($data, 'direct_logs', $routkey);
    }

    public static function consumerHandeling($xmlstring)
    {
        //XSD's paths
        $userXSD = "public/XML-XSD/user.xsd";
        $eventXSD = "public/XML-XSD/event.xsd";
        $eventSubscribeXSD = "public/XML-XSD/eventSubscribe.xsd";

        $doc = new \DOMDocument();
        try {
            $doc->loadXML($xmlstring);
        } catch (\Exception $e) {
            ConsumerController::errorLoggingToMonitoring(["code" => 1000]);
            error_log("Is not a valid XML");
            return ["error" => "Is not a valid XML"];
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
                ConsumerController::sendXMLToRabbitMQ($doc, "logging");
                return ["xml" => $doc->saveXML];
        }

        if (!$doc->SchemaValidate($XSD)) {
            //Send Error to RabbitMQ
            ConsumerController::errorLoggingToMonitoring([
                "code" => 1002,
                "objectOrigin" => $doc->getElementsByTagName("header")[0]->getElementsByTagName("origin")[0]->nodeValue,
                "objectUUID" => $doc->getElementsByTagName("header")[0]->getElementsByTagName("UUID")[0]->nodeValue,
                "objectSourceId" => $doc->getElementsByTagName("header")[0]->getElementsByTagName("UUID")[0]->nodeValue
            ]);
            error_log("foutive xml");
            return ["error" => "foutive xml"] ;
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

            //Send to UUID
            $ROUTEKEY = "UUID";
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
                    if ($header->getElementsByTagName("method")[0]->nodeValue == "UNSUBSCRIBE") {
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

                    if ($header->getElementsByTagName("method")[0]->nodeValue == "SUBSCRIBE") {
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
                    return ["mssg" => "No xml needs to be sended"];
                }
            } else if ($header->getElementsByTagName("sourceEntityId")[0]->nodeValue != "" && $header->getElementsByTagName("UUID")[0]->nodeValue == "") {
                //Delete got saved in UUID Master
                $header->getElementsByTagName("sourceEntityId")[0]->nodeValue = "";
                if ($type == ConsumerController::EVENT) $header->getElementsByTagName("organiserSourceEntityId")[0]->nodeValue = "";
                $ROUTEKEY = $type;
            }
        }

        return [
            "xml" => $doc,
            "route" => $ROUTEKEY
        ];
    }
    
    public static function errorArrayToXML($errorInfo)
    {
        $now =  new DateTime("now", new DateTimeZone("Europe/Brussels"));
        $XSDate = $now->format(\DateTime::RFC3339);

        //XML/XSD paths
        $xmlPath = "public/XML-XSD/error.xml";
        $XSDPath = "public/XML-XSD/error.xsd";

        $description = "";

        //Loading in the XML
        $xml = new \DOMDocument();
        $xml->load($xmlPath);

        //Changing Header Value
        $header = $xml->getElementsByTagName("header")[0];

        $header->getElementsByTagName("code")[0]->nodeValue = $errorInfo["code"];
        $header->getElementsByTagName("timestamp")[0]->nodeValue = $XSDate;

        //Changing Body values
        $body = $xml->getElementsByTagName("body")[0];

        if(array_key_exists ("objectUUID",$errorInfo)) $body->getElementsByTagName("objectUUID")[0]->nodeValue = $errorInfo["objectUUID"];
        if(array_key_exists ("objectSourceId",$errorInfo)) $body->getElementsByTagName("objectSourceId")[0]->nodeValue = $errorInfo["objectSourceId"];
        $body->getElementsByTagName("objectOrigin")[0]->nodeValue = (array_key_exists ("objectOrigin",$errorInfo)) ? $errorInfo["objectOrigin"] : "None";

        switch ($errorInfo["code"]) {
            case "1000":
                $description = " Incorrect message, couldn't parse XML";
                break;
            case "1001":
                $description = "Incorrect message, object doesn't exists or is empty";
                break;
            case "1002":
                $description = "Incorrect message, incorrect XML";
                break;
            case "1003":
                $description = "Incorrect message, method should be CREATE/SUBSCRIBE/UPDATE/DELETE/UNSUBSCRIBE";
                break;
            case "1004":
                $description = "Incorrect message, sourceEntityId and UUID connot both be empty";
                break;
            case "1005":
                $description = "Incorrect message, sourceEntityId and UUID connot both be filled on delete";
                break;
            case "2000":
                $description = "Something went wrong when adding to the UUID master, DB error:";
                break;
            default:
                throw new \Exception("Invalid error code");
            break;
        }
        $body->getElementsByTagName("description")[0]->nodeValue = $description;

        //Validate XML whit XSD
        if (!$xml->schemaValidate($XSDPath)) {
            $error = libxml_get_last_error();
            error_log($error);
            return false;
        }
        return $xml;
    }
}
