---
name: database-sql
description: Schema design, migration, and query optimization standards for MariaDB/MySQL/MSSQL. Load only when modifying migrations, models, or raw SQL.
---

# Database & SQL Guidelines

## Schema Design & Migrations
- **Precise Data Types:** Use `DECIMAL` for financial/numeric precision (never `FLOAT`), realistic `VARCHAR` lengths, and consistent date types (`DATETIME` or `TIMESTAMP`).
- **Mandatory Indexing:** Index all foreign keys and columns frequently used in `WHERE`, `JOIN`, and `ORDER BY`. For multi-column filters, use composite indexes with the most selective column first.
- **Safe Rollbacks:** Every migration must include a clean `down()` method (drop indexes/foreign keys before dropping columns/tables).

## Query Security & Performance
- **Prevent SQL Injection:** Always use parameterized queries / bindings (`?` or `:param`). Never concatenate variables directly into SQL strings.
- **Avoid SELECT *:** Explicitly specify required columns, especially in `JOIN` and reporting queries.
- **Sargable Queries:** Do not wrap indexed columns in functions inside `WHERE` (wrong: `WHERE YEAR(created_at) = 2026`; right: `WHERE created_at >= '2026-01-01' AND created_at < '2027-01-01'`).
- **Batch Operations:** Process large inserts, updates, or data syncs (thousands of rows) in chunks of 500–1,000 rows to avoid prolonged table locks and memory exhaustion.

## Cross-DB Compatibility (MariaDB/MySQL vs. MSSQL)
- Prefer the framework's Query Builder for standard CRUD operations to maintain portability.
- When writing raw SQL, match the target connection's syntax:
  - Pagination: `LIMIT ... OFFSET ...` (MySQL/MariaDB) vs. `OFFSET ... ROWS FETCH NEXT ... ROWS ONLY` (MSSQL).
  - Identifiers: backticks (``` ` ```) in MySQL/MariaDB vs. brackets (`[]`) in MSSQL.
  - Current timestamp: `NOW()` (MySQL/MariaDB) vs. `GETDATE()` (MSSQL).

## Pre-Completion Checklist
- [ ] All raw queries use parameter bindings.
- [ ] No `SELECT *` in production or reporting queries.
- [ ] Primary filter columns are indexed and migration `down()` is valid.
