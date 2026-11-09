<!DOCTYPE html>
<html>
<head>
    <title>Event Full-Text Search</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 35px;
            background: #f4f6f9;
        }

        h1, h2 {
            color: #174ea6;
        }

        form {
            margin-bottom: 25px;
        }

        input {
            padding: 10px;
            width: 280px;
        }

        button {
            padding: 10px 18px;
            background: #1769e0;
            color: white;
            border: none;
            cursor: pointer;
        }

        .event {
            background: white;
            padding: 15px;
            margin-bottom: 12px;
            border-radius: 8px;
        }

        pre {
            background: #222;
            color: #d8f5d0;
            padding: 15px;
            overflow-x: auto;
            white-space: pre-wrap;
        }
    </style>
</head>
<body>

    <h1>Campus Events Full-Text Search</h1>

    <form method="GET" action="{{ route('events.search') }}">
        <input
            type="text"
            name="q"
            value="{{ $q }}"
            placeholder="Search event descriptions..."
        >

        <button type="submit">Search</button>
    </form>

    @if ($q !== '')
        <h2>Results for: {{ $q }}</h2>

        <p>Results displayed: {{ $events->count() }} (maximum 50)</p>

        @forelse ($events as $event)
            <div class="event">
                <h3>{{ $event->title }}</h3>
                <p>{{ $event->description }}</p>
            </div>
        @empty
            <p>No matching events found.</p>
        @endforelse
    @endif

    <h2>Generated SQL (toSql)</h2>
    <pre>{{ $sql }}</pre>

    <h2>Query Bindings</h2>
    <pre>{{ json_encode($bindings, JSON_PRETTY_PRINT) }}</pre>

</body>
</html>