# Rukometni Centar

Rukometni Centar is a single-page web application for managing a handball league. The platform allows users to track clubs, players, fixtures, standings, venues, and user login/registration flows in a clean and responsive interface.

## Project overview

This project is being developed as a PHP + JavaScript + MySQL application with a REST API backend and a mobile-friendly frontend. The initial milestone focuses on the app setup, static frontend, and database/entity design.

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

1. Import `database/rukometni_centar.sql` into MySQL. It creates the `rukometni_centar` database, all seven tables, and sample rows.
2. Optionally copy `backend/config/config.example.php` to `backend/config/config.php` if you need local settings different from the defaults.
3. Configure `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, and `DB_PASSWORD` in the environment or local config.

`backend/config/config.php` is a private local override and must not be committed. The example configuration provides local development defaults.

The schema contains roles, users, teams, players, venues, matches, and standings. Foreign keys connect users to roles, players and standings to teams, and matches to their home team, away team, and venue. Unique and check constraints enforce account, roster, fixture, and standings integrity.

The DAO layer provides prepared-statement CRUD operations through `BaseDao` and a table-specific DAO for each entity. The user list excludes password hashes; user lookup by email is intended for authentication code.

Run the DAO smoke tests after importing the database and configuring MySQL:

```powershell
php backend/rest/dao/test.php
```

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
  config/
    config.example.php
  rest/
    routes/
    services/
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

## Deployment and API docs

- Swagger/OpenAPI: to be added in later milestones
- Deployed app: to be added when public hosting is completed

## AI usage documentation

This project follows the course requirement that AI usage is documented. See:

- [AGENTS.md](AGENTS.md)
- [docs/AI_LOG.md](docs/AI_LOG.md)

## Notes

- Backend credentials and secret values must be kept out of GitHub.
- All logic must be separated cleanly between backend and frontend.
- The frontend remains static in this milestone and will be fully connected to the REST API in later milestones.
