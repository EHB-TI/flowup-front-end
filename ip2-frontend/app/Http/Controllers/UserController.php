<?php

namespace App\Http\Controllers;
use PhpAmqpLib\Message\AMQPMessage;
use Illuminate\Http\Request;
use App\Models\User;


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

    public static function recieveUser(AMQPMessage $message){
        $message->ack();
        $string = $message->getBody();
        $doc = new \DOMDocument();
        $doc->loadXML($string);
        $XSDPath = "public/XML-XSD/user.xsd";
        if($doc->SchemaValidate($XSDPath)){
            $body = $doc->getElementsByTagName("body")[0];
             $user = new User([
                'firstName' => $body->getElementsByTagName("firstname")[0]->nodeValue, 
                'lastName' => $body->getElementsByTagName("lastname")[0]->nodeValue,
                'email' => $body->getElementsByTagName("email")[0]->nodeValue,
                'birthDate' => $body->getElementsByTagName("birthday")[0]->nodeValue,
                'role' => $body->getElementsByTagName("role")[0]->nodeValue,
                'study' => $body->getElementsByTagName("study")[0]->nodeValue,
            ]);
           

            $user->save();
        }else{
            error_log('error');
        }
    }


}
