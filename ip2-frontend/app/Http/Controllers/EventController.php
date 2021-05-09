<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use DateTime;

class EventController extends Controller
{
    //
    public function index()
    {
        $events = Event::orderBy('startsAt', 'desc')->paginate(25)->toArray();
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
        //$this->publishToEventQueue($event, "create");
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

    public function publishToEventQueue(Event $event,string $type){
        $now =  new DateTime("now");
        $XSDate = $now->format(\DateTime::RFC3339);
        error_log($event->description);
        $xsd = '<?xml version="1.0" encoding="utf-8"?> 

        <xs:schema attributeFormDefault="unqualified" elementFormDefault="qualified"
            xmlns:xs="http://www.w3.org/2001/XMLSchema">
          <xs:element name="event">
            <xs:complexType>
              <xs:sequence>
                <xs:element name="header">
                  <xs:complexType>
                    <xs:sequence>
                      <xs:element name="UUID">
                        <xs:simpleType>
                          <xs:restriction base="xs:string">
                            <xs:pattern value="[0-9A-Fa-f]{8}-?([0-9A-Fa-f]{4}-?){3}[0-9A-Fa-f]{12}" />
                          </xs:restriction>
                        </xs:simpleType>
                      </xs:element>
                      <xs:element name="organiserUUID">
                        <xs:simpleType>
                          <xs:restriction base="xs:string">
                            <xs:pattern value="[0-9A-Fa-f]{8}-?([0-9A-Fa-f]{4}-?){3}[0-9A-Fa-f]{12}" />
                          </xs:restriction>
                        </xs:simpleType>
                      </xs:element>
                      <xs:element name="method">
                        <xs:simpleType>
                          <xs:restriction base="xs:string">
                            <xs:pattern value="(CREATE|UPDATE|DELETE)" />
                          </xs:restriction>
                        </xs:simpleType>
                      </xs:element>
                      <xs:element name="origin">
                        <xs:simpleType>
                          <xs:restriction base="xs:string">
                            <xs:pattern value="(AD|FrontEnd|Canvas|Monitor|Office|Control)" />
                          </xs:restriction>
                        </xs:simpleType>
                      </xs:element>
                      <xs:element name="timestamp" type="xs:dateTime" />
                    </xs:sequence>
                  </xs:complexType>
                </xs:element>
                <xs:element name="body">
                  <xs:complexType>
                    <xs:sequence>
                      <xs:element name="agenda">
                        <xs:simpleType>
                          <xs:restriction base="xs:string">
                            <xs:maxLength value="64" />
                          </xs:restriction>
                        </xs:simpleType>
                      </xs:element>
                      <xs:element name="name">
                        <xs:simpleType>
                          <xs:restriction base="xs:string">
                            <xs:pattern value="[a-zA-Z0-9àáâäãåąčćęèéêëėįìíîïłńòóôöõøùúûüųūÿýżźñçčšžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹÑßÇŒÆČŠŽ∂ð ,.\'-]{1,32}" />
                          </xs:restriction>
                        </xs:simpleType>
                      </xs:element>
                      <xs:element name="startEvent" type="xs:dateTime" />
                      <xs:element name="endEvent" type="xs:dateTime" />
                      <xs:element name="description">
                        <xs:simpleType>
                          <xs:restriction base="xs:string">
                            <xs:maxLength value="2048" />
                          </xs:restriction>
                        </xs:simpleType>
                      </xs:element>
                      <xs:element name="location">
                        <xs:simpleType>
                          <xs:restriction base="xs:string">
                            <xs:maxLength value="64" />
                          </xs:restriction>
                        </xs:simpleType>
                      </xs:element>
                      <xs:element name="guests">
                        <xs:simpleType>
                          <xs:restriction base="xs:string">
                            <xs:maxLength value="64" />
                          </xs:restriction>
                        </xs:simpleType>
                      </xs:element>
                    </xs:sequence>
                  </xs:complexType>
                </xs:element>
              </xs:sequence>
            </xs:complexType>
          </xs:element>
        </xs:schema>';
        $type = strtoupper($type);
        $msg = "<?xml version=\"1.0\" encoding=\"utf-8\"?>
        <event>
          <header>
            <UUID>5698cd59-3acc-4f15-9ce2-83545cbfe0ba</UUID>
            <organiserUUID>333ade47-03d1-40bb-9912-9a6c86a60169</organiserUUID>
            <method>$type</method>
            <origin>FrontEnd</origin>
            <timestamp>$XSDate</timestamp>
          </header>
          <body>
            <agenda>Agenda</agenda>
            <name>{$event->name}</name>
            <startEvent>{$XSDate}</startEvent>
            <endEvent>{$XSDate}</endEvent>
            <description>Test</description>
            <location>{$event->location}</location>
            <guests>mroreoehb@outlook.com</guests>
          </body>
        </event>";
        error_log($msg);
        $xml = new \DOMDocument();
        $xml->loadXML($msg);
        if (!$xml->schemaValidateSource($xsd)) {
            $error = libxml_get_last_error();
            error_log($error);
            return false;
        }

        $connection = new AMQPStreamConnection('10.3.56.6', 5672, 'guest', 'guest');
        $channel = $connection->channel();
        $ROUTEKEY = "event";
        $data = new AMQPMessage($msg);
        $channel->basic_publish($data, 'direct_logs', $ROUTEKEY);
        echo ' [x] Sent ', $ROUTEKEY, ':', $msg, "\n";
    }
}
