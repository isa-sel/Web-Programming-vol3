<?php

use OpenApi\Attributes as OA;

/* Documents backend/rest/routes/PlayerRoutes.php */
class PlayerPaths
{
    #[OA\Get(
        path: '/players',
        tags: ['players'],
        summary: 'List all players',
        responses: [
            new OA\Response(response: 200, description: 'All players', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Player'))),
            new OA\Response(response: 500, ref: '#/components/responses/ServerError')
        ]
    )]
    public function getPlayers()
    {
    }

    #[OA\Get(
        path: '/players/{id}',
        tags: ['players'],
        summary: 'Get one player',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Id of the player', example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'The player', content: new OA\JsonContent(ref: '#/components/schemas/Player')),
            new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
            new OA\Response(response: 500, ref: '#/components/responses/ServerError')
        ]
    )]
    public function getPlayersById()
    {
    }

    #[OA\Post(
        path: '/players',
        tags: ['players'],
        summary: 'Create a player',
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/PlayerInput')),
        responses: [
            new OA\Response(response: 201, description: 'The created player', content: new OA\JsonContent(ref: '#/components/schemas/Player')),
            new OA\Response(response: 400, ref: '#/components/responses/BadRequest'),
            new OA\Response(response: 500, ref: '#/components/responses/ServerError')
        ]
    )]
    public function postPlayers()
    {
    }

    #[OA\Put(
        path: '/players/{id}',
        tags: ['players'],
        summary: 'Update a player (all required fields must be sent)',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Id of the player', example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/PlayerInput')),
        responses: [
            new OA\Response(response: 200, description: 'The updated player', content: new OA\JsonContent(ref: '#/components/schemas/Player')),
            new OA\Response(response: 400, ref: '#/components/responses/BadRequest'),
            new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
            new OA\Response(response: 500, ref: '#/components/responses/ServerError')
        ]
    )]
    public function putPlayersById()
    {
    }

    #[OA\Delete(
        path: '/players/{id}',
        tags: ['players'],
        summary: 'Delete a player',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Id of the player', example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'The player was deleted', content: new OA\JsonContent(ref: '#/components/schemas/Message')),
            new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
            new OA\Response(response: 500, ref: '#/components/responses/ServerError')
        ]
    )]
    public function deletePlayersById()
    {
    }

    #[OA\Get(
        path: '/players/with-team',
        tags: ['players'],
        summary: 'List all players with the name and city of their team',
        responses: [
            new OA\Response(response: 200, description: 'All players, ordered by last name', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/PlayerWithTeam'))),
            new OA\Response(response: 500, ref: '#/components/responses/ServerError')
        ]
    )]
    public function getPlayersWithTeam()
    {
    }
}
