<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: todo.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List - Home</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<div class="container">
    <h1>Welcome to Your To-Do List App</h1>
    <nav>
        <a href="src/todo.php">Your Tasks</a>
        <a href="src/profile.php">Profile</a> <!-- Ссылка на профиль -->
        <a href="src/logout.php">Logout</a>
    </nav>
</div>
</body>
</html>
