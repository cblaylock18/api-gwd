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

**Production URL:** `https://api-369920917738.us-west1.run.app`

## Cloud Run commands

**Manually deploy a new image:**

```bash
gcloud run deploy api \
  --image=us-west1-docker.pkg.dev/quizgame-491018/gwd/api:latest \
  --region=us-west1 \
  --platform=managed \
  --allow-unauthenticated \
  --set-secrets=DB_USER=DB_USER:latest,DB_PASSWORD=DB_PASSWORD:latest,DB_NAME=DB_NAME:latest \
  --set-cloudsql-instances=quizgame-491018:us-west1:quizgame
```

**Check logs:**

```bash
gcloud logging read "resource.type=cloud_run_revision AND resource.labels.service_name=api" \
  --limit=50 \
  --format="value(textPayload)" \
  --project=quizgame-491018
```
