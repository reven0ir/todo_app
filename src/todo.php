<?php
session_start();
include '../includes/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$stmt = $conn->prepare("SELECT time_format FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$user_settings = $stmt->get_result()->fetch_assoc();
$time_format = $user_settings['time_format'];

$display_time = function($datetime) use ($time_format) {
    if ($datetime) {
        if ($time_format === '12') {
            return date('g:i A', strtotime($datetime));
        }
        return date('H:i', strtotime($datetime));
    }
    return '';
};

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['task'])) {
    $task = $_POST['task'];
    $priority = $_POST['priority'];
    $due_date = $_POST['due_date'];
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO tasks (user_id, task, priority, due_date) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $user_id, $task, $priority, $due_date);
    $stmt->execute();
    header('Location: todo.php');
    exit();
}

$tasks = $conn->query("SELECT * FROM tasks WHERE user_id = " . $_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your To-Do List</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
<div class="container">
    <h1>Your To-Do List</h1>
    <nav>
        <a href="index.php">Home</a>
        <a href="profile.php">Profile</a>
        <a href="logout.php">Logout</a>
    </nav>

    <form method="POST" class="task-form">
        <input type="text" name="task" placeholder="New Task" required class="task-input">
        <label for="priority">Select Task Priority:</label>
        <select name="priority" id="priority" required class="priority-select">
            <option value="low">Low</option>
            <option value="medium" selected>Medium</option>
            <option value="high">High</option>
        </select>
        <label for="due_date">Due Date and Time:</label>
        <input type="datetime-local" name="due_date" id="due_date" required class="due-date-input">
        <button type="submit" class="add-task-button">Add Task</button>
    </form>

    <ul>
        <?php if ($tasks->num_rows > 0): ?>
            <?php while ($task = $tasks->fetch_assoc()): ?>
                <li>
                    <?php echo htmlspecialchars($task['task']); ?>
                    <span class="priority-<?php echo htmlspecialchars($task['priority']); ?>">
                        (Priority: <?php echo htmlspecialchars(ucfirst($task['priority'])); ?>)
                    </span>
                    <span>
                        (Due: <?php echo htmlspecialchars($display_time($task['due_date'])); ?>)
                    </span>
                    <a href="edit_task.php?id=<?php echo $task['id']; ?>">Edit</a>
                    <a href="delete_task.php?id=<?php echo $task['id']; ?>">Delete</a>
                </li>
            <?php endwhile; ?>
        <?php else: ?>
            <li>No tasks found.</li>
        <?php endif; ?>
    </ul>
</div>
</body>
</html>
