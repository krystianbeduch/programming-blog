<?php
class HttpStatus {
    // Success codes
    public const OK = 200;

    // Client error codes
    public const BAD_REQUEST = 400;
    public const UNAUTHORIZED = 401;
    public const FORBIDDEN = 403;
    public const NOT_FOUND = 404;
    public const METHOD_NOT_ALLOWED = 405;

    // Server error codes
    public const INTERNAL_SERVER_ERROR = 500;
}
