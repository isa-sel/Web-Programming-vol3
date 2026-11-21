<?php

declare(strict_types=1);

/**
 * Exceptions thrown by services when a rule is broken.
 * Each one carries the HTTP status code the API answers with;
 * the error handler in backend/index.php turns them into JSON responses.
 */
class HttpException extends Exception
{
    protected int $status = 500;

    public function get_status(): int
    {
        return $this->status;
    }
}

/** 400 - the request data is invalid or a business rule is broken. */
class ValidationException extends HttpException
{
    protected int $status = 400;
}

/** 401 - the user is not logged in (used from Milestone 4). */
class UnauthorizedException extends HttpException
{
    protected int $status = 401;
}

/** 403 - the user is logged in but not allowed to do this (used from Milestone 4). */
class ForbiddenException extends HttpException
{
    protected int $status = 403;
}

/** 404 - the requested entity does not exist. */
class NotFoundException extends HttpException
{
    protected int $status = 404;
}

/** 503 - an external service (third-party API) is not available. */
class ExternalServiceException extends HttpException
{
    protected int $status = 503;
}
