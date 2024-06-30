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
                    <button class="btn btn-outline-primary like-button" data-recipe-id="<?php echo $recipe['id']; ?>">Like</button>
                    <a href="recipe.php?id=<?php echo $recipe['id']; ?>" class="btn btn-primary">View Full Recipe</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.like-button').forEach(function(button) {
        button.addEventListener('click', function() {
            var recipeId = this.getAttribute('data-recipe-id');
            fetch('like_recipe.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'recipe_id=' + recipeId
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'liked') {
                    this.classList.remove('btn-outline-primary');
                    this.classList.add('btn-primary');
                    this.innerText = 'Liked';
                } else if (data.status === 'unliked') {
                    this.classList.remove('btn-primary');
                    this.classList.add('btn-outline-primary');
                    this.innerText = 'Like';
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
