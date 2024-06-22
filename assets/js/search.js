document.getElementById('searchForm').addEventListener('submit', function(event) {
    event.preventDefault();
    var query = document.getElementById('search').value;
    var resultsDiv = document.getElementById('results');
    resultsDiv.innerHTML = 'Searching...';

    fetch(`https://www.themealdb.com/api/json/v1/1/search.php?s=${query}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            resultsDiv.innerHTML = '';
            if (data.meals) {
                data.meals.forEach(meal => {
                    var mealDiv = document.createElement('div');
                    mealDiv.classList.add('meal');

                    var mealName = document.createElement('h3');
                    mealName.textContent = meal.strMeal;
                    mealDiv.appendChild(mealName);

                    var mealImage = document.createElement('img');
                    mealImage.src = meal.strMealThumb;
                    mealImage.alt = meal.strMeal;
                    mealDiv.appendChild(mealImage);

                    var mealInstructions = document.createElement('p');
                    mealInstructions.textContent = meal.strInstructions;
                    mealDiv.appendChild(mealInstructions);

                    resultsDiv.appendChild(mealDiv);
                });
            } else {
                resultsDiv.textContent = 'No results found.';
            }
        })
        .catch(error => {
            resultsDiv.textContent = `An error occurred while searching: ${error.message}`;
            console.error('Error fetching data from TheMealDB API:', error);
        });
});