<?php

use OpenApi\Attributes as OA;

/* Documents backend/rest/routes/RoleRoutes.php */
class RolePaths
{
    #[OA\Get(
        path: '/roles',
        tags: ['roles'],
        summary: 'List all roles',
        responses: [
            new OA\Response(response: 200, description: 'All roles', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Role'))),
            new OA\Response(response: 500, ref: '#/components/responses/ServerError')
        ]
    )]
    public function getRoles()
    {
    }

    #[OA\Get(
        path: '/roles/{id}',
        tags: ['roles'],
        summary: 'Get one role',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Id of the role', example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'The role', content: new OA\JsonContent(ref: '#/components/schemas/Role')),
            new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
            new OA\Response(response: 500, ref: '#/components/responses/ServerError')
        ]
    )]
    public function getRolesById()
    {
    }

    #[OA\Post(
        path: '/roles',
        tags: ['roles'],
        summary: 'Create a role',
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/RoleInput')),
        responses: [
            new OA\Response(response: 201, description: 'The created role', content: new OA\JsonContent(ref: '#/components/schemas/Role')),
            new OA\Response(response: 400, ref: '#/components/responses/BadRequest'),
            new OA\Response(response: 500, ref: '#/components/responses/ServerError')
        ]
    )]
    public function postRoles()
    {
    }

    #[OA\Put(
        path: '/roles/{id}',
        tags: ['roles'],
        summary: 'Update a role (all required fields must be sent)',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Id of the role', example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/RoleInput')),
        responses: [
            new OA\Response(response: 200, description: 'The updated role', content: new OA\JsonContent(ref: '#/components/schemas/Role')),
            new OA\Response(response: 400, ref: '#/components/responses/BadRequest'),
            new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
            new OA\Response(response: 500, ref: '#/components/responses/ServerError')
        ]
    )]
    public function putRolesById()
    {
    }

    #[OA\Delete(
        path: '/roles/{id}',
        tags: ['roles'],
        summary: 'Delete a role',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Id of the role', example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'The role was deleted', content: new OA\JsonContent(ref: '#/components/schemas/Message')),
            new OA\Response(response: 400, description: 'Cannot be deleted: the role is still assigned to users', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
            new OA\Response(response: 500, ref: '#/components/responses/ServerError')
        ]
    )]
    public function deleteRolesById()
    {
    }
}
