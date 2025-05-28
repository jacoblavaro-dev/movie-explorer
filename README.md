# CinemaSearch 🎬

A modern movie search application with rating system and movie details.

## Project Structure
```
movie-explorer/
├── api/              # Backend API endpoints
│   ├── movie.php     # Single movie details
│   ├── rate.php      # Rating functionality
│   ├── rated.php     # Top rated movies
│   ├── recent.php    # Recently searched movies
│   ├── search.php    # Movie search
│   └── top5.php      # Top 5 most searched movies
├── assets/           # Frontend assets
│   ├── css/          # Stylesheets
│   │   ├── main.css  # Main styles
│   │   └── movie.css # Movie details styles
│   └── js/           # JavaScript files
│       ├── main.js   # Main functionality
│       └── movie.js  # Movie details functionality
├── config/           # Configuration files
│   ├── db.php       # Database connection
│   └── setup.php    # Database setup
└── pages/           # HTML pages
    ├── movie.html   # Movie details page
    ├── rated.html   # Rated movies page
    ├── recent.html  # Recent searches page
    └── top5.html    # Top 5 page
```

## Features
- Movie search with TMDb integration
- Movie ratings system
- Detailed movie information
- Top rated movies list
- Recently searched movies
- Top 5 most searched movies
- Responsive design with modern UI

## Setup
1. Install XAMPP
2. Clone this repository to htdocs
3. Run setup.php to initialize database
4. Access via http://localhost/movie-explorer 