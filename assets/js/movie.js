// Constants
const TMDB_API_KEY = 'bcb16046fef602fb03fb0f90a09967f8';
const TMDB_IMAGE_BASE_URL = 'https://image.tmdb.org/t/p/w500';

$(document).ready(function() {
    const urlParams = new URLSearchParams(window.location.search);
    const movieId = urlParams.get('id');

    if (!movieId) {
        showError('No movie ID provided');
        return;
    }

    loadMovieDetails(movieId);
});

function loadMovieDetails(movieId) {
    // Load movie details from our database
    $.get(`../api/movie.php?id=${movieId}`)
        .done(function(movie) {
            // Load additional details from TMDB
            $.get(`https://api.themoviedb.org/3/movie/${movieId}?api_key=${TMDB_API_KEY}&append_to_response=videos,credits,similar`)
                .done(function(tmdbData) {
                    displayMovieDetails({ ...movie, ...tmdbData });
                })
                .fail(function(error) {
                    console.error('TMDB API request failed:', error);
                    displayMovieDetails(movie); // Fallback to basic details
                });
        })
        .fail(function(error) {
            showError('Failed to load movie details');
            console.error('API request failed:', error);
        });
}

function displayMovieDetails(movie) {
    const content = `
        <div class="movie-header">
            <img src="${movie.poster_url || `${TMDB_IMAGE_BASE_URL}${movie.poster_path}`}" 
                 alt="${movie.title}" class="movie-poster" 
                 onerror="this.src='https://via.placeholder.com/500x750.png?text=No+Poster'">
            <div class="movie-info">
                <h1 class="movie-title">${movie.title}</h1>
                <div class="movie-meta">
                    <p>Release Date: ${formatDate(movie.release_date)}</p>
                    ${movie.runtime ? `<p>Runtime: ${movie.runtime} minutes</p>` : ''}
                    ${movie.genres ? `<p>Genres: ${movie.genres.map(g => g.name).join(', ')}</p>` : ''}
                </div>
                <p class="movie-overview">${movie.overview || 'No overview available.'}</p>
                <div class="rating-section">
                    <h3>Rate this Movie</h3>
                    <div class="stars" data-id="${movie.id}">
                        ${createStarRating()}
                    </div>
                    <div class="rating-stats">
                        <div class="rating-stat">
                            <div class="value">${movie.average_rating}</div>
                            <div class="label">Average Rating</div>
                        </div>
                        <div class="rating-stat">
                            <div class="value">${movie.rating_count}</div>
                            <div class="label">Total Ratings</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        ${displayTrailer(movie.videos?.results)}

        ${displayCast(movie.credits?.cast)}

        ${displaySimilarMovies(movie.similar?.results)}
    `;

    $('#movie-details').html(content);
    initializeRating();
}

function displayTrailer(videos) {
    if (!videos || !videos.length) return '';
    
    const trailer = videos.find(v => v.type === 'Trailer') || videos[0];
    return `
        <div class="movie-section">
            <h2>Trailer</h2>
            <div class="trailer-container">
                <iframe 
                    src="https://www.youtube.com/embed/${trailer.key}"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen>
                </iframe>
            </div>
        </div>
    `;
}

function displayCast(cast) {
    if (!cast || !cast.length) return '';

    const castMembers = cast.slice(0, 8).map(member => `
        <div class="cast-member">
            <img src="${member.profile_path ? 
                `${TMDB_IMAGE_BASE_URL}${member.profile_path}` : 
                'https://via.placeholder.com/185x278.png?text=No+Image'}"
                alt="${member.name}">
            <h4>${member.name}</h4>
            <p>${member.character}</p>
        </div>
    `).join('');

    return `
        <div class="movie-section">
            <h2>Cast</h2>
            <div class="cast-grid">
                ${castMembers}
            </div>
        </div>
    `;
}

function displaySimilarMovies(movies) {
    if (!movies || !movies.length) return '';

    const similarMovies = movies.slice(0, 6).map(movie => `
        <a href="movie.html?id=${movie.id}" class="similar-movie">
            <img src="${movie.poster_path ? 
                `${TMDB_IMAGE_BASE_URL}${movie.poster_path}` : 
                'https://via.placeholder.com/500x750.png?text=No+Poster'}"
                alt="${movie.title}">
            <h4>${movie.title}</h4>
            <p>⭐ ${movie.vote_average.toFixed(1)}</p>
        </a>
    `).join('');

    return `
        <div class="movie-section">
            <h2>Similar Movies</h2>
            <div class="similar-movies-grid">
                ${similarMovies}
            </div>
        </div>
    `;
}

function createStarRating() {
    return [1, 2, 3, 4, 5]
        .map(rating => `<span class="star" data-rating="${rating}">★</span>`)
        .join('');
}

function initializeRating() {
    $('.stars').on('mouseenter', '.star', function() {
        const starsContainer = $(this).closest('.stars');
        starsContainer.find('.star').removeClass('hovered');
        $(this).addClass('hovered');
        $(this).prevAll().addClass('hovered');
    });

    $('.stars').on('mouseleave', function() {
        $(this).find('.star').removeClass('hovered');
    });

    $('.stars').on('click', '.star', function() {
        const rating = $(this).data('rating');
        const movieId = $(this).closest('.stars').data('id');
        submitRating(movieId, rating);
        
        // Update visual feedback
        const starsContainer = $(this).closest('.stars');
        starsContainer.find('.star').removeClass('active');
        $(this).addClass('active');
        $(this).prevAll().addClass('active');
    });
}

function submitRating(movieId, rating) {
    $.ajax({
        url: '../api/rate.php',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ movie_id: movieId, rating: rating })
    })
    .done(function() {
        // Refresh the movie details to update ratings
        loadMovieDetails(movieId);
    })
    .fail(function(error) {
        showError('Failed to submit rating: ' + (error.responseJSON?.error || 'Unknown error'));
    });
}

function formatDate(dateString) {
    if (!dateString) return 'Release date unknown';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

function showError(message) {
    const errorHtml = `
        <div class="error-message">
            <p>${message}</p>
            <a href="../index.html" class="back-button">Return to Search</a>
        </div>
    `;
    $('#movie-details').html(errorHtml);
} 