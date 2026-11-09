
# Web Development Week 11 Assessment
## Campus Events: Optimized & Advanced Queries

**Members:**
- Rebato
- Abache

**Project:** Campus Events Database System  
**Database:** PostgreSQL  
**Framework:** Laravel  
**Database Size:** 20,000 Events

---

## 1. Project Description

This project demonstrates relational database modeling, Eloquent relationships, JSONB queries, full-text search, and query optimization using a campus events system.

It includes venues, events, participants, event registrations, and polymorphic notes.

The project also demonstrates how indexes and eager loading improve database query performance when working with large amounts of data.

## 2. Database Relationships

| Relationship | Type | Description |
|---|---|---|
| Venue - Event | One-to-Many | A venue can have multiple events. |
| Event - Venue | Many-to-One | An event belongs to one venue. |
| Event - Participant | Many-to-Many | Participants can register for multiple events. |
| Participant - Event | Many-to-Many | Events can have multiple participants. |
| Event - Note | Polymorphic One-to-Many | An event can have multiple notes. |
| Venue - Note | Polymorphic One-to-Many | A venue can have multiple notes. |

## 3. Database Constraints

- Events reference existing venues through a foreign key.
- Venue deletion is restricted when events still reference it.
- Event and participant registrations use a composite primary key.
- Deleting an event or participant cascades to its registrations.
- Notes use polymorphic columns: `noteable_type` and `noteable_id`.

## 4. Sample Data

The Week 11 seeder creates:

| Table | Records |
|---|---:|
| Venues | 3 |
| Events | 20,000 |
| Participants | 200 |
| Event Registrations | 300 |

The event descriptions contain searchable text. The rare keyword `quasar` appears in 50 events.

The JSONB `details` column contains:
- `dress_code`
- `capacity`
- `tags`

The rare JSONB value `cosmic-formal` appears in 50 events.

Data is inserted in chunks to handle large datasets efficiently.

## 5. Eloquent Local Scopes

The Event model contains two local scopes:

### Upcoming Events

```php
Event::upcoming()->get();
```

Retrieves events with an event date equal to or later than the current date and time.

### Minimum Capacity

```php
Event::withMinimumCapacity(100)->get();
```

Retrieves events with a capacity of at least 100.

### Chaining Scopes

```php
Event::upcoming()
    ->withMinimumCapacity(100)
    ->get();
```

Combines both conditions in one query.

## 6. N+1 Query Problem and Optimization

The project demonstrates the N+1 query problem using the first 50 events.

The naive implementation retrieves events first, then separately loads each event's venue and participant count.

The optimized implementation uses eager loading and `withCount()`.

| Implementation | Queries |
|---|---:|
| Naive loading | 101 |
| Optimized loading | 2 |

### Explanation

The naive implementation executes:
- 1 query to retrieve the events.
- 50 queries to retrieve venues.
- 50 queries to count participants.

Total: 101 queries.

The optimized implementation uses:
- `with('venue')` to eager load venues.
- `withCount('participants')` to retrieve participant counts.

This reduces the total to 2 queries and avoids unnecessary database requests.

The project also demonstrates Laravel's `Model::preventLazyLoading()` to detect accidental lazy loading.

## 7. JSONB Queries

The `events` table contains a PostgreSQL JSONB column named `details`.

### JSON Arrow Syntax

```php
Event::where('details->dress_code', 'cosmic-formal')->count();
```

Result: 50 events.

Generated SQL:

```sql
select * from "events"
where "details"->>'dress_code' = ?
```

### JSON Containment

```php
Event::whereJsonContains('details->tags', 'campus')->count();
```

Result: 20,000 events.

Generated SQL:

```sql
select * from "events"
where ("details"->'tags')::jsonb @> ?
```

### JSONB vs. Normal Columns

Normal database columns are appropriate for frequently queried structured values with a fixed data type, such as event dates, titles, and capacities.

JSONB is useful for flexible or additional attributes that may vary between events, such as dress codes and tags.

PostgreSQL JSONB also supports containment operators and indexing, making it useful for searching structured JSON data.

## 8. Full-Text Search

The project implements full-text search using Laravel's `whereFullText()` method.

```php
Event::whereFullText('description', 'quasar')
    ->limit(50)
    ->get();
```

Search page:

`/events/search?q=quasar`

The search returns up to 50 matching events.

PostgreSQL uses `to_tsvector()` and `plainto_tsquery()` to search event descriptions.

A GIN index is created to improve full-text search performance.

## 9. Database Indexes

The project uses three additional indexes:

| Index | Type | Purpose |
|---|---|---|
| `events_dress_code_idx` | B-tree expression | JSON arrow filtering |
| `events_details_gin_idx` | GIN JSONB | JSON containment |
| `events_description_fulltext_idx` | GIN | Full-text search |

These indexes are created through Laravel migrations using PostgreSQL SQL statements.

## 10. EXPLAIN ANALYZE Results

The following results were collected using PostgreSQL `EXPLAIN ANALYZE` with 20,000 event records.

### Query Performance Comparison

| Query | Index Status | Query Plan | Execution Time |
|---|---|---|---:|
| JSON arrow | Without index | Seq Scan | 10.624 ms |
| JSON arrow | With index | Bitmap Index Scan | 2.043 ms |
| JSON containment | Without index | Seq Scan | 7.681 ms |
| JSON containment | With index | Bitmap Index Scan | 2.019 ms |
| Full-text search | Without index | Seq Scan | 218.247 ms |
| Full-text search | With index | Bitmap Index Scan | 0.083 ms |

### Unused Index Demonstration

Query:

```sql
EXPLAIN ANALYZE
SELECT *
FROM events
WHERE details->>'dress_code' ILIKE '%cosmic%';
```

Result:

- Query Plan: Sequential Scan
- Execution Time: 18.149 ms
- Matching Rows: 50

### Explanation

The B-tree expression index supports exact matching on the indexed JSON expression.

However, the query uses `ILIKE '%cosmic%'`, which has a leading wildcard. A regular B-tree index cannot efficiently support this pattern, so PostgreSQL performs a sequential scan instead.

### Performance Discussion

The indexed queries showed lower execution times than their corresponding baseline queries.

The full-text search improved from 218.247 ms to 0.083 ms in the recorded tests.

Actual execution times may vary depending on database cache, system resources, and query conditions.

## 11. How to Run

1. Clone the repository.
2. Run `composer install`.
3. Copy `.env.example` to `.env`.
4. Configure the PostgreSQL database credentials.
5. Run `php artisan key:generate`.
6. Run `php artisan migrate --seed`.
7. Run `php artisan serve`.
8. Open `/reports` to view the database reports.
9. Open `/events` to view optimized event queries.
10. Open `/events/naive` to compare naive queries.
11. Open `/events/search?q=quasar` to test full-text search.

## 12. What We Learned

This assessment helped us understand how to optimize database queries when handling large amounts of data. We learned how Eloquent local scopes simplify reusable queries and how eager loading prevents the N+1 query problem. We also practiced using PostgreSQL JSONB filtering, full-text search, and different index types. Through EXPLAIN ANALYZE, we observed how indexes can reduce query execution time and how certain search patterns can prevent an index from being used.

---

**Project Members:** Rebato and Abache  
**Course:** Web Development  
**Assessment:** Week 11 - Optimized & Advanced Queries
