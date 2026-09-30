---
name: sqlite-ops
description: >
  Best practices, performance tuning, and schema design for embedded SQLite
  with PHP PDO. Covers WAL mode, busy timeouts, indexing, atomic transactions,
  foreign key enforcement, and query optimization without heavy ORMs.
---

# SQLite Operations & Performance

Expert patterns for embedded SQLite in local web applications using native PHP PDO.

## 1. Concurrency & Connection Calibration

SQLite is file-based. Multiple concurrent readers are supported, but only one writer at a time. To prevent `SQLITE_BUSY` or lock collisions:

- **Enable WAL Mode (Write-Ahead Logging):**
  Execute once or upon DB initialization:
  ```sql
  PRAGMA journal_mode = WAL;
  PRAGMA synchronous = NORMAL;
  ```
- **Foreign Keys Enforcement:**
  SQLite does not enforce foreign keys by default. Always execute on every connection:
  ```sql
  PRAGMA foreign_keys = ON;
  ```
- **Busy Timeout:**
  Configure a busy timeout in PDO attributes to wait instead of failing immediately:
  ```php
  $pdo->setAttribute(PDO::ATTR_TIMEOUT, 5); // 5 segundos
  ```

## 2. Transactions & Atomicity

- Wrap multi-statement mutations in `beginTransaction()`, `commit()`, and `rollBack()` inside `try / catch`.
- Never leave transactions hanging. Check `$pdo->inTransaction()` before rolling back.
- Keep transaction blocks as short as possible to release database locks promptly.

## 3. Query Optimization & Indexing

- Use `EXPLAIN QUERY PLAN` before suspect queries.
- Ensure foreign keys and filter columns (`WHERE producto_id = ?`, `WHERE ci_us = ?`, `estado = 'A'`) have indexes.
- Avoid `SELECT *` when only specific columns are needed.
- Use `LIMIT` and pagination for tables with large datasets to avoid memory bloat.

## 4. SQLite Schema Integrity

- SQLite uses dynamic typing. Define column affinities cleanly (`INTEGER PRIMARY KEY AUTOINCREMENT`, `TEXT`, `REAL`).
- Dates should be formatted in ISO-8601 (`YYYY-MM-DD` or `YYYY-MM-DD HH:MM:SS`) to allow string-based date comparisons and `strftime()`.
