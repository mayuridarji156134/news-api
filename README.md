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

### 1. Clone the Repository

```bash
git clone https://github.com/yourusername/news-aggregator-api.git
cd news-aggregator-api

composer install

APP_NAME=NewsAggregatorAPI
APP_URL=http://localhost
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=user
DB_PASSWORD=secret
SANCTUM_STATEFUL_DOMAINS=localhost
