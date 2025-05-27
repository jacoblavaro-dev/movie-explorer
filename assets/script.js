$('#search').on('input', function () {
  const query = $(this).val();
  if (query.length < 2) return;

  $.get(`api/search.php?query=${query}`, function (data) {
    const movies = JSON.parse(data);
    $('#results').html(movies.map(m => `
      <div class="movie-card">
        <img src="${m.poster}" alt="">
        <h3>${m.title}</h3>
        <p>${m.overview}</p>
        <p><strong>Release:</strong> ${m.release_date}</p>
        <p>⭐ Avg Rating: ${m.average_rating}</p>
        <div class="stars">
          ${[1,2,3,4,5].map(i => `<span data-id="${m.id}" data-rating="${i}">★</span>`).join('')}
        </div>
      </div>
    `).join(''));
  });
});

$(document).on('click', '.stars span', function () {
  const rating = $(this).data('rating');
  const movie_id = $(this).data('id');
  $.ajax({
    url: 'api/rate.php',
    method: 'POST',
    data: JSON.stringify({ movie_id, rating }),
    success: () => alert('Thanks for rating!')
  });
});
