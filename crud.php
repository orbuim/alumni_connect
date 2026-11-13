
<?php
require 'includes/init.php';

$key = $_GET['entity'] ?? '';

if (!isset($entities[$key])) {
    http_response_code(404);
    exit('Onbekend onderdeel');
}

$entity = $entities[$key];
$fields = $entity['fields'];
$action = $_GET['action'] ?? 'list';
$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
$error = '';

if (in_array($action, ['create', 'edit', 'delete'], true)) {
    require_login();
}

$record = null;

if (in_array($action, ['edit', 'delete'], true)) {
    if (!$id) {
        http_response_code(400);
        exit('Ongeldig ID');
    }

    $stmt = $pdo->prepare("SELECT * FROM `$key` WHERE id=?");
    $stmt->execute([$id]);
    $record = $stmt->fetch();

    if (!$record) {
        http_response_code(404);
        exit('Record niet gevonden');
    }

    if (!can_edit($record)) {
        http_response_code(403);
        exit('Geen toegang');
    }
}

if ($action === 'create' && !can_create($entity)) {
    http_response_code(403);
    exit('Geen toestemming voor deze rol');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    if ($action === 'delete') {
        $pdo->prepare("DELETE FROM `$key` WHERE id=?")->execute([$id]);
        header('Location: crud.php?entity=' . $key);
        exit;
    }

    if (!in_array($action, ['create', 'edit'], true)) {
        http_response_code(405);
        exit('Actie niet toegestaan');
    }

    $values = [];

    foreach ($fields as $field => $label) {
        $values[$field] = trim((string)($_POST[$field] ?? ''));

        if ($values[$field] === '') {
            $error = 'Vul alle velden in.';
        }
    }

    if (!$error) {
        if ($key === 'registrations' &&
            !in_array($values['status'], ['aangemeld', 'geannuleerd'], true)) {
            $error = 'Ongeldige status';
        }

        if ($key === 'news' &&
            !in_array($values['published'], ['0', '1'], true)) {
            $error = 'Gebruik 0 of 1';
        }

        if (isset($values['event_date']) &&
            !preg_match('/^\d{4}-\d{2}-\d{2}$/', $values['event_date'])) {
            $error = 'Gebruik datum JJJJ-MM-DD';
        }

        foreach ($values as $field => $value) {
            if (str_ends_with($field, '_id') && !ctype_digit($value)) {
                $error = 'ID moet een getal zijn';
            }
        }
    }

    if (!$error) {
        try {
            $names = array_keys($fields);

            if ($action === 'create') {
                $columns = implode(',', array_map(fn($n) => "`$n`", $names));
                $placeholders = implode(',', array_fill(0, count($names), '?'));

                $stmt = $pdo->prepare(
                    "INSERT INTO `$key` (owner_id,$columns)
                     VALUES (?,$placeholders)"
                );

                $stmt->execute(array_merge(
                    [me()['id']],
                    array_values($values)
                ));
            } else {
                $set = implode(',', array_map(fn($n) => "`$n`=?", $names));

                $stmt = $pdo->prepare(
                    "UPDATE `$key` SET $set WHERE id=?"
                );

                $stmt->execute(array_merge(
                    array_values($values),
                    [$id]
                ));
            }

            header('Location: crud.php?entity=' . $key);
            exit;

        } catch (PDOException $e) {
            $error = 'Opslaan mislukt. Controleer unieke waarden en gekoppelde ID’s.';
        }
    }
}

header_html($entity['title']);

if ($error) {
    echo '<div class="alert alert-danger">' . h($error) . '</div>';
}

if ($action === 'create' || $action === 'edit') {
    echo '<form method="post" class="card card-body">';
    echo '<input type="hidden" name="csrf" value="' . h(csrf()) . '">';

    foreach ($fields as $field => $label) {
        $value = $_POST[$field] ?? ($record[$field] ?? '');

        echo '<label class="mb-3">' . h($label);

        if (in_array($field, ['body', 'description', 'skills'], true)) {
            echo '<textarea class="form-control" name="' . h($field) . '" required>' .
                 h($value) . '</textarea>';
        } else {
            echo '<input class="form-control" name="' . h($field) .
                 '" value="' . h($value) . '" required>';
        }

        echo '</label>';
    }

    echo '<button class="btn btn-primary">Opslaan</button>';
    echo '</form>';

} elseif ($action === 'delete') {
    echo '<div class="alert alert-warning">
        Weet je zeker dat je dit item wilt verwijderen?
    </div>';

    echo '<form method="post">
        <input type="hidden" name="csrf" value="' . h(csrf()) . '">
        <button class="btn btn-danger">Ja, verwijderen</button>
    </form>';

} else {
    $stmt = $pdo->query("SELECT * FROM `$key` ORDER BY id DESC");
    $rows = $stmt->fetchAll();

    if (can_create($entity)) {
        echo '<a class="btn btn-primary mb-3"
            href="crud.php?entity=' . h($key) . '&action=create">
            Nieuw toevoegen
        </a>';
    }

    echo '<div class="row g-3">';

    foreach ($rows as $r) {
        if (
            $key === 'news' &&
            !$r['published'] &&
            (!me() || (
                $r['owner_id'] != me()['id'] &&
                me()['role'] !== 'beheerder'
            ))
        ) {
            continue;
        }

        echo '<div class="col-md-6">
            <div class="card card-body h-100">
            <h2 class="h6">Item #' . h($r['id']) . '</h2>';

        foreach ($fields as $field => $label) {
            echo '<p class="mb-2">
                <strong>' . h($label) . ':</strong> ' .
                nl2br(h($r[$field])) .
                '</p>';
        }

        if (can_edit($r)) {
            echo '<div class="mt-auto">
                <a class="btn btn-outline-primary btn-sm"
                   href="crud.php?entity=' . h($key) .
                   '&action=edit&id=' . h($r['id']) . '">
                   Wijzigen
                </a>

                <a class="btn btn-outline-danger btn-sm"
                   href="crud.php?entity=' . h($key) .
                   '&action=delete&id=' . h($r['id']) . '">
                   Verwijderen
                </a>
            </div>';
        }

        echo '</div></div>';
    }

    echo '</div>';
}

echo '<p class="mt-4">
    <a href="index.php">Terug naar dashboard</a>
</p>';

footer_html();
