<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: "API – Aplikacija za izložbe fotografija",
    version: "1.0.0",
    description: "Swagger dokumentacija Laravel API-ja"
)]
#[OA\Server(
    url: "http://localhost:8000",
    description: "Lokalno (Docker)"
)]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    scheme: "bearer",
    bearerFormat: "Token",
    description: "Unesi token u formatu: Bearer {token}"
)]
class SwaggerBootstrap {}