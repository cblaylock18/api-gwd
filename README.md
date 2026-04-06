# api-gwd

PHP Apache API serving Thrice by Geeks Who Drink quiz data from Cloud SQL.

## What it does

Exposes REST endpoints for the quiz game data scraped by `bot-gwd-scraper`. Deployed as a Cloud Run service and publicly accessible.

## Repo structure

api-gwd/
  src/
    index.php    # Main entry point, routes and responds to requests
  Dockerfile     # PHP 8.2 Apache image configured for Cloud Run

## Environment variables

| Variable | Description |
| ---------- | ------------- |
| `DB_HOST` | DB hostname. Set to `db` in Docker Compose. Omit for Cloud Run (uses socket). |
| `DB_USER` | MySQL username |
| `DB_PASSWORD` | MySQL password |
| `DB_NAME` | Database name |

## Local development

Requires Docker and Docker Compose running from the `gwd-project` repo. Source files are mounted as a volume so changes reflect immediately without rebuilding.

**Start the API locally:**

```bash
cd ../gwd-project
docker compose up -d api
```

**Test the API:**

```bash
curl http://localhost:8080
```

Changes to files in `src/` are reflected immediately — no rebuild needed during local development.

## Endpoints

| Method | Path | Description               |
|--------|------|-------------------------- |
| `GET`  | `/`  | Returns today's game data |

More endpoints to be added.

## CI/CD

On push to `main`, GitHub Actions:

1. Builds the Docker image
2. Pushes it to Google Artifact Registry (`us-west1-docker.pkg.dev/quizgame-491018/gwd/api:latest`)
3. Deploys the new image to the Cloud Run service