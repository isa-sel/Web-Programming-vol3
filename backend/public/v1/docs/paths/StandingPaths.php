<?php

use OpenApi\Attributes as OA;

/* Documents backend/rest/routes/StandingRoutes.php */
class StandingPaths
{
    #[OA\Get(
        path: '/standings',
        tags: ['standings'],
        summary: 'List all standings rows, or the table of one season',
        parameters: [
            new OA\Parameter(
                name: 'season',
                in: 'query',
                required: false,
                description: 'Only this season, ordered by position',
                example: '2026-2027',
                schema: new OA\Schema(type: 'string')
            )
        ],
        responses: [
            new OA\Response(response: 200, description: 'The standings rows', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Standing'))),
            new OA\Response(response: 400, ref: '#/components/responses/BadRequest'),
            new OA\Response(response: 500, ref: '#/components/responses/ServerError')
        ]
    )]
    public function getStandings()
    {
    }

    #[OA\Get(
        path: '/standings/{id}',
        tags: ['standings'],
        summary: 'Get one standings row',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Id of the standings row', example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'The standings row', content: new OA\JsonContent(ref: '#/components/schemas/Standing')),
            new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
            new OA\Response(response: 500, ref: '#/components/responses/ServerError')
        ]
    )]
    public function getStandingsById()
    {
    }

    #[OA\Post(
        path: '/standings',
        tags: ['standings'],
        summary: 'Create a standings row',
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/StandingInput')),
        responses: [
            new OA\Response(response: 201, description: 'The created standings row', content: new OA\JsonContent(ref: '#/components/schemas/Standing')),
            new OA\Response(response: 400, ref: '#/components/responses/BadRequest'),
            new OA\Response(response: 500, ref: '#/components/responses/ServerError')
        ]
    )]
    public function postStandings()
    {
    }

    #[OA\Put(
        path: '/standings/{id}',
        tags: ['standings'],
        summary: 'Update a standings row (all required fields must be sent)',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Id of the standings row', example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/StandingInput')),
        responses: [
            new OA\Response(response: 200, description: 'The updated standings row', content: new OA\JsonContent(ref: '#/components/schemas/Standing')),
            new OA\Response(response: 400, ref: '#/components/responses/BadRequest'),
            new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
            new OA\Response(response: 500, ref: '#/components/responses/ServerError')
        ]
    )]
    public function putStandingsById()
    {
    }

    #[OA\Delete(
        path: '/standings/{id}',
        tags: ['standings'],
        summary: 'Delete a standings row',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Id of the standings row', example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'The standings row was deleted', content: new OA\JsonContent(ref: '#/components/schemas/Message')),
            new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
            new OA\Response(response: 500, ref: '#/components/responses/ServerError')
        ]
    )]
    public function deleteStandingsById()
    {
    }
}
