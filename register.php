
<?php
require 'includes/init.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$name || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8) {
        $error = 'Vul een naam, geldig e-mailadres en wachtwoord van minimaal 8 tekens in.';
    } else {
        try {
            $stmt = $pdo->prepare(
                'INSERT INTO users(name,email,password,role) VALUES(?,?,?,?)'
            );

            $stmt->execute([
                $name,
                $email,
                password_hash($password, PASSWORD_DEFAULT),
                'student'
            ]);

            header('Location: login.php?registered=1');
            exit;
        } catch (PDOException $e) {
            $error = 'Registratie mislukt. Mogelijk bestaat dit e-mailadres al.';
        }
    }
}

header_html('Registreren');

if ($error) {
    echo '<div class="alert alert-danger">' . h($error) . '</div>';
}
?>

<form method="post" class="card card-body">
    <input type="hidden" name="csrf" value="<?= h(csrf()) ?>">

    <label>
        Naam
        <input class="form-control mb-3" name="name" required>
    </label>

    <label>
        E-mail
        <input class="form-control mb-3" type="email" name="email" required>
    </label>

    <label>
        Wachtwoord (minimaal 8 tekens)
        <input class="form-control mb-3"
               type="password"
               name="password"
               minlength="8"
               required>
    </label>

    <button class="btn btn-primary">Account aanmaken</button>
</form>

<?php footer_html(); ?>
