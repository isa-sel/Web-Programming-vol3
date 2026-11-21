<?php

use OpenApi\Attributes as OA;

/*
 * General information about the API, the server and the reusable schemas.
 * Every entity has an Input schema (what POST and PUT receive) and an output
 * schema (what the API returns, with the fields the server adds).
 */

#[OA\Info(
    title: 'Rukometni Centar API',
    description: 'REST API of Rukometni Centar, a handball league management application. IT 2001 Web Programming.',
    version: '1.0'
)]
#[OA\Server(url: API_BASE_URL, description: 'This server')]

/* ---------- Common ---------- */
#[OA\Schema(
    schema: 'Error',
    properties: [
        new OA\Property(property: 'error', type: 'string', example: "Field 'name' is required.")
    ]
)]
#[OA\Schema(
    schema: 'Message',
    properties: [
        new OA\Property(property: 'message', type: 'string', example: 'Team deleted.')
    ]
)]
#[OA\Response(
    response: 'BadRequest',
    description: 'Invalid input or a broken business rule',
    content: new OA\JsonContent(ref: '#/components/schemas/Error')
)]
#[OA\Response(
    response: 'NotFound',
    description: 'The entity does not exist',
    content: new OA\JsonContent(ref: '#/components/schemas/Error')
)]
#[OA\Response(
    response: 'ServerError',
    description: 'Unexpected server or database error (details only in the error log)',
    content: new OA\JsonContent(
        ref: '#/components/schemas/Error',
        example: ['error' => 'Something went wrong on the server. Please try again later.']
    )
)]

/* ---------- Roles ---------- */
#[OA\Schema(
    schema: 'RoleInput',
    required: ['name'],
    properties: [
        new OA\Property(property: 'name', type: 'string', description: 'Lowercase letters and underscores, unique', example: 'editor'),
        new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Can edit match results')
    ]
)]
#[OA\Schema(
    schema: 'Role',
    allOf: [new OA\Schema(ref: '#/components/schemas/RoleInput')],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'created_at', type: 'string', example: '2026-11-02 06:44:57')
    ]
)]

/* ---------- Users ---------- */
#[OA\Schema(
    schema: 'UserInput',
    required: ['first_name', 'last_name', 'email', 'password'],
    properties: [
        new OA\Property(property: 'first_name', type: 'string', example: 'Ena'),
        new OA\Property(property: 'last_name', type: 'string', example: 'Begić'),
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'ena.begic@example.com'),
        new OA\Property(
            property: 'password',
            type: 'string',
            writeOnly: true,
            description: '8 to 72 characters with a letter and a number. Required for POST; on PUT it is changed only when sent. Stored only as a bcrypt hash.',
            example: 'Rukomet2026'
        ),
        new OA\Property(property: 'role_id', type: 'integer', description: 'Defaults to the "user" role on POST', example: 3),
        new OA\Property(property: 'is_active', type: 'integer', enum: [0, 1], example: 1)
    ]
)]
#[OA\Schema(
    schema: 'User',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 3),
        new OA\Property(property: 'first_name', type: 'string', example: 'Amir'),
        new OA\Property(property: 'last_name', type: 'string', example: 'Kovač'),
        new OA\Property(property: 'email', type: 'string', example: 'user@rukometnicentar.com'),
        new OA\Property(property: 'role_id', type: 'integer', example: 3),
        new OA\Property(property: 'is_active', type: 'integer', example: 1),
        new OA\Property(property: 'created_at', type: 'string', example: '2026-11-02 06:44:57')
    ]
)]

/* ---------- Teams ---------- */
#[OA\Schema(
    schema: 'TeamInput',
    required: ['name', 'city', 'category'],
    properties: [
        new OA\Property(property: 'name', type: 'string', description: 'Unique within the city', example: 'RK Sloboda'),
        new OA\Property(property: 'city', type: 'string', example: 'Tuzla'),
        new OA\Property(property: 'founded_year', type: 'integer', nullable: true, description: 'From 1800 to the current year', example: 1955),
        new OA\Property(property: 'category', type: 'string', enum: ['Senior Men', 'Senior Women', 'Junior Men', 'Junior Women'], example: 'Senior Men')
    ]
)]
#[OA\Schema(
    schema: 'Team',
    allOf: [new OA\Schema(ref: '#/components/schemas/TeamInput')],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'created_at', type: 'string', example: '2026-11-02 06:44:57')
    ]
)]

/* ---------- Players ---------- */
#[OA\Schema(
    schema: 'PlayerInput',
    required: ['first_name', 'last_name', 'position', 'jersey_number', 'team_id'],
    properties: [
        new OA\Property(property: 'first_name', type: 'string', example: 'Kenan'),
        new OA\Property(property: 'last_name', type: 'string', example: 'Hodžić'),
        new OA\Property(property: 'birth_date', type: 'string', format: 'date', nullable: true, description: 'The player must be at least 14 years old', example: '2003-05-14'),
        new OA\Property(
            property: 'position',
            type: 'string',
            enum: ['Goalkeeper', 'Left wing', 'Right wing', 'Left back', 'Right back', 'Centre back', 'Pivot'],
            example: 'Pivot'
        ),
        new OA\Property(property: 'jersey_number', type: 'integer', minimum: 1, maximum: 99, description: 'Unique within the team', example: 22),
        new OA\Property(property: 'team_id', type: 'integer', example: 1)
    ]
)]
#[OA\Schema(
    schema: 'Player',
    allOf: [new OA\Schema(ref: '#/components/schemas/PlayerInput')],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'created_at', type: 'string', example: '2026-11-02 06:44:57')
    ]
)]
#[OA\Schema(
    schema: 'PlayerWithTeam',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'first_name', type: 'string', example: 'Admir'),
        new OA\Property(property: 'last_name', type: 'string', example: 'Mehmedović'),
        new OA\Property(property: 'position', type: 'string', example: 'Centre back'),
        new OA\Property(property: 'jersey_number', type: 'integer', example: 10),
        new OA\Property(property: 'team_id', type: 'integer', example: 1),
        new OA\Property(property: 'team_name', type: 'string', example: 'RK Bosna'),
        new OA\Property(property: 'team_city', type: 'string', example: 'Sarajevo')
    ]
)]

/* ---------- Venues ---------- */
#[OA\Schema(
    schema: 'VenueInput',
    required: ['name', 'city', 'address', 'capacity'],
    properties: [
        new OA\Property(property: 'name', type: 'string', description: 'Unique within the city', example: 'Dvorana Mejdan'),
        new OA\Property(property: 'city', type: 'string', example: 'Tuzla'),
        new OA\Property(property: 'address', type: 'string', example: 'Rudarska 1, Tuzla'),
        new OA\Property(property: 'capacity', type: 'integer', minimum: 1, maximum: 100000, example: 4500)
    ]
)]
#[OA\Schema(
    schema: 'Venue',
    allOf: [new OA\Schema(ref: '#/components/schemas/VenueInput')],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'created_at', type: 'string', example: '2026-11-02 06:44:57')
    ]
)]

/* ---------- Matches ---------- */
#[OA\Schema(
    schema: 'MatchInput',
    required: ['home_team_id', 'away_team_id', 'venue_id', 'match_date'],
    properties: [
        new OA\Property(property: 'home_team_id', type: 'integer', example: 1),
        new OA\Property(property: 'away_team_id', type: 'integer', description: 'Must differ from home_team_id', example: 2),
        new OA\Property(property: 'venue_id', type: 'integer', example: 1),
        new OA\Property(
            property: 'match_date',
            type: 'string',
            description: 'YYYY-MM-DD HH:MM:SS (YYYY-MM-DDTHH:MM is also accepted). A new scheduled match must be in the future.',
            example: '2026-12-05 18:00:00'
        ),
        new OA\Property(property: 'home_score', type: 'integer', nullable: true, description: 'Only for completed matches (then required)', example: null),
        new OA\Property(property: 'away_score', type: 'integer', nullable: true, description: 'Only for completed matches (then required)', example: null),
        new OA\Property(property: 'status', type: 'string', enum: ['scheduled', 'completed', 'postponed', 'cancelled'], description: 'Defaults to scheduled', example: 'scheduled')
    ]
)]
#[OA\Schema(
    schema: 'Match',
    allOf: [new OA\Schema(ref: '#/components/schemas/MatchInput')],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'created_at', type: 'string', example: '2026-11-02 06:44:57')
    ]
)]

/* ---------- Standings ---------- */
#[OA\Schema(
    schema: 'StandingInput',
    required: ['team_id', 'season', 'wins', 'draws', 'losses', 'goals_for', 'goals_against', 'position'],
    properties: [
        new OA\Property(property: 'team_id', type: 'integer', description: 'One row per team and season', example: 1),
        new OA\Property(property: 'season', type: 'string', description: 'Two following years', example: '2027-2028'),
        new OA\Property(property: 'wins', type: 'integer', minimum: 0, example: 5),
        new OA\Property(property: 'draws', type: 'integer', minimum: 0, example: 1),
        new OA\Property(property: 'losses', type: 'integer', minimum: 0, example: 0),
        new OA\Property(property: 'goals_for', type: 'integer', minimum: 0, example: 172),
        new OA\Property(property: 'goals_against', type: 'integer', minimum: 0, example: 145),
        new OA\Property(property: 'position', type: 'integer', minimum: 1, description: 'Unique within the season', example: 1)
    ]
)]
#[OA\Schema(
    schema: 'Standing',
    allOf: [new OA\Schema(ref: '#/components/schemas/StandingInput')],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'played', type: 'integer', description: 'Calculated: wins + draws + losses', example: 6),
        new OA\Property(property: 'points', type: 'integer', description: 'Calculated: 2 per win, 1 per draw', example: 11),
        new OA\Property(property: 'created_at', type: 'string', example: '2026-11-02 06:44:57')
    ]
)]

/* ---------- External API: weather ---------- */
#[OA\Schema(
    schema: 'MatchWeather',
    properties: [
        new OA\Property(property: 'match_id', type: 'integer', example: 1),
        new OA\Property(property: 'match_date', type: 'string', example: '2026-10-24 18:00:00'),
        new OA\Property(
            property: 'venue',
            type: 'object',
            properties: [
                new OA\Property(property: 'id', type: 'integer', example: 1),
                new OA\Property(property: 'name', type: 'string', example: 'Dvorana Skenderija'),
                new OA\Property(property: 'city', type: 'string', example: 'Sarajevo')
            ]
        ),
        new OA\Property(
            property: 'location',
            type: 'object',
            properties: [
                new OA\Property(property: 'name', type: 'string', example: 'Sarajevo'),
                new OA\Property(property: 'latitude', type: 'number', example: 43.84864),
                new OA\Property(property: 'longitude', type: 'number', example: 18.35644)
            ]
        ),
        new OA\Property(
            property: 'weather',
            type: 'object',
            properties: [
                new OA\Property(property: 'time', type: 'string', example: '2026-10-24 18:00'),
                new OA\Property(property: 'temperature_c', type: 'number', nullable: true, example: 12.1),
                new OA\Property(property: 'precipitation_mm', type: 'number', nullable: true, example: 0),
                new OA\Property(property: 'precipitation_probability', type: 'integer', nullable: true, example: 88),
                new OA\Property(property: 'wind_speed_kmh', type: 'number', nullable: true, example: 5.6),
                new OA\Property(property: 'weather_code', type: 'integer', nullable: true, example: 3),
                new OA\Property(property: 'description', type: 'string', example: 'Overcast')
            ]
        ),
        new OA\Property(property: 'source', type: 'string', example: 'Open-Meteo')
    ]
)]
class OpenApiSpec
{
}
