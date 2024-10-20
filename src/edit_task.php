<?php
session_start();
include '../includes/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $task_id = $_POST['id'];
    $updated_task = $_POST['task'];
    $updated_priority = $_POST['priority']; // Получаем обновленный приоритет

    $stmt = $conn->prepare("UPDATE tasks SET task = ?, priority = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ssii", $updated_task, $updated_priority, $task_id, $_SESSION['user_id']);
    $stmt->execute();

    header('Location: todo.php');
    exit();
}


if (isset($_GET['id'])) {
    $task_id = $_GET['id'];

    $stmt = $conn->prepare("SELECT * FROM tasks WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $task_id, $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $task = $result->fetch_assoc();
    if (!$task) {
        header('Location: todo.php'); // Задача не найдена, перенаправляем на todo.php
        exit();
    }
} else {
    header('Location: todo.php'); // Если ID не указан, перенаправляем на todo.php
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Task - To-Do List App</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
<div class="container">
    <h1>Edit Task</h1>
    <nav>
        <a href="index.php">Home</a>
        <a href="todo.php">Back to To-Do List</a>
    </nav>
    <form method="POST">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($task['id']); ?>">
        <input type="text" name="task" value="<?php echo htmlspecialchars($task['task']); ?>" required>
        <select name="priority" required>
            <option value="low" <?php echo ($task['priority'] == 'low') ? 'selected' : ''; ?>>Low</option>
            <option value="medium" <?php echo ($task['priority'] == 'medium') ? 'selected' : ''; ?>>Medium</option>
            <option value="high" <?php echo ($task['priority'] == 'high') ? 'selected' : ''; ?>>High</option>
        </select>
        <button type="submit">Update Task</button>
    </form>
</div>
</body>
</html>
