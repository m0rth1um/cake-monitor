<?php
declare(strict_types = 1);

return [
    'CakeMonitor' => [
        'accessToken' => null,
        'projectName' => null,
        'serverDescription' => 'Server: ' . env('SERVERDESCRIPTION'),
        'onSuccess' => function (): void {
            echo 'CHECK-OK';

            return;
        },
        'checks' => [],
        'Sentry' => [
            'enabled' => false,
            'dsn' => null,
            'sanitizeFields' => [],
            'sanitizeExtraCallback' => null
        ]
    ]
];
