<?php

namespace App\Http\Controllers;

use App\Models\EventSubscriber;
use App\Models\Event;
use App\Models\User;

use Illuminate\Http\Request;

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
        
    }

    public function showAllSubscribers($event_id)
    {
        //
        $eventSubscribers = EventSubscriber::select('user_id')->where('event_id', '=', $event_id)->get();

        $results = [];

        for($i = 0; $i < count($eventSubscribers); $i++)
        {
            $firstName = User::select('firstName')->where('id', '=', $eventSubscribers[$i]->user_id)->first();
            $lastName = User::select('lastName')->where('id', '=', $eventSubscribers[$i]->user_id)->first();

            $name = $firstName['firstName'] . ' ' . $lastName['lastName'];

            array_push($results, $name);
        }

        return response($results);
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
        $eventSubscriber->delete();

        return response()->json('EventSubscriber deleted');
    }
}
