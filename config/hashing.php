<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Hash Driver
    |--------------------------------------------------------------------------
    |
    | This option controls the default hash driver that will be used to hash
    | passwords for your application. By default, the bcrypt algorithm is
    | used; however, you remain free to modify this option if you wish.
    |
    | Supported: "bcrypt", "argon", "argon2id"
    |
    */

    'driver' => 'argon2id',

    /*
    |--------------------------------------------------------------------------
    | Bcrypt Options
    |--------------------------------------------------------------------------
    |
    | Here you may specify the options that should be passed to the Laravel
    | hashing driver. The "rounds" option determines the work factor that
    | will be applied when hashing passwords. Higher rounds means longer
    | computation time and therefore more secure.
    |
    */

    'bcrypt' => [
        'rounds' => env('BCRYPT_ROUNDS', 12),
        'verify' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Argon Options
    |--------------------------------------------------------------------------
    |
    | Here you may specify the options that should be passed to the Argon
    | hashing driver. The "memory" option specifies the amount of memory
    | in kilobytes that the hashing operation may use. The "time" option
    | specifies the amount of time in milliseconds the hashing will run.
    | The "threads" option specifies the number of parallel threads used.
    |
    | Argon2id is the winner of the 2015 Password Hashing Competition and
    | provides better security against GPU cracking attacks than bcrypt.
    |
    */

    'argon' => [
        'memory' => 65536,
        'threads' => 3,
        'time' => 4,
        'verify' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Argon2id Options (Specific)
    |--------------------------------------------------------------------------
    |
    | These options are specifically for the Argon2id algorithm, which is
    | recommended for password hashing in modern PHP applications. It
    | combines the benefits of Argon2i and Argon2d for maximum security.
    |
    */

    'argon2id' => [
        'memory' => 65536,
        'threads' => 3,
        'time' => 4,
        'verify' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Rehash On Login
    |--------------------------------------------------------------------------
    |
    | Setting this option to true will tell Laravel to automatically rehash
    | the user's password during login if the configured work factor for
    | the algorithm has changed, or if the driver has been changed since
    | the last time the password was hashed.
    |
    */

    'rehash_on_login' => true,
];
