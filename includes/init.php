
<?php

session_start();

require __DIR__ . '/../config/db.php';
$entities = require __DIR__ . '/../config/entities.php';

function h($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function me(): ?array {
    return $_SESSION['user'] ?? null;
}

function require_login(): void {
    if (!me()) {
        header('Location: login.php');
        exit;
    }
}

function csrf(): string {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function verify_csrf(): void {
    if (!hash_equals(csrf(), $_POST['csrf'] ?? '')) {
        http_response_code(403);
        exit('Ongeldige beveiligingstoken');
    }
}

function can_create(array $entity): bool {
    return me() && (
        $entity['role'] === 'all' ||
        me()['role'] === $entity['role'] ||
        me()['role'] === 'beheerder'
    );
}

function can_edit(array $record): bool {
    return me() && (
        (int)$record['owner_id'] === (int)me()['id'] ||
        me()['role'] === 'beheerder'
    );
}

function header_html(string $title): void {
    echo '<!doctype html>
    <html lang="nl">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title>' . h($title) . ' - Alumni Connect</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="assets/style.css">
    </head>
    <body>
    <nav class="navbar navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php">Alumni Connect</a>
        <div>';

    if (me()) {
        echo '<span class="text-light me-3">' .
            h(me()['name']) . ' (' . h(me()['role']) . ')</span>
            <a class="btn btn-outline-light btn-sm" href="account.php">Mijn account</a>
            <a class="btn btn-outline-light btn-sm" href="logout.php">Uitloggen</a>';
    } else {
        echo '<a class="btn btn-outline-light btn-sm" href="login.php">Inloggen</a>
        <a class="btn btn-light btn-sm" href="register.php">Registreren</a>';
    }

    echo '</div></div></nav>
    <main class="container py-4">
    <h1 class="h3 mb-4">' . h($title) . '</h1>';
}

function footer_html(): void {
    echo '</main></body></html>';
}
