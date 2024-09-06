<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

/**
 * @OA\Info(version="1.0.0", description="Incident Management", title="Incident Management"),
 * @OA\Server(url="http://127.0.0.1/api", description="local server"),
 * @OA\Server(url="#", description="staging server"),
 * @OA\Server(url="#", description="production server"),
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */

abstract class Controller extends \Illuminate\Routing\Controller
{
    //
}
