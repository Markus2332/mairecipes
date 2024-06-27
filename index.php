<?php
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/functions.php';
include __DIR__ . '/includes/header.php';

$stmt = $conn->prepare("SELECT * FROM recipes");
$stmt->execute();
$recipes = $stmt->fetchAll();
?>

<div class="container">
    <h2>Recipes</h2>
    <?php if (is_logged_in()): ?>
        <div class="text-right mb-3">
            <a href="recipe_add.php" class="btn btn-success">Add Recipe</a>
        </div>
    <?php endif; ?>
    <div class="row">
        <?php foreach ($recipes as $recipe): ?>
        <div class="col-md-6 mb-4">
            <div class="card">
                <img src="<?php echo htmlspecialchars($recipe['photo']); ?>" class="card-img-top" alt="Recipe Photo">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($recipe['title']); ?></h5>
                    <p class="card-text">
                        <strong>Cooking Time:</strong> <?php echo htmlspecialchars($recipe['cooking_time']); ?> minutes<br>
                        <strong>Ingredients:</strong> <?php echo htmlspecialchars($recipe['ingredients']); ?><br>
                        <strong>Instructions:</strong> <?php echo htmlspecialchars($recipe['instructions']); ?><br>
                    </p>
                    <a href="recipe.php?id=<?php echo $recipe['id']; ?>" class="btn btn-primary">View Full Recipe</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
