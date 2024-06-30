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

$stmt = $conn->prepare("
    SELECT recipes.* FROM recipes
    JOIN recipe_likes ON recipes.id = recipe_likes.recipe_id
    WHERE recipe_likes.user_id = :user_id
");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$saved_recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($saved_recipes);
?>
