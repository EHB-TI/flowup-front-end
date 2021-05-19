<?php

namespace App\Http\Controllers;

use App\Models\EventSubscriber;
use App\Models\Event;
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
        //
        $eventSubscribers = EventSubscriber::Where('event_id', '=', $id)->get();
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