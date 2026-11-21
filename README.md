# Rukometni Centar

Rukometni Centar is a single-page web application for managing a handball league. The platform allows users to track clubs, players, fixtures, standings, venues, and user login/registration flows in a clean and responsive interface.

## Project overview

This project is being developed as a PHP + JavaScript + MySQL application with a REST API backend (FlightPHP) and a mobile-friendly frontend. The backend has three layers: routes receive HTTP requests and answer with JSON, services check the input and the business rules, and DAOs run the SQL with PDO.

## Entities and relationships

The main application entities are:

- Users
  - stores user account data and role assignment
- Roles
  - identifies access levels such as admin and regular user
- Teams
  - each team belongs to a city and has a category
- Players
  - each player belongs to a team and has a position and jersey number
- Matches
  - each match is played between two teams in a venue on a date/time
- Venues
  - each venue has a capacity and address
- Standings
  - stores season statistics for teams

Relationship summary:

- One user has one role
- One team has many players
- One match has two teams
- One venue hosts many matches
- One team has many standings records across seasons

## Roles and sample accounts

The database seed includes three roles and these sample accounts. Passwords are stored as bcrypt hashes.

| Role | Email | Password |
|---|---|---|
| Admin | `admin@rukometnicentar.com` | `admin123` |
| Manager | `manager@rukometnicentar.com` | `manager123` |
| User | `user@rukometnicentar.com` | `user123` |

## Local setup

1. Import `database/rukometni_centar.sql` into MySQL. It creates the `rukometni_centar` database, all seven tables, and sample rows. When you import it from the command line, use UTF-8, e.g. `mysql -u root --default-character-set=utf8mb4 < database/rukometni_centar.sql`.
2. Install the PHP packages (FlightPHP and swagger-php) with [Composer](https://getcomposer.org) in the `backend` folder: `cd backend` and `composer install`. The `vendor/` folder is not committed.
3. Optionally copy `backend/config/config.example.php` to `backend/config/config.php` if you need local settings different from the defaults.
4. Configure `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, and `DB_PASSWORD` in the environment or local config.

`backend/config/config.php` is a private local override and must not be committed. The example configuration provides local development defaults.

5. Start Apache and MySQL in XAMPP. With the project in `htdocs/Web-Programming-vol-3`, the API runs at `http://localhost/Web-Programming-vol-3/backend/` and the Swagger documentation at `http://localhost/Web-Programming-vol-3/backend/public/v1/docs/`.

The schema contains roles, users, teams, players, venues, matches, and standings. Foreign keys connect users to roles, players and standings to teams, and matches to their home team, away team, and venue. Unique and check constraints enforce account, roster, fixture, and standings integrity.

The DAO layer provides prepared-statement CRUD operations through `BaseDao` and a table-specific DAO for each entity. The user list excludes password hashes; user lookup by email is intended for authentication code.

Run the DAO smoke tests after importing the database and configuring MySQL:

```powershell
php backend/rest/dao/test.php
```

Run the service tests, which try to break every business rule and delete everything they add:

```powershell
php backend/rest/services/test.php
```

## REST API

Every entity has the same five endpoints, for example for teams:

| Method | URL | Answer |
|---|---|---|
| GET | `/teams` | 200, all teams |
| GET | `/teams/{id}` | 200, or 404 |
| POST | `/teams` | 201 with the created team, or 400 |
| PUT | `/teams/{id}` | 200 with the updated team, 400 or 404 |
| DELETE | `/teams/{id}` | 200, 400 (a rule forbids it) or 404 |

The resources are `roles`, `users`, `teams`, `players`, `venues`, `matches` and `standings`. Additional endpoints:

- `GET /teams/{id}/players` and `GET /teams/{id}/matches`
- `GET /players/with-team` (players with the name and city of their team)
- `GET /standings?season=2026-2027` (the table of one season, ordered by position)
- `GET /matches/{id}/weather` (external API, see below)

Every error has the shape `{"error": "..."}`. Unexpected errors answer 500 with a general message; the details are written only to `backend/logs/error.log`.

Main business rules (in `backend/rest/services`): unique role names, team names, venue names and user e-mails; passwords of at least 8 characters with a letter and a number, stored only as bcrypt hashes; jersey numbers 1-99 and unique within a team; a match between two different existing teams, with scores only when it is completed; a new scheduled match must be in the future; standings `played` and `points` (2 per win, 1 per draw) are calculated by the server; roles, teams and venues that are still in use cannot be deleted.

## External API

The weather feature uses [Open-Meteo](https://open-meteo.com), which needs no API key:

1. the Geocoding API finds the coordinates of the venue city of a match;
2. the Forecast API returns the hourly weather of the match day, and the hour of the throw-off is returned by `GET /matches/{id}/weather`.

It works for matches from 92 days ago to 15 days ahead. Every call has a timeout (5 seconds by default, `WEATHER_TIMEOUT_SECONDS`), every call is written to `backend/logs/info.log`, and every failure to `backend/logs/error.log`; the endpoint then answers 503 with a friendly message and the rest of the API keeps working. The frontend weather widget on the match page is added in Milestone 4.

## Repository structure

```text
frontend/
  index.html
  assets/
    css/
    images/
    js/
  views/
  services/
  utils/
backend/
  index.php
  .htaccess
  composer.json
  composer.lock
  config/
    config.example.php
  utils/
    exceptions.php
    Validator.php
    Logger.php
  public/
    v1/
      docs/
        index.html
        swagger.php
        doc_setup.php
        paths/
  rest/
    routes/
      RoleRoutes.php, UserRoutes.php, TeamRoutes.php, PlayerRoutes.php,
      VenueRoutes.php, MatchRoutes.php, StandingRoutes.php
    services/
      BaseService.php, RoleService.php, UserService.php, TeamService.php,
      PlayerService.php, VenueService.php, MatchService.php,
      StandingService.php, WeatherService.php, test.php
    dao/
      BaseDao.php
      RoleDao.php
      UserDao.php
      TeamDao.php
      PlayerDao.php
      VenueDao.php
      MatchDao.php
      StandingDao.php
      test.php
    middleware/
  logs/
database/
  rukometni_centar.sql
README.md
AGENTS.md
docs/
  AI_LOG.md
  ERD.md
.gitignore
```

## Milestone 1 checklist

This milestone includes:

- setup and project structure
- static frontend pages
- simplified SPA navigation
- ERD/database model
- project README and AI documentation files

## Milestone 2 checklist

- MySQL schema and bcrypt-hashed sample accounts in `database/rukometni_centar.sql`
- Shared PDO connection and prepared-statement CRUD DAOs for all seven tables
- DAO smoke test in `backend/rest/dao/test.php`

## Milestone 3 checklist

- One service per entity with validation and business rules, built on `BaseService`, `Validator` and HTTP exceptions
- FlightPHP routes for all CRUD operations of all entities, with one global JSON error handler
- OpenAPI documentation with Swagger UI in `backend/public/v1/docs/`
- External API: match weather from Open-Meteo with timeouts, error logging and a 503 fallback
- Service tests in `backend/rest/services/test.php`

## Deployment and API docs

- Swagger/OpenAPI (local): `http://localhost/Web-Programming-vol-3/backend/public/v1/docs/`
- Deployed app: to be added when public hosting is completed

## AI usage documentation

This project follows the course requirement that AI usage is documented. See:

- [AGENTS.md](AGENTS.md)
- [docs/AI_LOG.md](docs/AI_LOG.md)

## Notes

- Backend credentials and secret values must be kept out of GitHub.
- All logic must be separated cleanly between backend and frontend.
- The frontend is still static; it is connected to the REST API in Milestone 4, together with JWT authentication, role-based access and request logging.
