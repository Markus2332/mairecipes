<?php
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/functions.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];
$recipe_id = $_POST['recipe_id'];

$stmt = $conn->prepare("SELECT * FROM recipe_likes WHERE user_id = :user_id AND recipe_id = :recipe_id");
$stmt->bindParam(':user_id', $user_id);
$stmt->bindParam(':recipe_id', $recipe_id);
$stmt->execute();
$like = $stmt->fetch();

if ($like) {
    $stmt = $conn->prepare("DELETE FROM recipe_likes WHERE user_id = :user_id AND recipe_id = :recipe_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':recipe_id', $recipe_id);
    $stmt->execute();
    echo json_encode(['status' => 'unliked']);
} else {
    $stmt = $conn->prepare("INSERT INTO recipe_likes (user_id, recipe_id) VALUES (:user_id, :recipe_id)");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':recipe_id', $recipe_id);
    $stmt->execute();
    echo json_encode(['status' => 'liked']);
}
?>
