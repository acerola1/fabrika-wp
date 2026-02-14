# WordPress local Docker setup

## 1) Env file

```bash
cp .env.example .env
```

At minimum, change passwords in `.env`.

## 2) Start WordPress

```bash
docker compose up -d
```

WordPress will be available at: `http://localhost:8080`

## 3) Stop

```bash
docker compose down
```

If you also want to remove DB/content volumes:

```bash
docker compose down -v
```
