---
name: laravel-backend
description: Laravel backend coding standards (Controller, Service, Model, Validation). Load only when modifying app files.
---

# Laravel Backend Guidelines

## Architecture & Patterns
- **Thin Controller, Fat Service:** Controllers only handle HTTP request/response and delegate to Service classes. Keep business logic strictly out of Controllers.
- **Validation:** Use Form Requests (`app/Http/Requests/`) for all `POST`, `PUT`, and `PATCH` endpoints. Do not place validation logic inside Controllers.
- **Database Transactions:** Wrap any write operation touching more than one table in `DB::transaction(function () { ... })`.

## Queries & Performance
- Prevent N+1 queries: always use eager loading (`with()`) for relations in listings and DataTables.
- Use Query Builder (`DB::table()`) instead of Eloquent Models for heavy aggregations or cross-table reporting to minimize memory usage.
- Index all foreign keys and frequently filtered `WHERE` columns in migrations.

## Naming & Error Handling
- Name Service methods with descriptive verbs (e.g., `assignAssetToLine`, `syncProductionLog`).
- Return consistent API/AJAX JSON responses:
```php
return response()->json([
    'status' => 'success', // or 'error'
    'message' => 'Data saved successfully.',
    'data' => $result,
], 200);
```
- Catch specific exceptions, log with `Log::error($e->getMessage(), ['context' => ...])`, and never expose raw SQL errors in user responses.

## Pre-Completion Checklist
- [ ] No leftover `dd()`, `dump()`, or `Log::debug()`.
- [ ] Migration includes a valid `down()` method.
- [ ] New routes are protected by appropriate auth/permission middleware.
