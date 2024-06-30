<?php
include __DIR__ . '/includes/header.php';

$query = isset($_GET['s']) ? urlencode($_GET['s']) : '';
$url = "https://www.themealdb.com/api/json/v1/1/search.php?s=" . $query;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

$meals = [];

if ($httpcode >= 200 && $httpcode < 300) {
    $data = json_decode($response, true);
    if ($data && isset($data['meals'])) {
        $meals = $data['meals'];
    }
} else {
    echo "<div class='alert alert-danger'>Unable to fetch data from TheMealDB API. HTTP Code: $httpcode. Error: $error</div>";
}

?>

<div class="container">
    <h2>Search Recipes</h2>
    <form method="GET" action="search.php">
        <div class="form-group">
            <label for="search">Search for a recipe:</label>
            <input type="text" id="search" name="s" class="form-control" value="<?php echo htmlspecialchars($query); ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Search</button>
    </form>
    <div class="row mt-4">
        <?php if (!empty($meals)): ?>
            <?php foreach ($meals as $meal): ?>
            <div class="col-md-6 mb-4">
                <div class="card">
                    <img src="<?php echo htmlspecialchars($meal['strMealThumb']); ?>" class="card-img-top" alt="Recipe Photo">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($meal['strMeal']); ?></h5>
                        <p class="card-text">
                            <strong>Category:</strong> <?php echo htmlspecialchars($meal['strCategory']); ?><br>
                            <strong>Area:</strong> <?php echo htmlspecialchars($meal['strArea']); ?><br>
                            <strong>Instructions:</strong> <?php echo htmlspecialchars($meal['strInstructions']); ?><br>
                        </p>
                        <?php if (is_logged_in()): ?>
                            <button class="btn btn-outline-primary like-button" data-recipe-id="<?php echo htmlspecialchars($meal['idMeal']); ?>">Like</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <p>No recipes found.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.like-button').forEach(function(button) {
        button.addEventListener('click', function() {
            var recipeId = this.getAttribute('data-recipe-id');
            var title = this.closest('.card-body').querySelector('.card-title').innerText;
            var category = this.closest('.card-body').querySelector('.card-text').innerText.split('\n')[0].split(': ')[1];
            var area = this.closest('.card-body').querySelector('.card-text').innerText.split('\n')[1].split(': ')[1];
            var instructions = this.closest('.card-body').querySelector('.card-text').innerText.split('\n')[2].split(': ')[1];
            var photo = this.closest('.card').querySelector('.card-img-top').src;

            fetch('like_recipe.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    recipe_id: recipeId,
                    title: title,
                    category: category,
                    area: area,
                    instructions: instructions,
                    photo: photo
                })
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
                } else if (data.error) {
                    console.error('Error:', data.error);
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });
});
</script>

<?php
include __DIR__ . '/includes/footer.php';
?>
