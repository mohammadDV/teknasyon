<?php
return [
    "APP_KEY"       => "0ec19e8351b1dea39538f4e1ace5e73e",
    "MODE"          => "1",
    "VERSION"       => "0.1.0",
    "APP_TITLE"     => "Teknasyon",
    "BASE_URL"      => "http://localhost:8000",
    "BASE_DIR"      => realpath(__DIR__ . "/../"),
    "providers"     => [
        \App\Providers\SessionProvider::class,
        \App\Providers\AppServiceProvider::class
    ]
];


