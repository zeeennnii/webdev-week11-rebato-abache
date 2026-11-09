<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\LazyLoadingViolationException;
use Illuminate\Support\Facades\DB;

class EventQueryController extends Controller
{
    // OPTIMIZED QUERY
    public function index()
    {
        DB::flushQueryLog();
        DB::enableQueryLog();

        $events = Event::with('venue')
            ->withCount('participants')
            ->orderBy('id')
            ->limit(50)
            ->get();

        $queries = DB::getQueryLog();
        DB::disableQueryLog();

        return view('events.index', compact('events', 'queries'));
    }

    // NAIVE QUERY
    public function naive()
    {
        DB::flushQueryLog();
        DB::enableQueryLog();

        $events = Event::orderBy('id')
            ->limit(50)
            ->get();

        foreach ($events as $event) {
            $event->setAttribute('naive_venue_name', $event->venue->name);
            $event->setAttribute(
                'naive_participant_count',
                $event->participants()->count()
            );
        }

        $queries = DB::getQueryLog();
        DB::disableQueryLog();

        return view('events.naive', compact('events', 'queries'));
    }

    // LAZY LOADING PREVENTION DEMONSTRATION
    public function lazyLoadingError()
    {
        Model::preventLazyLoading();

        try {
            $events = Event::orderBy('id')->limit(50)->get();

            foreach ($events as $event) {
                $venueName = $event->venue->name;
            }

            return response('No lazy-loading violation detected.');
        } catch (LazyLoadingViolationException $e) {
            return response(
                'LAZY LOADING PREVENTED!' . "\n\n" . $e->getMessage(),
                500
            );
        }
    }

    // FULL-TEXT SEARCH
    public function search()
    {
        $q = request('q', '');

        $query = Event::query()
            ->whereFullText('description', $q)
            ->orderBy('id')
            ->limit(50);

        $sql = $query->toSql();
        $bindings = $query->getBindings();

        $events = $q !== ''
            ? $query->get()
            : collect();

        return view('events.search', compact(
            'events',
            'q',
            'sql',
            'bindings'
        ));
    }
}