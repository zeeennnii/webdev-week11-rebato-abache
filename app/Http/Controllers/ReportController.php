<?php
namespace App\Http\Controllers;

use App\Models\Venue;
use App\Models\Event;
use App\Models\Participant;
use App\Models\Note;

class ReportController extends Controller
{
    public function index()
    {
        // QUERY 1: Every venue and its number of events
        $venues = Venue::withCount('events')->get();

        // QUERY 2: All events at one chosen venue
        $chosenVenue = Venue::with('events')->first();

        // QUERY 3: Participants of one chosen event with registration status
        $chosenEvent = Event::with('participants')->first();

        // QUERY 4: All events joined by one chosen participant
        $chosenParticipant = Participant::with('events')->first();

        // QUERY 5: Events with at least one waitlisted participant
        $waitlistedEvents = Event::whereHas('participants', function ($query) {
            $query->where('event_participant.status', 'waitlisted');
        })->with('participants')->get();

        // QUERY 6: Notes of one chosen event and one chosen venue
        $eventNotes = $chosenEvent
            ? $chosenEvent->notes
            : collect();

        $venueNotes = $chosenVenue
            ? $chosenVenue->notes
            : collect();

        // QUERY 7: One note and its polymorphic owner
        $chosenNote = Note::with('noteable')->first();

        return view('reports.index', compact(
            'venues',
            'chosenVenue',
            'chosenEvent',
            'chosenParticipant',
            'waitlistedEvents',
            'eventNotes',
            'venueNotes',
            'chosenNote'
        ));
    }
}