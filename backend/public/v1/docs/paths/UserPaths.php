<?php

use OpenApi\Attributes as OA;

/* Documents backend/rest/routes/UserRoutes.php */
class UserPaths
{
    #[OA\Get(
        path: '/users',
        tags: ['users'],
        summary: 'List all users',
        responses: [
            new OA\Response(response: 200, description: 'All users', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/User'))),
            new OA\Response(response: 500, ref: '#/components/responses/ServerError')
        ]
    )]
    public function getUsers()
    {
    }

    #[OA\Get(
        path: '/users/{id}',
        tags: ['users'],
        summary: 'Get one user',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Id of the user', example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'The user', content: new OA\JsonContent(ref: '#/components/schemas/User')),
            new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
            new OA\Response(response: 500, ref: '#/components/responses/ServerError')
        ]
    )]
    public function getUsersById()
    {
    }

    #[OA\Post(
        path: '/users',
        tags: ['users'],
        summary: 'Create a user',
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/UserInput')),
        responses: [
            new OA\Response(response: 201, description: 'The created user', content: new OA\JsonContent(ref: '#/components/schemas/User')),
            new OA\Response(response: 400, ref: '#/components/responses/BadRequest'),
            new OA\Response(response: 500, ref: '#/components/responses/ServerError')
        ]
    )]
    public function postUsers()
    {
    }

    #[OA\Put(
        path: '/users/{id}',
        tags: ['users'],
        summary: 'Update a user (all required fields must be sent)',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Id of the user', example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/UserInput')),
        responses: [
            new OA\Response(response: 200, description: 'The updated user', content: new OA\JsonContent(ref: '#/components/schemas/User')),
            new OA\Response(response: 400, ref: '#/components/responses/BadRequest'),
            new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
            new OA\Response(response: 500, ref: '#/components/responses/ServerError')
        ]
    )]
    public function putUsersById()
    {
    }

    #[OA\Delete(
        path: '/users/{id}',
        tags: ['users'],
        summary: 'Delete a user',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Id of the user', example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'The user was deleted', content: new OA\JsonContent(ref: '#/components/schemas/Message')),
            new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
            new OA\Response(response: 500, ref: '#/components/responses/ServerError')
        ]
    )]
    public function deleteUsersById()
    {
    }
}
