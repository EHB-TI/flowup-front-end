<?php

namespace App\Http\Controllers;

use DateTime;
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

    //End Code from stackoverflow


    $RamUsage = 100/$mem*memory_get_usage();

    $CPUload = array_sum(sys_getloadavg()) / count(sys_getloadavg());
    $now =  new DateTime("now");
    $XSDate = $now->format(\DateTime::RFC3339);
    $error = null;
    //#region XSD
    $xsd = '<?xml version="1.0" encoding="utf-8"?>

        <xs:schema attributeFormDefault="unqualified" elementFormDefault="qualified" xmlns:xs="http://www.w3.org/2001/XMLSchema">
          <xs:element name="heartbeat">
            <xs:complexType>
              <xs:sequence>
                <xs:element name="header">
                  <xs:complexType>
                    <xs:sequence>
                      <xs:element name="code">
                          <xs:simpleType>
                              <xs:restriction base="xs:string">
                                    <xs:pattern value="[0-9]{1,6}" />
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
                      <xs:element name="nameService">
                        <xs:simpleType>
                            <xs:restriction base="xs:string">
                               <xs:pattern value="[a-zA-Z -]{1,16}" />
                             </xs:restriction>
                         </xs:simpleType>
                      </xs:element>
                      <xs:element name="CPUload" type="xs:double" />
                      <xs:element name="RAMload" type="xs:double" />
                    </xs:sequence>
                  </xs:complexType>
                </xs:element>
              </xs:sequence>
            </xs:complexType>
          </xs:element>
        </xs:schema>';
    //#endregion

    //#region XML string
    $msg = "<?xml version=\"1.0\" encoding=\"utf-8\"?>

        <heartbeat>
            <header>
              <code>2000</code>
              <origin>FrontEnd</origin>
              <timestamp>$XSDate</timestamp>
            </header>
            <body>
              <nameService>Website</nameService>
              <CPUload>$CPUload</CPUload>
              <RAMload>$RamUsage</RAMload>
            </body>
        </heartbeat>";
    //#endregion

    $xml = new \DOMDocument();
    $xml->loadXML($msg);
    if (!$xml->schemaValidateSource($xsd)) {
      $error = libxml_get_last_error();
    }
    error_log($msg);
    return response()->json([
      'msg' => $msg,
      'error' => $error,
    ]);
  }
}
