---
name: vanilla-web-ops
description: >
  Best practices for native PHP 8 backend controllers and Vanilla JS DOM manipulation.
  Emphasizes no-framework architecture, strict input sanitization, PDO prepared statements,
  secure session handling, fetch API, and responsive UI without bloat.
---

# Vanilla Web Operations (PHP 8 & Modern JS)

Guidelines for maintaining a lean, fast, secure native web application without external frameworks.

## 1. Native PHP Backend Rules

- **Strict Prepared Statements:**
  Never concatenate variables into SQL strings. Always use named or positional parameters:
  ```php
  $query = $pdo->prepare("SELECT * FROM producto WHERE id_producto = :id");
  $query->execute([':id' => $id]);
  ```
- **Error & Exception Mode:**
  Ensure PDO is set to `PDO::ERRMODE_EXCEPTION`.
- **Session Security & Authorization:**
  - Verify session state at the entry point of every controller.
  - Never trust user-supplied IDs from `$_POST` or `$_GET` for authorization (prevent IDOR). Use `$_SESSION['usuario']` directly.
- **Passwords & Cryptography:**
  - Strictly use `password_hash($pass, PASSWORD_BCRYPT)` and `password_verify($pass, $hash)`.
  - Never store or compare plaintext credentials.
- **JSON API Responses:**
  Ensure headers are set properly: `header('Content-Type: application/json')` when returning JSON, and avoid trailing whitespaces outside PHP tags.

## 2. Vanilla JavaScript & DOM Performance

- **Async / Fetch API:**
  Use `fetch()` with `FormData` or JSON. Always handle network errors and check `response.ok`.
  ```javascript
  const res = await fetch('../controller/ProductoController.php', { method: 'POST', body: data });
  if (!res.ok) throw new Error('Error en el servidor');
  ```
- **Event Delegation:**
  Attach listeners to static parent containers (e.g. table body or list container) rather than attaching individual listeners to dynamic rows.
- **DOM Rendering Performance:**
  Build HTML strings or use `DocumentFragment` before inserting into the DOM to avoid multiple reflows:
  ```javascript
  const fragment = document.createDocumentFragment();
  // append elements
  container.replaceChildren(fragment);
  ```
- **UI Responsiveness:**
  Provide visual feedback on asynchronous actions (loading indicators, disabled buttons during submit) to prevent double submissions.
