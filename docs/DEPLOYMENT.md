# FiberFlow — Deployment Runbook (AWS EC2, Linux + Docker)

Single-VM production deployment: Ubuntu 24.04 LTS on AWS EC2 **t4g.medium**
(2 vCPU / 4 GB RAM / 30 GB gp3 EBS), plain HTTP on an Elastic IP.

## Architecture

```
Internet ──▶ :80 ──▶ nginx (Docker) ──▶ app:9000 (php-fpm + queue worker via supervisord)
                              ├── mysql:8.0    (fiberflow, main DB)
                              └── postgis:16   (fiberflow_gis, GIS DB)
```

- No Redis — cache, session and queue all use the `database` driver.
- AI runs via the external **Groq API** (no GPU, no local model). Outbound
  internet is required.
- The queue worker runs **inside the app container** under supervisord
  (`docker/supervisord/supervisord.conf`). No separate queue service.
- No scheduled tasks (`routes/console.php` defines none), so no cron/scheduler
  is needed.
- **No build happens on the server.** The three images (`fiberflow-app`,
  `fiberflow-nginx`, `fiberflow-postgis`) are built natively for **arm64** in
  GitHub Actions (`.github/workflows/cd.yml`), pushed to the public GHCR
  registry, and only **pulled** by this instance — deploy operations never
  exceed the 4 GB RAM budget.

## 1. AWS provisioning

### 1.1 Launch EC2 instance

| Setting | Value |
|---|---|
| AMI | Ubuntu 24.04 LTS (arm64) |
| Instance type | `t4g.medium` (2 vCPU / 4 GB) |
| Storage | 30 GB gp3 (20 GB minimum) |
| Key pair | Create or reuse one |
| Security group | Inbound: `22` (SSH), `80` (HTTP) only |

MySQL/PostGIS expose **no host ports** in `docker-compose.prod.yml` — they are
only reachable inside the Docker network.

### 1.2 Allocate and attach an Elastic IP

1. EC2 → Elastic IPs → Allocate (IPv4, Amazon pool).
2. Associate it with the instance. Free while attached to a running instance.
3. Note the address; it becomes `APP_URL` and the nginx host.

### 1.3 Expected costs (on-demand, us-east-1)

| Item | Cost/mo |
|---|---|
| t4g.medium (2 vCPU/4 GB) | ~$32 |
| 30 GB gp3 EBS | ~$3 |
| Elastic IP (attached) | $0 |
| **Total** | **~$35/mo** + outbound data (~$0.09/GB) |

## 2. Server setup

```bash
ssh -i <your-key.pem> ubuntu@<EIP>

# Docker + compose plugin
sudo apt update && sudo apt install -y docker.io docker-compose-v2
sudo usermod -aG docker $USER
# log out and back in for the group to take effect

# Firewall
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw enable

# Swap (safety margin for MySQL/PostGIS during load)
sudo fallocate -l 4G /swapfile
sudo chmod 600 /swapfile
sudo mkswap /swapfile
sudo swapon /swapfile
echo '/swapfile none swap sw 0 0' | sudo tee -a /etc/fstab
```

## 3. Application deployment

### 3.1 Get the code on the server

```bash
git clone <your-repo-url> fiberflow && cd fiberflow
```

(Or `rsync -av --exclude .git --exclude vendor --exclude node_modules ./ ubuntu@<EIP>:~/fiberflow/`.)

### 3.2 Configure `.env`

```bash
cp .env.example .env
```

Mandatory changes:

| Variable | Value |
|---|---|
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_URL` | `http://<EIP>` |
| `APP_KEY` | run `php artisan key:generate --show` locally (or on the server once) and paste |
| `DB_PASSWORD` | strong MySQL root password |
| `POSTGIS_PASSWORD` | strong PostGIS password |
| `GROQ_API_KEY` | your Groq API key |
| `TELESCOPE_ENABLED` | `false` (unless intentionally used) |

`DB_HOST=mysql`, `POSTGIS_HOST=postgis`, and `POSTGIS_USERNAME=fiberflow`
must stay as in `.env.example` — they are the Docker service names.

### 3.3 Pull and start

```bash
docker compose -f docker-compose.prod.yml up -d
```

Images are pulled from the public GHCR registry:
`ghcr.io/BEN-ESSAHRAOUI-Yassine/fiberflow-{app,nginx,postgis}:latest`.
`pull_policy: always` in `docker-compose.prod.yml` makes `up -d` re-fetch the
`latest` tag automatically.

PostGIS runs its init scripts on **first boot only**: GIS schema creation and
fake data load (`docs/Fake_GIS_data`).

### 3.4 Post-deploy steps

```bash
docker compose -f docker-compose.prod.yml exec app php artisan migrate --force
docker compose -f docker-compose.prod.yml exec app php artisan config:cache
```

### 3.5 Verify

```bash
curl -s http://<EIP>/health          # expect: OK from php-fpm
docker compose -f docker-compose.prod.yml ps
docker compose -f docker-compose.prod.yml logs -f app
```

## 4. Daily operations

### 4.1 Deploy an update

**Automatic (recommended):** pushing to `main` runs CI then CD
(`.github/workflows/cd.yml`) — images are rebuilt on an arm64 GitHub runner,
pushed to GHCR, then the server SSH step runs `git pull`, `compose pull` +
`up -d --remove-orphans`, migrations and cache rebuilds.

**Manual:**

```bash
cd fiberflow && git pull
docker compose -f docker-compose.prod.yml up -d
docker compose -f docker-compose.prod.yml exec app php artisan migrate --force
docker compose -f docker-compose.prod.yml exec app php artisan config:cache
```

### 4.2 Backup (cron, nightly)

```bash
sudo crontab -e
```

```cron
30 2 * * * mkdir -p /home/ubuntu/backups && \
  docker compose -f /home/ubuntu/fiberflow/docker-compose.prod.yml exec -T mysql \
    mysqldump -uroot -p"$DB_PASSWORD" fiberflow | gzip > /home/ubuntu/backups/mysql-$(date +\%F).sql.gz && \
  docker compose -f /home/ubuntu/fiberflow/docker-compose.prod.yml exec -T postgis \
    pg_dump -U fiberflow fiberflow_gis | gzip > /home/ubuntu/backups/postgis-$(date +\%F).sql.gz && \
  find /home/ubuntu/backups -name "*.gz" -mtime +7 -delete
```

Store the backups off-server (e.g. S3) for real disaster recovery.

### 4.3 Troubleshooting

| Symptom | Check |
|---|---|
| `curl /health` fails | `docker compose logs app nginx`; verify app healthcheck passes (`docker compose ps`) |
| DB connection refused | `docker compose logs mysql postgis`; first PostGIS boot may take minutes (init scripts) |
| Vite manifest error (blank CSS/JS) | The app image's `latest` tag is stale or the CD build failed — check the `CD` workflow run, then re-pull: `docker compose pull app && docker compose up -d app` |
| PostGIS init did not run | The volume `postgis_data_prod` already existed from a previous boot — wipe it only if the GIS schema is disposable: `docker compose down && docker volume rm fiberflow_postgis_data_prod` |
| Queue not processing | `docker compose exec app supervisorctl status`; restart: `docker compose exec app supervisorctl restart queue-worker` |

## 5. Resource requirements (reference)

| Resource | Requirement |
|---|---|
| RAM | Runtime ~3 GB total, capped per service (`mem_limit`: app 768 MB, nginx 128 MB, mysql 1 GB, postgis 1 GB); MySQL tuned via `--innodb_buffer_pool_size=512M --performance_schema=OFF`, PostGIS via `shared_buffers=256MB`. No build RAM needed — images come from GHCR |
| CPU | 2 vCPU (FPM + queue worker + DBs); building happens in GitHub Actions, not here |
| Disk | 30 GB gp3 (images ~2 GB, code+vendor ~150 MB, DBs + backups variable) |
| Network | Outbound required (Groq API, GHCR pulls); inbound 22/80 only |

Minimum viable instance: `t3.small` (2 GB) with 2 GB swap — tight, but no
builds happen on the server so it stays borderline. Recommended:
`t4g.medium`. Heavy production: `t4g.large` (8 GB).

## 6. Local development notes

- **Docker Desktop memory:** the VM defaults to an 8 GB cap, which shows as
  5+ GB in Task Manager regardless of the project. Set Settings → Resources →
  Memory to **4096 MB**.
- Local prod-image builds (`docker compose -f docker-compose.prod.yml build`)
  still work thanks to the `build:` blocks kept alongside `image:`; they are
  only needed for testing, never on the EC2 instance.
