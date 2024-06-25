<?php
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/functions.php';
include __DIR__ . '/includes/header.php';

$search_query = '';
$recipes = [];

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['query'])) {
    $search_query = $_GET['query'];
    $stmt = $conn->prepare("SELECT * FROM recipes WHERE title LIKE :query");
    $stmt->bindValue(':query', '%' . $search_query . '%');
    $stmt->execute();
    $recipes = $stmt->fetchAll();
}
?>

<div class="container">
    <h2>Search Recipes</h2>
    <form method="GET" action="search.php">
        <div class="form-group">
            <input type="text" name="query" class="form-control" placeholder="Search for recipes..." value="<?php echo htmlspecialchars($search_query); ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Search</button>
    </form>
    <div class="row mt-4">
        <?php if (!empty($recipes)): ?>
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
        <?php else: ?>
            <div class="col-12">
                <p>No recipes found.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
