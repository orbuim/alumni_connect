
<?php
require 'includes/init.php';

$_SESSION = [];
session_destroy();

header('Location: index.php');
exit;
