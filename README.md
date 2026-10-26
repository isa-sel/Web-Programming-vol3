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

## Roles and test accounts

The application is designed around two primary roles:

- Admin
  - email: admin@rukometnicentar.com
  - password: admin123
- User
  - email: user@rukometnicentar.com
  - password: user123

These credentials are for local testing and should be replaced in the final environment with secure values.

## Local setup

1. Create a MySQL database named `rukometni_centar`.
2. Import the SQL schema from `backend/sql/schema.sql`.
3. Copy `backend/config.example.php` to `backend/config.php` and fill in your local database and secret values.
4. Start the PHP server from the project root or serve the frontend from the `frontend` directory.
5. Open the app in the browser.

Example local PHP server:

```bash
cd frontend
php -S localhost:8000
```

## Repository structure

```text
frontend/
  index.html
  css/
  js/
  templates/
backend/
  config.example.php
  sql/
    schema.sql
  routes/
    services/
      dao/
        middleware/
README.md
AGENTS.md
docs/
  AI_LOG.md
```

## Milestone 1 checklist

This milestone includes:

- setup and project structure
- static frontend pages
- simplified SPA navigation
- ERD/database model
- project README and AI documentation files

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
