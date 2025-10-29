protected $middlewareGroups = [
    'web' => [
        // existing middlewares
        \App\Http\Middleware\CheckTokenExpiry::class,
    ],
];
