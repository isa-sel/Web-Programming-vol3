<?php

use OpenApi\Attributes as OA;

/* Documents backend/rest/routes/MatchRoutes.php */
class MatchPaths
{
    #[OA\Get(
        path: '/matches',
        tags: ['matches'],
        summary: 'List all matches',
        responses: [
            new OA\Response(response: 200, description: 'All matches', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Match'))),
            new OA\Response(response: 500, ref: '#/components/responses/ServerError')
        ]
    )]
    public function getMatches()
    {
    }

    #[OA\Get(
        path: '/matches/{id}',
        tags: ['matches'],
        summary: 'Get one match',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Id of the match', example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'The match', content: new OA\JsonContent(ref: '#/components/schemas/Match')),
            new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
            new OA\Response(response: 500, ref: '#/components/responses/ServerError')
        ]
    )]
    public function getMatchesById()
    {
    }

    #[OA\Post(
        path: '/matches',
        tags: ['matches'],
        summary: 'Create a match',
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/MatchInput')),
        responses: [
            new OA\Response(response: 201, description: 'The created match', content: new OA\JsonContent(ref: '#/components/schemas/Match')),
            new OA\Response(response: 400, ref: '#/components/responses/BadRequest'),
            new OA\Response(response: 500, ref: '#/components/responses/ServerError')
        ]
    )]
    public function postMatches()
    {
    }

    #[OA\Put(
        path: '/matches/{id}',
        tags: ['matches'],
        summary: 'Update a match (all required fields must be sent)',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Id of the match', example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/MatchInput')),
        responses: [
            new OA\Response(response: 200, description: 'The updated match', content: new OA\JsonContent(ref: '#/components/schemas/Match')),
            new OA\Response(response: 400, ref: '#/components/responses/BadRequest'),
            new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
            new OA\Response(response: 500, ref: '#/components/responses/ServerError')
        ]
    )]
    public function putMatchesById()
    {
    }

    #[OA\Delete(
        path: '/matches/{id}',
        tags: ['matches'],
        summary: 'Delete a match',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Id of the match', example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'The match was deleted', content: new OA\JsonContent(ref: '#/components/schemas/Message')),
            new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
            new OA\Response(response: 500, ref: '#/components/responses/ServerError')
        ]
    )]
    public function deleteMatchesById()
    {
    }

    #[OA\Get(
        path: '/matches/{id}/weather',
        tags: ['matches', 'weather'],
        summary: 'Weather at the venue at the time of the match (external API: Open-Meteo)',
        description: 'Finds the venue city with the Open-Meteo Geocoding API and reads the hourly forecast for the match day. Available for matches from 92 days ago to 15 days ahead.',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Id of the match', example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'The weather for the match', content: new OA\JsonContent(ref: '#/components/schemas/MatchWeather')),
            new OA\Response(
                response: 400,
                description: 'The match date is outside the range Open-Meteo covers',
                content: new OA\JsonContent(ref: '#/components/schemas/Error', example: ['error' => 'Weather is only available for matches from 92 days ago to 15 days ahead.'])
            ),
            new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
            new OA\Response(
                response: 503,
                description: 'Open-Meteo is not reachable, timed out or answered with an error (written to the error log)',
                content: new OA\JsonContent(ref: '#/components/schemas/Error', example: ['error' => 'Weather information is not available at the moment. Please try again later.'])
            ),
            new OA\Response(response: 500, ref: '#/components/responses/ServerError')
        ]
    )]
    public function getMatchesByIdWeather()
    {
    }
}
