
<?php
require 'includes/init.php';
require_login();

$error = '';
$success = '';

$stmt = $pdo->prepare('SELECT id,name,email,role FROM users WHERE id=?');
$stmt->execute([me()['id']]);
$u = $stmt->fetch();

if (!$u) {
    session_destroy();
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    if (($_POST['action'] ?? '') === 'delete') {
        $pdo->prepare('DELETE FROM users WHERE id=?')->execute([$u['id']]);

        session_destroy();
        header('Location: index.php');
        exit;
    }

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if (!$name || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Ongeldige gegevens.';
    } else {
        try {
            $pdo->prepare('UPDATE users SET name=?,email=? WHERE id=?')
                ->execute([$name, $email, $u['id']]);

            $_SESSION['user']['name'] = $name;
            $u['name'] = $name;
            $u['email'] = $email;

            $success = 'Gegevens opgeslagen.';
        } catch (PDOException $e) {
            $error = 'E-mailadres is al in gebruik.';
        }
    }
}

header_html('Mijn account');

if ($error) {
    echo '<div class="alert alert-danger">' . h($error) . '</div>';
}

if ($success) {
    echo '<div class="alert alert-success">' . h($success) . '</div>';
}
?>

<div class="card card-body">
    <p>Rol: <?= h($u['role']) ?></p>

    <form method="post">
        <input type="hidden" name="csrf" value="<?= h(csrf()) ?>">

        <label>
            Naam
            <input class="form-control mb-3"
                   name="name"
                   value="<?= h($u['name']) ?>"
                   required>
        </label>

        <label>
            E-mail
            <input class="form-control mb-3"
                   type="email"
                   name="email"
                   value="<?= h($u['email']) ?>"
                   required>
        </label>

        <button class="btn btn-primary">Opslaan</button>
    </form>

    <hr>

    <form method="post"
          onsubmit="return confirm('Account definitief verwijderen?')">
        <input type="hidden" name="csrf" value="<?= h(csrf()) ?>">
        <input type="hidden" name="action" value="delete">

        <button class="btn btn-danger">Account verwijderen</button>
    </form>
</div>

<?php footer_html(); ?>
