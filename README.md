## Requirements
- [Docker](https://www.docker.com/)
- [Docker Compose](https://docs.docker.com/compose/)

### Outside of Docker
- PHP 8.1
- Composer ^2
- PostgreSQL 14

## Getting Started

1. Copy `.env.example` to `.env` ou run the command `make env` if you have `make` installed on your machine
2. (optional) Fill the blank fields in `.env` file if you want
3. Build and run the container: `docker compose build && docker compose up -d` or `make build-up` if you have make installed on your machine
4. Run the command inside the container (by doing `docker exec -it symfony-rest-php` or `make bash`): `make install-project`
5. Open the browser at [https://localhost](https://localhost) and accept the self-signed certificate to verify that the project is running
6. You can run all the tests with the command `make pest-all` inside the container

If you want to stop the container, run this command: `make prune`

## Routes
### USER
```
GET /user
GET /user/{uuid}
POST /user
POST /register
PATCH /user/{uuid}
DELETE /user/{uuid}
```
