<?php

namespace App\Http\Controllers;

use App\Models\EventSubscriber;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


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

        return response()->json('EventSubscriber created!');
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
        //$eventSubscribers = EventSubscriber::Where('event_id', '=', $id)->get();
        
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
        if(DB::table('event_subscribers')->where('user_id','=',$user_id)->where('event_id','=',$event_id)->delete()){
            return response()->json('EventSubscriber deleted');
        }
        else{
            return response()->json('EventSubscriber does not exist so can\'t be deleted');
        }
        
        
        
    }
}
