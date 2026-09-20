<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Processing driver
    |--------------------------------------------------------------------------
    |
    | The job processor used by the queue worker. "sim" is the deterministic
    | simulator used for acceptance testing; a real driver (Whisper/LLM via
    | service_registry) plugs in here in a follow-up phase.
    |
    */

    'processor' => env('AI_FACTORY_PROCESSOR', 'sim'),

    /*
    |--------------------------------------------------------------------------
    | Idempotency
    |--------------------------------------------------------------------------
    |
    | Lifetime of an Idempotency-Key in hours. Replayed requests within this
    | window return the originally created resource.
    |
    */

    'idempotency_ttl_hours' => (int) env('AI_FACTORY_IDEMPOTENCY_TTL_HOURS', 24),

    /*
    |--------------------------------------------------------------------------
    | File uploads
    |--------------------------------------------------------------------------
    |
    | Storage disk, directory and per-extension mime whitelist for
    | POST /api/v1/files/upload.
    |
    */

    'upload' => [
        'disk' => env('AI_FACTORY_UPLOAD_DISK', 'local'),
        'directory' => 'uploads',
        'max_bytes' => (int) env('AI_FACTORY_UPLOAD_MAX_BYTES', 512 * 1024 * 1024),
        'allowed' => [
            'doc' => ['application/msword'],
            'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
            'pdf' => ['application/pdf'],
            'txt' => ['text/plain'],
            'mp3' => ['audio/mpeg', 'audio/mpeg3'],
            'wav' => ['audio/wav', 'audio/x-wav', 'audio/wave'],
            'mp4' => ['video/mp4'],
        ],
    ],

];
