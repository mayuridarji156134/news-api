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

1. Install Composer Dependencies:
   composer install

2. Configure `.env` File:
   - Update any necessary environment variables in `.env` (e.g., database credentials).

3. Modify Docker Configuration:
   - Update the project path in `docker-compose.yml` for the `app` and `nginx` services.
   - Ensure the correct file permissions are set for the `storage` and `bootstrap/cache` directories.

4. Build and Run Docker Containers:
   - docker-compose build app
   - docker-compose up -d

5. Run Database Migrations:
    docker-compose exec app php artisan migrate
   	
   	To seed the database with initial data (if required):
	docker-compose exec app php artisan db:seed


6. Generate Swagger Documentation:
   php artisan l5-swagger:generate

7. Fetch Articles:
   php artisan articles:fetch

8. Testing the Application

	Run unit and feature tests:
    php artisan test

9. Scheduling News Fetching

	To aggregate articles from external sources, Laravel's scheduler will automatically fetch news at regular intervals. To run the scheduler locally, run:

	php artisan schedule:work

	In production, you'll set up a cron job to handle this:

	* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1


APIs will be accessible at:
127.0.0.1:8082/api/documentation

---
API Endpoints Overview:

1. Authentication

POST /api/register - User registration

POST /api/login - User login

POST /api/logout - User logout (Require Bearer token)

POST /api/password/reset - Password reset


2. Articles

GET /api/articles - Fetch all articles (with pagination)

GET /api/articles/{id} - Get details of a single article

GET /api/articles/search - Search articles by keyword, date, category, and source


3. User Preferences (Require Beare token)

GET /api/user/preferences - Get user preferences (sources, categories, authors)

POST /api/user/preferences - Set user preferences

GET /api/user/feed - Get personalized news feed based on preferences


4. Swagger Documentation

GET /api/documentation - View Swagger API documentation


---
For more information, refer to the API documentation at 127.0.0.1:8082/api/documentation.


---

Additional Notes

1. Data Aggregation: Articles are fetched using the Laravel scheduler at regular intervals. The fetched articles are stored in the local database, and all filtering/searching is done on this stored data, not on live API data.


2. Caching: The system implements caching to optimize API performance, particularly for frequently accessed routes.


3. Security: Laravel Sanctum is used for API token-based authentication. All API routes are protected with appropriate middleware to ensure that only authenticated users can access certain endpoints.


4. Performance: Implemented indexing on the articles table for optimizing search operations. Rate limiting has been applied to prevent abuse of public endpoints.



---

Docker Development Setup

1. Build the Docker containers:

docker-compose build


2. Start the containers:

docker-compose up -d


3. Stopping the containers:

docker-compose down




---
