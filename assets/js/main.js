// Constants
const API_KEY = 'bcb16046fef602fb03fb0f90a09967f8';
const TMDB_IMAGE_BASE_URL = 'https://image.tmdb.org/t/p/w500';

// Search functionality
function initializeSearch() {
    $('#search').on('input', function () {
        const query = $(this).val().trim();
        if (query.length < 2) {
            $('#results').empty();
            return;
        }
        searchMovies(query);
    });
}

function searchMovies(query) {
    $.get(`api/search.php?query=${encodeURIComponent(query)}`)
        .done(function (data) {
            displaySearchResults(data);
        })
        .fail(function (error) {
            handleError('Failed to fetch search results');
            console.error('API request failed', error);
        });
}

function displaySearchResults(movies) {
    if (!movies || !movies.length) {
        $('#results').html('<p>No movies found</p>');
        return;
    }
    
    const movieCards = movies.map(movie => createMovieCard(movie)).join('');
    $('#results').html(movieCards);
}

function createMovieCard(movie) {
    return `
        <div class="movie-card">
            <a href="pages/movie.html?id=${movie.id}" class="movie-poster-link">
                <img src="${movie.poster}" alt="${movie.title}" 
                     onerror="this.src='https://via.placeholder.com/500x750.png?text=No+Poster'">
            </a>
            <div class="movie-content">
                <h3>${movie.title}</h3>
                <p class="overview">${movie.overview || 'No overview available.'}</p>
                <p class="release-date"><strong>Release:</strong> ${movie.release_date || 'Release date unknown'}</p>
                <p class="rating">⭐ Avg Rating: ${movie.average_rating}</p>
                <div class="stars" data-id="${movie.id}">
                    ${createStarRating()}
                </div>
            </div>
        </div>
    `;
}

function createStarRating() {
    return [1, 2, 3, 4, 5]
        .map(rating => `<span class="star" data-rating="${rating}">★</span>`)
        .join('');
}

// Rating functionality
function initializeRating() {
    $(document).on('click', '.stars span', function (e) {
        e.preventDefault();
        e.stopPropagation();
        const rating = $(this).data('rating');
        const movieId = $(this).closest('.stars').data('id');
        submitRating(movieId, rating);
        
        // Update visual feedback
        const starsContainer = $(this).closest('.stars');
        starsContainer.find('.star').removeClass('active');
        $(this).addClass('active');
        $(this).prevAll().addClass('active');
    });

    // Add hover effect for stars
    $(document).on('mouseenter', '.stars span', function() {
        const starsContainer = $(this).closest('.stars');
        starsContainer.find('.star').removeClass('hovered');
        $(this).addClass('hovered');
        $(this).prevAll().addClass('hovered');
    });

    $(document).on('mouseleave', '.stars', function() {
        $(this).find('.star').removeClass('hovered');
    });
}

function submitRating(movieId, rating) {
    $.ajax({
        url: 'api/rate.php',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ movie_id: movieId, rating: rating })
    })
    .done(function() {
        // Update the UI to show the rating was successful
        const movieCard = $(`.stars[data-id="${movieId}"]`).closest('.movie-card');
        movieCard.addClass('rated');
        // Refresh the movie's average rating
        $.get(`api/movie.php?id=${movieId}`)
            .done(function(data) {
                if (data && data.average_rating) {
                    movieCard.find('.rating').text(`⭐ Avg Rating: ${data.average_rating}`);
                }
            });
    })
    .fail(function(error) {
        handleError('Failed to submit rating: ' + (error.responseJSON?.error || 'Unknown error'));
    });
}

// Popular movies section
function loadPopularMovies() {
    $.get(`https://api.themoviedb.org/3/movie/popular?api_key=${API_KEY}&language=en-US&page=1`)
        .done(function (data) {
            if (!data.results) {
                handleError('Invalid response from TMDb API');
                return;
            }
            const movies = data.results.slice(0, 8);
            displayPopularMovies(movies);
        })
        .fail(function (error) {
            handleError('Failed to load popular movies');
            console.error('Popular movies request failed', error);
        });
}

function displayPopularMovies(movies) {
    const movieThumbs = movies.map(movie => createMovieThumb(movie)).join('');
    $('#popular-movies').html(movieThumbs);
}

function createMovieThumb(movie) {
    const posterUrl = movie.poster_path ? 
        `${TMDB_IMAGE_BASE_URL}${movie.poster_path}` : 
        'https://via.placeholder.com/500x750.png?text=No+Poster';
        
    return `
        <a href="pages/movie.html?id=${movie.id}" class="movie-thumb">
            <img src="${posterUrl}" alt="${movie.title}" 
                 onerror="this.src='https://via.placeholder.com/500x750.png?text=No+Poster'" />
            <div class="movie-info">
                <h4>${movie.title}</h4>
                <p class="rating">⭐ ${movie.vote_average.toFixed(1)}</p>
            </div>
        </a>
    `;
}

// Error handling
function handleError(message) {
    const errorDiv = $('<div class="error-message"></div>')
        .text(message)
        .fadeIn();
    
    $('#results').prepend(errorDiv);
    
    setTimeout(() => {
        errorDiv.fadeOut(() => errorDiv.remove());
    }, 3000);
}

// Initialize everything when document is ready
$(document).ready(function() {
    initializeSearch();
    initializeRating();
    loadPopularMovies();
}); 