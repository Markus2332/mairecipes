<?php
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/functions.php';
include __DIR__ . '/includes/header.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $ingredients = $_POST['ingredients'];
    $instructions = $_POST['instructions'];
    $dietary_preferences = $_POST['dietary_preferences'];
    $cooking_skill = $_POST['cooking_skill'];
    $favorite_cuisine = $_POST['favorite_cuisine'];
    $dietary_restrictions = $_POST['dietary_restrictions'];
    $meal_preferences = implode(', ', $_POST['meal_preferences']);
    $cooking_time = $_POST['cooking_time'];
    $user_id = $_SESSION['user_id']; // Assuming the user is logged in and user_id is stored in session

    // Handle file upload
    $photo = $_FILES['photo'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($photo["name"]);
    $upload_ok = 1;
    $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if file is an actual image
    $check = getimagesize($photo["tmp_name"]);
    if ($check !== false) {
        $upload_ok = 1;
    } else {
        echo "File is not an image.<br>";
        $upload_ok = 0;
    }

    // Check file size
    if ($photo["size"] > 500000) {
        echo "Sorry, your file is too large.<br>";
        $upload_ok = 0;
    }

    // Allow certain file formats
    if ($file_type != "jpg" && $file_type != "png" && $file_type != "jpeg" && $file_type != "gif") {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.<br>";
        $upload_ok = 0;
    }

    // Check if $upload_ok is set to 0 by an error
    if ($upload_ok == 0) {
        echo "Sorry, your file was not uploaded.<br>";
    } else {
        if (move_uploaded_file($photo["tmp_name"], $target_file)) {
            $stmt = $conn->prepare("INSERT INTO recipes (user_id, title, ingredients, instructions, dietary_preferences, cooking_skill, favorite_cuisine, dietary_restrictions, meal_preferences, cooking_time, created_at, photo) VALUES (:user_id, :title, :ingredients, :instructions, :dietary_preferences, :cooking_skill, :favorite_cuisine, :dietary_restrictions, :meal_preferences, :cooking_time, NOW(), :photo)");
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':ingredients', $ingredients);
            $stmt->bindParam(':instructions', $instructions);
            $stmt->bindParam(':dietary_preferences', $dietary_preferences);
            $stmt->bindParam(':cooking_skill', $cooking_skill);
            $stmt->bindParam(':favorite_cuisine', $favorite_cuisine);
            $stmt->bindParam(':dietary_restrictions', $dietary_restrictions);
            $stmt->bindParam(':meal_preferences', $meal_preferences);
            $stmt->bindParam(':cooking_time', $cooking_time);
            $stmt->bindParam(':photo', $target_file);

            if ($stmt->execute()) {
                echo "<div class='alert alert-success'>Recipe added successfully.</div>";
            } else {
                echo "<div class='alert alert-danger'>Error: Could not add recipe.</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>Sorry, there was an error uploading your file. Error code: " . $_FILES['photo']['error'] . "</div>";
        }
    }
}
?>

<div class="container">
    <h2>Add Recipe</h2>
    <form id="addRecipeForm" method="POST" action="" enctype="multipart/form-data">
        <p class="info-text"><b>ADD Recipe:</b></p>
        
        <div class="form-group">
            <label for="title">Recipe Title:</label>
            <input type="text" id="title" name="title" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="ingredients">Ingredients:</label>
            <textarea id="ingredients" name="ingredients" class="form-control" required></textarea>
        </div>
        
        <div class="form-group">
            <label for="instructions">Instructions:</label>
            <textarea id="instructions" name="instructions" class="form-control" required></textarea>
        </div>
        
        <div class="form-group">
            <label for="dietary_preferences">Special Tag:</label>
            <select id="dietary_preferences" name="dietary_preferences" class="form-control">
                <option value="none">None</option>
                <option value="vegetarian">Vegetarian</option>
                <option value="vegan">Vegan</option>
                <option value="gluten_free">Gluten-Free</option>
                <option value="keto">Keto</option>
                <option value="paleo">Paleo</option>
                <option value="pescatarian">Pescatarian</option>
                <option value="halal">Halal</option>
                <option value="kosher">Kosher</option>
            </select>
        </div>

        <div class="form-group">
            <label for="cooking_skill">Cooking Skill Level:</label>
            <select id="cooking_skill" name="cooking_skill" class="form-control">
                <option value="beginner">Beginner</option>
                <option value="intermediate">Intermediate</option>
                <option value="advanced">Advanced</option>
            </select>
        </div>

        <div class="form-group">
            <label for="favorite_cuisine">Cuisine:</label>
            <select id="favorite_cuisine" name="favorite_cuisine" class="form-control">
                <option value="italian">Italian</option>
                <option value="chinese">Chinese</option>
                <option value="mexican">Mexican</option>
                <option value="indian">Indian</option>
                <option value="japanese">Japanese</option>
                <option value="mediterranean">Mediterranean</option>
                <option value="french">French</option>
                <option value="thai">Thai</option>
                <option value="american">American</option>
                <option value="other">Other</option>
            </select>
        </div>

        <div class="form-group">
            <label for="dietary_restrictions">Diet: </label>
            <select id="dietary_restrictions" name="dietary_restrictions" class="form-control">
                <option value="none">None</option>
                <option value="low_carb">Low Carb</option>
                <option value="low_fat">Low Fat</option>
                <option value="low_sodium">Low Sodium</option>
                <option value="low_sugar">Low Sugar</option>
            </select>
        </div>

        <div class="form-group">
            <label for="meal_preferences">Types:</label>
            <select id="meal_preferences" name="meal_preferences[]" class="form-control" multiple>
                <option value="breakfast">Breakfast</option>
                <option value="lunch">Lunch</option>
                <option value="dinner">Dinner</option>
                <option value="snack">Snack</option>
                <option value="dessert">Dessert</option>
                <option value="beverage">Beverage</option>
            </select>
            <p class="comment">Select your preferred meal types (hold Ctrl or Cmd to select multiple).</p>
        </div>

        <div class="form-group">
            <label for="cooking_time">Cooking Time (in minutes):</label>
            <input type="number" id="cooking_time" name="cooking_time" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="photo">Upload Photo:</label>
            <input type="file" name="photo" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Add Recipe</button>
    </form>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
