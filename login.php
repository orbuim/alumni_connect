
<?php
require 'includes/init.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $stmt = $pdo->prepare('SELECT * FROM users WHERE email=?');
    $stmt->execute([trim($_POST['email'] ?? '')]);
    $u = $stmt->fetch();

    if ($u && password_verify($_POST['password'] ?? '', $u['password'])) {
        session_regenerate_id(true);

        $_SESSION['user'] = [
            'id' => $u['id'],
            'name' => $u['name'],
            'role' => $u['role']
        ];

        header('Location: index.php');
        exit;
    }

    $error = 'E-mailadres of wachtwoord is onjuist.';
}

header_html('Inloggen');

if (isset($_GET['registered'])) {
    echo '<div class="alert alert-success">Account aangemaakt. Log nu in.</div>';
}

if ($error) {
    echo '<div class="alert alert-danger">' . h($error) . '</div>';
}
?>

<form method="post" class="card card-body">
    <input type="hidden" name="csrf" value="<?= h(csrf()) ?>">

    <label>
        E-mail
        <input class="form-control mb-3" type="email" name="email" required>
    </label>

    <label>
        Wachtwoord
        <input class="form-control mb-3" type="password" name="password" required>
    </label>

    <button class="btn btn-primary">Inloggen</button>
</form>

<?php footer_html(); ?>
