<?php
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/functions.php';
include __DIR__ . '/includes/header.php';

if (isset($_GET['id'])) {
    $stmt = $conn->prepare("SELECT * FROM recipes WHERE id = :id");
    $stmt->bindParam(':id', $_GET['id']);
    $stmt->execute();
    $recipe = $stmt->fetch();

    if ($recipe):
?>

<div class="container">
    <h2><?php echo htmlspecialchars($recipe['title']); ?></h2>
    <img src="<?php echo htmlspecialchars($recipe['photo']); ?>" class="img-fluid" alt="Recipe Photo">
    <p><strong>Cooking Time:</strong> <?php echo htmlspecialchars($recipe['cooking_time']); ?> minutes</p>
    <p><strong>Ingredients:</strong><br><?php echo nl2br(htmlspecialchars($recipe['ingredients'])); ?></p>
    <p><strong>Instructions:</strong><br><?php echo nl2br(htmlspecialchars($recipe['instructions'])); ?></p>
    <a href="index.php" class="btn btn-secondary">Back to Recipes</a>
</div>

<?php
    else:
        echo "<div class='container'><h2>Recipe not found.</h2></div>";
    endif;
} else {
    echo "<div class='container'><h2>Invalid request.</h2></div>";
}

include __DIR__ . '/includes/footer.php';
?>
