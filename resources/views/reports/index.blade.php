
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campus Events - Database Reports</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 30px;
            font-family: Arial, sans-serif;
            background: #f3f6fb;
            color: #263449;
        }

        .container {
            max-width: 1100px;
            margin: auto;
        }

        header {
            background: linear-gradient(120deg, #1769e0, #104db0);
            color: white;
            padding: 30px;
            border-radius: 14px;
            margin-bottom: 25px;
        }

        header h1 {
            margin-top: 0;
        }

        .report {
            background: white;
            padding: 24px;
            margin-bottom: 20px;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0,0,0,.05);
        }

        h2 {
            color: #1457b8;
            font-size: 19px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e5eaf1;
        }

        th {
            background: #edf4ff;
        }

        .status {
            font-weight: bold;
            text-transform: capitalize;
        }

        .empty {
            color: #777;
        }

        .back {
            display: inline-block;
            color: white;
            background: #1457b8;
            padding: 12px 18px;
            border-radius: 8px;
            text-decoration: none;
        }
    </style>
</head>
<body>

<div class="container">

    <header>
        <h1>Campus Events Database Reports</h1>
        <p>Relational Data Model and Eloquent Query Demonstrations</p>
    </header>

    {{-- QUERY 1 --}}
    <section class="report">
        <h2>Query 1: Every Venue and Number of Events</h2>

        <table>
            <tr>
                <th>Venue</th>
                <th>Address</th>
                <th>Number of Events</th>
            </tr>

            @forelse($venues as $venue)
                <tr>
                    <td>{{ $venue->name }}</td>
                    <td>{{ $venue->address }}</td>
                    <td>{{ $venue->events_count }}</td>
                </tr>
            @empty
                <tr><td colspan="3">No venues found.</td></tr>
            @endforelse
        </table>
    </section>

    {{-- QUERY 2 --}}
    <section class="report">
        <h2>Query 2: All Events at One Chosen Venue</h2>

        @if($chosenVenue)
            <p><strong>Chosen Venue:</strong> {{ $chosenVenue->name }}</p>

            @forelse($chosenVenue->events as $event)
                <p>{{ $event->title }} - {{ $event->event_date->format('M d, Y h:i A') }}</p>
            @empty
                <p class="empty">No events at this venue.</p>
            @endforelse
        @else
            <p>No venue found.</p>
        @endif
    </section>

    {{-- QUERY 3 --}}
    <section class="report">
        <h2>Query 3: Participants of One Chosen Event and Their Status</h2>

        @if($chosenEvent)
            <p><strong>Chosen Event:</strong> {{ $chosenEvent->title }}</p>

            <table>
                <tr>
                    <th>Participant</th>
                    <th>Email</th>
                    <th>Registration Status</th>
                </tr>

                @forelse($chosenEvent->participants as $participant)
                    <tr>
                        <td>{{ $participant->name }}</td>
                        <td>{{ $participant->email }}</td>
                        <td class="status">{{ $participant->pivot->status }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3">No participants found.</td></tr>
                @endforelse
            </table>
        @endif
    </section>

    {{-- QUERY 4 --}}
    <section class="report">
        <h2>Query 4: All Events Joined by One Chosen Participant</h2>

        @if($chosenParticipant)
            <p><strong>Chosen Participant:</strong> {{ $chosenParticipant->name }}</p>

            @forelse($chosenParticipant->events as $event)
                <p>
                    {{ $event->title }}
                    - Status: {{ $event->pivot->status }}
                </p>
            @empty
                <p class="empty">No joined events.</p>
            @endforelse
        @endif
    </section>

    {{-- QUERY 5 --}}
    <section class="report">
        <h2>Query 5: Events with at Least One Waitlisted Participant</h2>

        @forelse($waitlistedEvents as $event)
            <p>
                <strong>{{ $event->title }}</strong>
                - {{ $event->event_date->format('M d, Y') }}
            </p>
        @empty
            <p class="empty">No waitlisted events found.</p>
        @endforelse
    </section>

    {{-- QUERY 6 --}}
    <section class="report">
        <h2>Query 6: Notes of One Chosen Event and One Chosen Venue</h2>

        <h3>Event Notes</h3>

        @forelse($eventNotes as $note)
            <p>{{ $note->body }}</p>
        @empty
            <p class="empty">No event notes found.</p>
        @endforelse

        <h3>Venue Notes</h3>

        @forelse($venueNotes as $note)
            <p>{{ $note->body }}</p>
        @empty
            <p class="empty">No venue notes found.</p>
        @endforelse
    </section>

    {{-- QUERY 7 --}}
    <section class="report">
        <h2>Query 7: One Note and Its Polymorphic Owner</h2>

        @if($chosenNote)
            <p><strong>Note:</strong> {{ $chosenNote->body }}</p>

            <p>
                <strong>Owner Name:</strong>
                {{ $chosenNote->noteable?->name ?? 'Owner not found' }}
            </p>

            <p>
                <strong>Model Type:</strong>
                {{ class_basename($chosenNote->noteable_type) }}
            </p>
        @else
            <p>No notes found.</p>
        @endif
    </section>

</div>

</body>
</html>