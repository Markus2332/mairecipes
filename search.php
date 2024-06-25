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
                        <a href="recipe.php?id=<?php echo $meal['idMeal']; ?>" class="btn btn-primary">View Full Recipe</a>
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

<?php
include __DIR__ . '/includes/footer.php';
?>
