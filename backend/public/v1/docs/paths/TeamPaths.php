<?php

use OpenApi\Attributes as OA;

/* Documents backend/rest/routes/TeamRoutes.php */
class TeamPaths
{
    #[OA\Get(
        path: '/teams',
        tags: ['teams'],
        summary: 'List all teams',
        responses: [
            new OA\Response(response: 200, description: 'All teams', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Team'))),
            new OA\Response(response: 500, ref: '#/components/responses/ServerError')
        ]
    )]
    public function getTeams()
    {
    }

    #[OA\Get(
        path: '/teams/{id}',
        tags: ['teams'],
        summary: 'Get one team',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Id of the team', example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'The team', content: new OA\JsonContent(ref: '#/components/schemas/Team')),
            new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
            new OA\Response(response: 500, ref: '#/components/responses/ServerError')
        ]
    )]
    public function getTeamsById()
    {
    }

    #[OA\Post(
        path: '/teams',
        tags: ['teams'],
        summary: 'Create a team',
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/TeamInput')),
        responses: [
            new OA\Response(response: 201, description: 'The created team', content: new OA\JsonContent(ref: '#/components/schemas/Team')),
            new OA\Response(response: 400, ref: '#/components/responses/BadRequest'),
            new OA\Response(response: 500, ref: '#/components/responses/ServerError')
        ]
    )]
    public function postTeams()
    {
    }

    #[OA\Put(
        path: '/teams/{id}',
        tags: ['teams'],
        summary: 'Update a team (all required fields must be sent)',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Id of the team', example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/TeamInput')),
        responses: [
            new OA\Response(response: 200, description: 'The updated team', content: new OA\JsonContent(ref: '#/components/schemas/Team')),
            new OA\Response(response: 400, ref: '#/components/responses/BadRequest'),
            new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
            new OA\Response(response: 500, ref: '#/components/responses/ServerError')
        ]
    )]
    public function putTeamsById()
    {
    }

    #[OA\Delete(
        path: '/teams/{id}',
        tags: ['teams'],
        summary: 'Delete a team',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Id of the team', example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'The team was deleted', content: new OA\JsonContent(ref: '#/components/schemas/Message')),
            new OA\Response(response: 400, description: 'Cannot be deleted: the team still has players, matches or standings', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
            new OA\Response(response: 500, ref: '#/components/responses/ServerError')
        ]
    )]
    public function deleteTeamsById()
    {
    }

    #[OA\Get(
        path: '/teams/{id}/players',
        tags: ['teams', 'players'],
        summary: 'List the players of one team, ordered by jersey number',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Id of the team', example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'The players of the team (an empty list when it has none)', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Player'))),
            new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
            new OA\Response(response: 500, ref: '#/components/responses/ServerError')
        ]
    )]
    public function getTeamsByIdPlayers()
    {
    }

    #[OA\Get(
        path: '/teams/{id}/matches',
        tags: ['teams', 'matches'],
        summary: 'List the home and away matches of one team',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Id of the team', example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'The matches of the team, oldest first', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Match'))),
            new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
            new OA\Response(response: 500, ref: '#/components/responses/ServerError')
        ]
    )]
    public function getTeamsByIdMatches()
    {
    }
}
