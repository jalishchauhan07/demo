protected $middlewareGroups = [
    'web' => [
        // existing middlewares
        \App\Http\Middleware\CheckTokenExpiry::class,
        'log.send' => \App\Http\Middleware\LogSendSlotRequest::class,
    ],
];
