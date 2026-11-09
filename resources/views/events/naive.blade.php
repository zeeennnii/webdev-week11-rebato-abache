<!DOCTYPE html>
<html>
<head>
    <title>Naive Campus Events</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
            background: #f4f6fa;
        }

        h1 {
            color: #1769e0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background: #1769e0;
            color: white;
        }

        .query-box {
            margin-top: 25px;
            padding: 20px;
            background: white;
        }
    </style>
</head>

<body>

    <h1>Naive Campus Events</h1>

    <p>Showing the first 50 events using lazy loading.</p>

    <table>
        <thead>
            <tr>
                <th>Event Title</th>
                <th>Venue</th>
                <th>Participants</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($events as $event)
                <tr>
                    <td>{{ $event->title }}</td>
                    <td>{{ $event->naive_venue_name }}</td>
                    <td>{{ $event->naive_participant_count }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="query-box">
        <h2>Naive Query Count</h2>

        <p>Total queries: {{ count($queries) }}</p>
    </div>

</body>
</html>