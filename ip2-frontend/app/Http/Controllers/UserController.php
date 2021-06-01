<?php

namespace App\Http\Controllers;

use App\Models\Event;
use PhpAmqpLib\Message\AMQPMessage;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use DOMDocument;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $users = User::all()->toArray();
        return $users;
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
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $user = User::find($id);
        return response()->json($user);
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public static function storeRecievedUser(\DOMDocument $doc)
    {

        $body = $doc->getElementsByTagName("body")[0];
        $new_password = $body->getElementsByTagName("birthday")[0]->nodeValue.$body->getElementsByTagName("email")[0]->nodeValue;
        $user = new User([
            'firstName' => $body->getElementsByTagName("firstname")[0]->nodeValue,
            'lastName' => $body->getElementsByTagName("lastname")[0]->nodeValue,
            'email' => $body->getElementsByTagName("email")[0]->nodeValue,
            'password' => Hash::make($new_password),
            'birthday' => $body->getElementsByTagName("birthday")[0]->nodeValue,
            'role' => $body->getElementsByTagName("role")[0]->nodeValue,
            'study' => $body->getElementsByTagName("study")[0]->nodeValue,
        ]);


        $user->save();
        return $user->id;
    }

    public static function updateRecievedUser(\DOMDocument $doc)
    {

        $body = $doc->getElementsByTagName("body")[0];
        $header = $doc->getElementsByTagName("header")[0];
        $user = User::find($header->getElementsByTagName("sourceEntityId")[0]->nodeValue);
        $new_password = $body->getElementsByTagName("birthday")[0]->nodeValue.$body->getElementsByTagName("email")[0]->nodeValue;
        $newUser = [
            'firstName' => $body->getElementsByTagName("firstname")[0]->nodeValue,
            'lastName' => $body->getElementsByTagName("lastname")[0]->nodeValue,
            'email' => $body->getElementsByTagName("email")[0]->nodeValue,
            'password' => Hash::make($new_password),
            'birthday' => $body->getElementsByTagName("birthday")[0]->nodeValue,
            'role' => $body->getElementsByTagName("role")[0]->nodeValue,
            'study' => $body->getElementsByTagName("study")[0]->nodeValue,
        ];
        $user->update($newUser);
    }

    public static function deleteRecievedUser(\DOMDocument $doc)
  {
    $header = $doc->getElementsByTagName("header")[0];
    $user = User::find($header->getElementsByTagName("sourceEntityId")[0]->nodeValue);

    $events = Event::where('user_id', '=', $user->id)->get();
    foreach ($events as $event){
        EventController::destroy($event->id);
    }
    $user->delete();
  }
}
