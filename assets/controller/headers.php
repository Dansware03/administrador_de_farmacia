<?php
if (!headers_sent()) {
    header('Content-Type: application/json');
// Content Security Policy (CSP)
    header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline';");
// HTTP Strict Transport Security (HSTS)
    header("Strict-Transport-Security: max-age=31536000; includeSubDomains; preload");
// X-Frame-Options
    header("X-Frame-Options: DENY");
// X-XSS-Protection
    header("X-XSS-Protection: 1; mode=block");
// Referrer Policy
    header("Referrer-Policy: no-referrer");
// Content-Type Options
    header("X-Content-Type-Options: nosniff");
}
?>