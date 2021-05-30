<?php

namespace App\Http\Controllers;

use DateTime;
use DateTimeZone;
use Illuminate\Http\Request;

class StatusController extends Controller
{
  public function GetMssg()
  {

    //Code from: https://stackoverflow.com/questions/1455379/get-server-ram-with-php
    $fh = fopen('/proc/meminfo', 'r');
    $mem = 0;
    while ($line = fgets($fh)) {
      $pieces = array();
      if (preg_match('/^MemTotal:\s+(\d+)\skB$/', $line, $pieces)) {
        $mem = $pieces[1];
        break;
      }
    }
    fclose($fh);

    //End Code from github

    // getting necessary data
    $RamUsage = 100/$mem*memory_get_usage();
    
    $CPUload = array_sum(sys_getloadavg()) / count(sys_getloadavg());
    $now =  new DateTime("now",new DateTimeZone("Europe/Brussels"));
    $XSDate = $now->format(\DateTime::RFC3339);
    $error = null;

    //XML/XSD paths
    $xsdPath  = "XML-XSD/heartbeat.xsd";
    $xmlPath = "XML-XSD/heartbeat.xml";


    //Loading the XML
    $xml = new \DOMDocument();
    $xml->load($xmlPath);

    //Change Header values
    $header = $xml->getElementsByTagName("header")[0];

    $header->getElementsByTagName("timestamp")[0]->nodeValue = $XSDate;

    //Change Header values
    $body = $xml->getElementsByTagName("body")[0];

    $body->getElementsByTagName("CPUload")[0]->nodeValue = $CPUload;
    $body->getElementsByTagName("RAMload")[0]->nodeValue = $RamUsage;
   
    //Validation of the XML
    if (!$xml->schemaValidate($xsdPath)) {
      $error = libxml_get_last_error();
    }

    //Return the message
    error_log($xml->saveXML());
    return response()->json([
      'msg' => $xml->saveXML(),
      'error' => $error,
    ]);
  }
}
