<?php
session_start();
include '../includes/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Получение информации о пользователе
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Обработка загрузки аватара и изменения настроек
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Обработка загрузки аватара
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/';
        $avatar_name = basename($_FILES['avatar']['name']);
        $avatar_path = $upload_dir . $avatar_name;

        // Перемещение загруженного файла
        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $avatar_path)) {
            $stmt = $conn->prepare("UPDATE users SET avatar = ? WHERE id = ?");
            $stmt->bind_param("si", $avatar_path, $user_id);
            $stmt->execute();
        }
    }

    // Обработка изменения формата времени
    $time_format = $_POST['time_format'];
    $stmt = $conn->prepare("UPDATE users SET time_format = ? WHERE id = ?");
    $stmt->bind_param("si", $time_format, $user_id);
    $stmt->execute();

    header('Location: profile.php');
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
<div class="container">
    <h1>User Profile</h1>
    <nav>
        <a href="todo.php">Your Tasks</a> <!-- Ссылка на страницу задач -->
        <a href="index.php">Home</a>
        <a href="logout.php">Logout</a>
    </nav>
    <form method="POST" enctype="multipart/form-data">
        <label for="avatar">Upload Avatar:</label>
        <input type="file" name="avatar" id="avatar" accept="image/*">

        <label for="time_format">Select Time Format:</label>
        <select name="time_format" id="time_format">
            <option value="24" <?php echo ($user['time_format'] === '24') ? 'selected' : ''; ?>>24-hour</option>
            <option value="12" <?php echo ($user['time_format'] === '12') ? 'selected' : ''; ?>>12-hour</option>
        </select>

        <button type="submit">Save Changes</button>
    </form>

    <h2>Your Avatar:</h2>
    <img src="<?php echo htmlspecialchars($user['avatar']); ?>" alt="User Avatar" width="100">
</div>
</body>
</html>
