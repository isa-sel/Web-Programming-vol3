# AI usage log

## Milestone 1

### Tools used

- GitHub Copilot

### Main uses

- Initial project review and structure assessment
- Suggesting a clean folder layout for the PHP and frontend separation
- Preparing a project README and architecture guidance
- Building the database schema and setup documentation

### Important prompts

1. "Review the current SPA project and identify what is missing for Milestone 1 based on the project requirements."
2. "Create a README and AGENTS.md for a PHP/MySQL handball management app with static frontend and ERD-ready data model."
3. "Draft an SQL schema with users, roles, teams, players, matches, venues, and standings for the project."

### Cases where AI output was not correct

#### Case 1: Over-optimistic backend assumptions
The AI initially suggested that full backend connectivity and JWT setup were already present. This was incorrect because the project was still only a static frontend with a partial backend scaffold.

How it was noticed:
- the codebase had no actual FlightPHP routes, real database DAO layer, or database schema yet
- the milestone document clearly states that full backend connectivity is required only in later milestones

How it was fixed:
- the project was re-evaluated against the actual milestone requirements
- the final work focused on the static frontend plus the required M1 project documentation and schema

#### Case 2: Secret handling and config management
The AI initially recommended committing a live config file with database credentials and secrets. That would violate the project rule.

How it was noticed:
- the project requirements explicitly say credentials must not be committed to GitHub and must be replaced with a template file

How it was fixed:
- a template file was created instead of a real config
- .gitignore rules were added to exclude local secret files and log output

### Final note

For this milestone, AI was used only as a support tool for structure, documentation, and schema planning. The project architecture and final decisions were reviewed and adjusted manually to match the actual course requirements.

## Milestone 1 structure correction

### Feedback addressed

- The frontend views directory and backend directory structure did not match the course project structure.

### AI assistance

- Compared the repository layout with the supplied Frontend/Backend Project Structure lecture.
- Reorganized frontend views, styles, and scripts into `frontend/views/` and `frontend/assets/`.
- Reorganized backend routing, database access, and response middleware into `backend/rest/`, and added the backend entry point and rewrite rules.
- Updated documentation and all affected asset and view paths.

### Review

- Existing page content and API response behavior were preserved while paths and backend responsibilities were moved.
- Runtime configuration remains local and ignored by Git; only the example configuration is tracked.

## Milestone 2

### Tools used

- GitHub Copilot

### Main uses

- Writing `database/rukometni_centar.sql`: the seven tables from the ERD (roles, users, teams, players, venues, matches, standings) with primary and foreign keys, `NOT NULL`, `UNIQUE` and `CHECK` constraints, and sample rows inserted in foreign-key order
- Generating bcrypt hashes for the three sample accounts
- Writing the `Config` class in `backend/config/config.example.php`, which reads the database settings with `getenv()` and falls back to local defaults
- Writing `BaseDao` (one shared PDO connection, prepared statements, CRUD methods, column allow-lists and transactions) and one DAO per table, including a player/team JOIN and a standings GROUP BY query
- Writing the DAO smoke test `backend/rest/dao/test.php`, which runs inside a transaction and rolls back
- Updating `README.md` and `AGENTS.md` with the database setup and DAO paths

### Review

- The SQL file was imported into a local MariaDB server and all seven tables and sample rows were checked.
- The DAO smoke test passed, and the PHP files passed `php -l`.
- No real credentials were committed; `backend/config/config.php` stays ignored by Git.

### Cases where AI output was not correct

Both problems were in the Milestone 2 code and were found while working on Milestone 3.

#### Case 1: The SQL file did not set the connection character set
`database/rukometni_centar.sql` creates the database with `utf8mb4`, but it did not tell the MySQL client how the file itself is encoded. When the file was imported from a Windows console, names with č, ć, đ, š and ž (for example "HRK Izviđač" and "Hadžići") were stored garbled.

How it was noticed:
- the API returned "Izvi─æa─ì" instead of "Izviđač", and the weather lookup could not find the cities Zavidovići and Hadžići
- the bytes in the SQL file were correct UTF-8, but the bytes stored in the database were not

How it was fixed:
- `SET NAMES utf8mb4;` was added at the top of the SQL file, the README now shows the import command with `--default-character-set=utf8mb4`, and the local database was re-imported

#### Case 2: The DAO test script could be run from the browser
`backend/.htaccess` blocked only the `config` folder. Every other existing PHP file was served directly, so opening `backend/rest/dao/test.php` in a browser would run the DAO test against the database.

How it was noticed:
- while adding the Milestone 3 test script next to the services and checking which URLs the server answers

How it was fixed:
- `.htaccess` now answers 403 for `config`, `logs`, `vendor`, `utils` and `rest`, which was checked over HTTP

## Milestone 3

### Tools used

- Claude Code (Claude desktop app, model Claude Opus 5.5, agent mode)

### Main uses

- Planning Milestone 3 from Labs 04–06 and the project requirements; the plan was reviewed and approved before any code was written
- Service layer: `backend/utils/exceptions.php` (HTTP exceptions), `backend/utils/Validator.php`, `BaseService` and one service per entity with its business rules
- Small finder and count methods in the DAOs that the services need, so services never write SQL
- Presentation layer: FlightPHP installed with Composer, one routes file per entity and one global JSON error handler in `backend/index.php`
- OpenAPI documentation with swagger-php and Swagger UI in `backend/public/v1/docs/`
- External API: `WeatherService` and `GET /matches/{id}/weather` with Open-Meteo (geocoding and hourly forecast), timeouts, logging and a 503 fallback
- The service test script `backend/rest/services/test.php` and updates to `README.md` and `AGENTS.md`

### Important prompts

1. A project brief describing the stack, folder structure and rules, with the instruction to inspect the repository first and to ask instead of guessing; after a clarifying question the scope was set to "Milestone 3".
2. Approval of the plan with the choice of the external API: "Yes, Open-Meteo".
3. "Yes, re-import it" — permission to drop and re-import the local database as UTF-8.

### Cases where AI output was not correct

#### Case 1: Wrong HTTP method answered with plain text
A request with a method that a route does not support (for example `PUT /venues`) was answered by FlightPHP with a plain-text "Method Not Allowed" message instead of JSON.

How it was noticed:
- every route was tested with curl, and this answer did not have the `{"error": "..."}` shape of all other errors

How it was fixed:
- FlightPHP's `methodNotFound` was mapped in `backend/index.php` to a JSON answer with status 405 and an `Allow` header

#### Case 2: The weather service opened a database connection to read its settings
The first version of `WeatherService` called `BaseDao::getConnection()` only to load the `Config` class, so every weather request also connected to the database.

How it was noticed:
- while reviewing the service code before testing

How it was fixed:
- config loading was moved into its own method, `BaseDao::loadConfig()`, which both `getConnection()` and `WeatherService` use

#### Case 3: A shell command deleted a folder that was still needed
While removing the old router, the agent ran `git rm` on `backend/rest/middleware/response.php`. That left the folder empty, so Git removed it, the next command in the chain failed and the route files were not created.

How it was noticed:
- the command output showed the error and the route files were missing

How it was fixed:
- the `middleware` folder was recreated with a `.gitkeep` and the route files were generated again

#### Case 4: An unrequested change was offered
When asked only for an overview of the project, the agent offered to add content to this log.

How it was noticed:
- the offer was outside the question that was asked

How it was fixed:
- the agent was told not to add anything until explicitly asked, and it only changed files after that

### Review

- `php backend/rest/services/test.php` tries to break every rule (56 cases), and each broken rule ends with the right exception and status; everything the test adds is deleted again.
- The DAO smoke test still passes.
- All endpoints were tested over HTTP through XAMPP, and `GET /teams/{id}` was executed with "Try it out" in Swagger UI.
- The weather endpoint was tested with real data, with a timeout and with an error answer from the API; failures were written to `backend/logs/error.log`.
