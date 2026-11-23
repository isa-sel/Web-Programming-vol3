<?php

use OpenApi\Attributes as OA;

/* Documents backend/rest/routes/VenueRoutes.php */
class VenuePaths
{
    #[OA\Get(
        path: '/venues',
        tags: ['venues'],
        summary: 'List all venues',
        responses: [
            new OA\Response(response: 200, description: 'All venues', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Venue'))),
            new OA\Response(response: 500, ref: '#/components/responses/ServerError')
        ]
    )]
    public function getVenues()
    {
    }

    #[OA\Get(
        path: '/venues/{id}',
        tags: ['venues'],
        summary: 'Get one venue',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Id of the venue', example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'The venue', content: new OA\JsonContent(ref: '#/components/schemas/Venue')),
            new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
            new OA\Response(response: 500, ref: '#/components/responses/ServerError')
        ]
    )]
    public function getVenuesById()
    {
    }

    #[OA\Post(
        path: '/venues',
        tags: ['venues'],
        summary: 'Create a venue',
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/VenueInput')),
        responses: [
            new OA\Response(response: 201, description: 'The created venue', content: new OA\JsonContent(ref: '#/components/schemas/Venue')),
            new OA\Response(response: 400, ref: '#/components/responses/BadRequest'),
            new OA\Response(response: 500, ref: '#/components/responses/ServerError')
        ]
    )]
    public function postVenues()
    {
    }

    #[OA\Put(
        path: '/venues/{id}',
        tags: ['venues'],
        summary: 'Update a venue (all required fields must be sent)',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Id of the venue', example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/VenueInput')),
        responses: [
            new OA\Response(response: 200, description: 'The updated venue', content: new OA\JsonContent(ref: '#/components/schemas/Venue')),
            new OA\Response(response: 400, ref: '#/components/responses/BadRequest'),
            new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
            new OA\Response(response: 500, ref: '#/components/responses/ServerError')
        ]
    )]
    public function putVenuesById()
    {
    }

    #[OA\Delete(
        path: '/venues/{id}',
        tags: ['venues'],
        summary: 'Delete a venue',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Id of the venue', example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'The venue was deleted', content: new OA\JsonContent(ref: '#/components/schemas/Message')),
            new OA\Response(response: 400, description: 'Cannot be deleted: the venue hosts matches', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
            new OA\Response(response: 500, ref: '#/components/responses/ServerError')
        ]
    )]
    public function deleteVenuesById()
    {
    }
}
