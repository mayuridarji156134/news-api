# News Aggregator API

A Laravel-based News Aggregator API that aggregates news from various sources and allows users to manage their preferences for news categories, sources, and authors.

## Features

- User Authentication (Registration, Login, Logout) using Laravel Sanctum
- Fetch articles from multiple news APIs (NewsAPI, The Guardian, BBC News, etc.)
- Search and filter articles by keyword, date, category, and source
- Personalized news feed based on user preferences
- Efficient storage and indexing of articles
- Dockerized environment setup
- API documentation with Swagger/OpenAPI
- Unit and Feature tests

---

## Setup Instructions

1) composer install
2) .env changes if required
   change project path in docker.compose.yml for app and nginx service in volumes
   permission for storage and bootstarp folder
3) docker-compose build app
4) docker-compoase up -d 
5) docker-compose exec app php artisan migrate
6) php artisan l5-swagger:generate
7) php artisan articles:fetch

APIs will be accessed on
127.0.0.1:8082/api/documentation
