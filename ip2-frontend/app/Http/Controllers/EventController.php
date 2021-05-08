<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Event;

class EventController extends Controller
{
    //
    public function index()
    {
        $events = Event::all()->toArray();
        return array_reverse($events);
    }

    public function store(Request $request)
    {
        $event = new Event([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'startsAtDate' => $request->input('startsAtDate'),
            'startsAtTime' => $request->input('startsAtTime'),
            'endsAtDate' => $request->input('endsAtDate'),
            'endsAtTime' => $request->input('endsAtTime'),
            'location' => $request->input('location')
        ]);
        $event->save();

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
}
