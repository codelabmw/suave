<?php

use Codelabmw\Testament\Enums\CodeType;

return [
    /*
    |--------------------------------------------------------------------------
    | Verification Code Length
    |--------------------------------------------------------------------------
    |
    | This is the length of the verification code that will be sent to
    | the user.
    |
    */

    'verification_code_length' => env('SUAVE_VERIFICATION_CODE_LENGTH', 6),

    /*
    |--------------------------------------------------------------------------
    | Verification Code Type
    |--------------------------------------------------------------------------
    |
    | This is the type of the verification code that will be sent to
    | the user.
    |
    | Possible values: CodeType::NUMERIC, CodeType::ALPHANUMERIC,
    | CodeType::ALPHA
    |
    */

    'verification_code_type' => CodeType::NUMERIC,

    /*
    |--------------------------------------------------------------------------
    | Verification Code Expires In
    |--------------------------------------------------------------------------
    |
    | This is the number of minutes that the verification code will be
    | valid for.
    |
    */

    'verification_code_expires_in' => env('SUAVE_VERIFICATION_CODE_EXPIRES_IN', 15),

    /*
    |--------------------------------------------------------------------------
    | Verification Code Resend After
    |--------------------------------------------------------------------------
    |
    | This is the number of minutes that the user has to wait before they 
    | can request a new verification code.
    |
    */

    'verification_code_resend_after' => env('SUAVE_VERIFICATION_CODE_RESEND_AFTER', 1),

    /*
    |--------------------------------------------------------------------------
    | Temporary Password Length
    |--------------------------------------------------------------------------
    |
    | This is the length of the temporary password that will be sent to
    | the user.
    |
    */

    'temporary_password_length' => env('SUAVE_TEMPORARY_PASSWORD_LENGTH', 8),

    /*
    |--------------------------------------------------------------------------
    | Must Reset Temporary Password After
    |--------------------------------------------------------------------------
    |
    | This is the number of days until the user must change their
    | password from the temporary password that was set.
    |
    */

    'must_reset_temporary_password_after' => env('SUAVE_MUST_RESET_TEMPORARY_PASSWORD_AFTER', 15),
];