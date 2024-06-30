<?php
include __DIR__ . '/includes/header.php';

if (!is_logged_in()) {
    echo "<div class='container'><h2>Please log in to view your saved recipes.</h2></div>";
    include __DIR__ . '/includes/footer.php';
    exit();
}

?>

<div class="container">
    <h2>Saved Recipes</h2>
    <div class="row" id="saved-recipes">
        <!-- Saved recipes will be displayed here -->
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    fetch('get_saved_recipes.php')
    .then(response => response.json())
    .then(data => {
        var container = document.getElementById('saved-recipes');
        if (data.length > 0) {
            data.forEach(recipe => {
                var card = `
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <img src="${recipe.photo}" class="card-img-top" alt="Recipe Photo">
                            <div class="card-body">
                                <h5 class="card-title">${recipe.title}</h5>
                                <p class="card-text">
                                    <strong>Cooking Time:</strong> ${recipe.cooking_time} minutes<br>
                                    <strong>Ingredients:</strong> ${recipe.ingredients}<br>
                                    <strong>Instructions:</strong> ${recipe.instructions}<br>
                                </p>
                                <a href="recipe.php?id=${recipe.id}" class="btn btn-primary">View Full Recipe</a>
                            </div>
                        </div>
                    </div>
                `;
                container.innerHTML += card;
            });
        } else {
            container.innerHTML = '<p>No saved recipes found.</p>';
        }
    })
    .catch(error => console.error('Error:', error));
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
