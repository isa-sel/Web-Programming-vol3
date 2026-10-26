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

