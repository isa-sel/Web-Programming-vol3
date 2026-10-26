# AGENTS.md

## Project description
This project is a handball league management web application built with PHP, MySQL, JavaScript, and Bootstrap. The goal is to create a responsive single-page application for managing clubs, players, matches, standings, venues, and users.

## Tech stack
- PHP
- FlightPHP (required in later milestones)
- MySQL
- PDO
- JavaScript
- Bootstrap
- HTML/CSS
- REST API architecture

## App purpose
The application should allow users to:
- browse and manage teams
- manage players and their team assignments
- view and create matches
- track standings and league results
- manage venues
- register and log in as different user roles
- use the app through a mobile-friendly single-page interface

## Project structure
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

## Architecture rules
1. Frontend must contain only HTML, CSS, and JavaScript.
2. Backend must contain PHP logic only.
3. No mixing frontend and backend code in the same files.
4. Use REST API endpoints for all data access.
5. Use PDO for database queries.
6. Use FlightPHP for API routing in later milestones.
7. Keep code modular and reusable.
8. Store secrets and credentials in a local config file that is not committed to GitHub.

## Naming conventions
- JavaScript: camelCase
- PHP: camelCase for methods, snake_case for DB fields
- SQL tables: snake_case
- Files: descriptive lowercase names
- Routes: lowercase and explicit

## Database expectations
The project should have at least 5 entities such as:
- users
- roles
- teams
- players
- matches
- venues
- standings

Every entity should support basic CRUD behavior in later milestones.

## Authentication and authorization
- JWT must be used for authentication/authorization.
- At least two user roles must exist.
- Protected routes must validate tokens and enforce role-based access.

## API and documentation requirements
- The backend API must be documented using OpenAPI/Swagger.
- AJAX requests should be used for frontend communication.
- The frontend must be fully connected to the backend by the end of Milestone 4.

## External API requirement
At least one third-party external API must be integrated and used in a meaningful UI feature. The integration must gracefully handle failures and log all relevant errors.

## Logging requirements
- Info logs must record requests, successful login/register events, and external API usage.
- Error logs must record failed authentication, rejected access, database issues, and API failures.

## AI usage rules
- AI tools may be used only in a controlled and documented way.
- Every milestone must include updated AI documentation.
- If AI is not used, that must be clearly stated in AI_LOG.md.
- You must understand and be able to explain every part of the generated code.

## Final instructions for coding assistants
- Follow the architecture and folder structure exactly.
- Keep the frontend static before the API is connected.
- Do not hardcode data where database-backed data is expected.
- Do not commit secrets or credentials.
- Validate the code before finishing a task.
- If a suggestion conflicts with the project requirements, prefer the project requirements.
