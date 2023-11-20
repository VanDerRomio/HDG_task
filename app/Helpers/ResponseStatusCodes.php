<?php
namespace App\Helpers;

class ResponseStatusCodes
{
    /**
     * default errors
     */
    const RESPONSE_STATUS_CODE_1000 = [
        'code'      => 1000,
        'message'   => 'Error',
    ];
    const RESPONSE_STATUS_CODE_1001 = [
        'code'      => 1001,
        'message'   => 'Unauthenticated',
        'httpCode'  => 401,
    ];
    const RESPONSE_STATUS_CODE_1002 = [
        'code'      => 1002,
        'message'   => 'Bad request',
    ];
    const RESPONSE_STATUS_CODE_1003 = [
        'code'      => 1003,
        'message'   => 'Unauthorized',
        'httpCode'  => 403,
    ];
    const RESPONSE_STATUS_CODE_1004 = [
        'code'      => 1004,
        'message'   => 'HttpResponseException',
    ];
    const RESPONSE_STATUS_CODE_1005 = [
        'code'      => 1005,
        'message'   => 'Bad request',
    ];
    const RESPONSE_STATUS_CODE_1006 = [
        'code'      => 1006,
        'message'   => 'SuspiciousOperationException',
    ];
    const RESPONSE_STATUS_CODE_1007 = [
        'code'      => 1007,
        'message'   => 'TokenMismatchException',
    ];
    const RESPONSE_STATUS_CODE_1008 = [
        'code'      => 1008,
        'message'   => 'Error validation',
    ];
    const RESPONSE_STATUS_CODE_1009 = [
        'code'      => 1009,
        'message'   => 'Cannot remove this resource permanently. It is related with any other resource',
    ];
    const RESPONSE_STATUS_CODE_1010 = [
        'code'      => 1010,
        'message'   => 'Unexpected Exception. Try again later',
    ];

    /**
     * other errors
     */
    const RESPONSE_STATUS_CODE_1011 = [
        'code'      => 1011,
        'message'   => 'No user was found with given id',
    ];

    const RESPONSE_STATUS_CODE_1012 = [
        'code'      => 1012,
        'message'   => 'No task was found with given id',
    ];

    const RESPONSE_STATUS_CODE_1013 = [
        'code'      => 1013,
        'message'   => 'Failed to create user',
    ];

    const RESPONSE_STATUS_CODE_1014 = [
        'code'      => 1014,
        'message'   => 'Failed to update user',
    ];

    const RESPONSE_STATUS_CODE_1015 = [
        'code'      => 1015,
        'message'   => 'Failed to delete user',
    ];

    const RESPONSE_STATUS_CODE_1016 = [
        'code'      => 1016,
        'message'   => 'Failed to create task',
    ];

    const RESPONSE_STATUS_CODE_1017 = [
        'code'      => 1017,
        'message'   => 'Failed to update task',
    ];

    const RESPONSE_STATUS_CODE_1018 = [
        'code'      => 1018,
        'message'   => 'Failed to delete task',
    ];
}
