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
    <?php if (is_logged_in()): ?>
        <button class="btn btn-outline-primary like-button" data-recipe-id="<?php echo $recipe['id']; ?>">Like</button>
    <?php endif; ?>
    <a href="index.php" class="btn btn-secondary">Back to Recipes</a>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelector('.like-button').addEventListener('click', function() {
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
</script>

<?php
    else:
        echo "<div class='container'><h2>Recipe not found.</h2></div>";
    endif;
} else {
    echo "<div class='container'><h2>Invalid request.</h2></div>";
}

include __DIR__ . '/includes/footer.php';
?>
