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
$data = json_decode(file_get_contents('php://input'), true);

$recipe_id = $data['recipe_id'];
$title = $data['title'];
$category = $data['category'];
$area = $data['area'];
$instructions = $data['instructions'];
$photo = $data['photo'];

// Проверяем, существует ли рецепт в базе данных
$stmt = $conn->prepare("SELECT id FROM recipes WHERE api_id = :api_id");
$stmt->bindParam(':api_id', $recipe_id);
$stmt->execute();
$existing_recipe = $stmt->fetchColumn();

if ($existing_recipe) {
    // Рецепт уже существует, используем его ID
    $recipe_db_id = $existing_recipe;
} else {
    // Добавляем новый рецепт в базу данных
    $stmt = $conn->prepare("INSERT INTO recipes (api_id, user_id, title, ingredients, instructions, photo) VALUES (:api_id, :user_id, :title, :ingredients, :instructions, :photo)");
    $stmt->bindParam(':api_id', $recipe_id);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':ingredients', $category . ', ' . $area);
    $stmt->bindParam(':instructions', $instructions);
    $stmt->bindParam(':photo', $photo);
    $stmt->execute();
    $recipe_db_id = $conn->lastInsertId();
}

// Проверяем, лайкнул ли пользователь этот рецепт ранее
$stmt = $conn->prepare("SELECT * FROM recipe_likes WHERE user_id = :user_id AND recipe_id = :recipe_id");
$stmt->bindParam(':user_id', $user_id);
$stmt->bindParam(':recipe_id', $recipe_db_id);
$stmt->execute();
$like = $stmt->fetch();

if ($like) {
    // Пользователь уже лайкнул этот рецепт, удаляем лайк
    $stmt = $conn->prepare("DELETE FROM recipe_likes WHERE user_id = :user_id AND recipe_id = :recipe_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':recipe_id', $recipe_db_id);
    $stmt->execute();
    echo json_encode(['status' => 'unliked']);
} else {
    // Добавляем лайк
    $stmt = $conn->prepare("INSERT INTO recipe_likes (user_id, recipe_id) VALUES (:user_id, :recipe_id)");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':recipe_id', $recipe_db_id);
    $stmt->execute();
    echo json_encode(['status' => 'liked']);
}
?>
