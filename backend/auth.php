<?php
require_once __DIR__ . "/config.php";

function require_login(): void {
    if (!isset($_SESSION['login_id'])) {
        header("Location: ../login.html");
        exit;
    }
}

function require_role(string $role): void {
    require_login();
    if ($_SESSION['role'] !== $role) {
        http_response_code(403);
        exit("Access denied.");
    }
}
?>
