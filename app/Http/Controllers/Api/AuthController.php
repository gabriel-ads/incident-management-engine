<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    /**
     * @OA\Post(
     *     path="/login",
     *     tags={"Auth"},
     *     summary="Login",
     *     description="Authenticate a user and return an access token.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="email", type="string", example="kelly@celke.com.br"),
     *                 @OA\Property(property="password", type="string", example="123456a")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="access_token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xL2FwaS9hdXRoL2xvZ2luIiwiaWF0IjoxNzI1NTgxNTMwLCJleHAiOjE3MjU1ODUxMzAsIm5iZiI6MTcyNTU4MTUzMCwianRpIjoiZ1NrMGs2dlVBaDdlMFc5MCIsInN1YiI6IjMiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0._TzomZkRZC4lBIBByEgfNtVvi3kU4IzuDPaShpYbqwY"),
     *             @OA\Property(property="token_type", type="string", example="bearer"),
     *             @OA\Property(property="expires_in", type="integer", example=3600)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized, invalid credentials"
     *     )
     * )
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'Não Autorizado!'
                ],
                401
            );
        }

        return $this->respondWithToken($token);
    }

    /**
     * @OA\Get(
     *     path="/me",
     *     tags={"User"},
     *     summary="Get authenticated user details",
     *     description="Retrieve details of the currently authenticated user. Requires authentication.",
     *     security={{ "bearerAuth":{} }},
     *     @OA\Response(
     *         response=200,
     *         description="User details",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=3),
     *             @OA\Property(property="name", type="string", example="Kelly"),
     *             @OA\Property(property="email", type="string", example="kelly@celke.com.br"),
     *             @OA\Property(property="email_verified_at", type="string", nullable=true, example=null),
     *             @OA\Property(property="created_at", type="string", format="date-time", example="2024-09-03T13:54:04.000000Z"),
     *             @OA\Property(property="updated_at", type="string", format="date-time", example="2024-09-03T13:54:04.000000Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized, token missing or invalid"
     *     )
     * )
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * @OA\Post(
     *     path="/logout",
     *     tags={"Auth"},
     *     summary="Logout the user",
     *     description="Logout the currently authenticated user. Requires authentication.",
     *     security={{ "bearerAuth":{} }},
     *     @OA\Response(
     *         response=200,
     *         description="Successful logout",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Deslogado com sucesso!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized, token missing or invalid"
     *     )
     * )
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(
            [
                'status' => true,
                'message' => 'Deslogado com sucesso!'
            ]
        );
    }

    /**
     * @OA\Post(
     *     path="/refresh",
     *     tags={"Auth"},
     *     summary="Refresh the access token",
     *     description="Refresh the access token using a valid token. Requires authentication.",
     *     security={{ "bearerAuth":{} }},
     *     @OA\Response(
     *         response=200,
     *         description="Successful token refresh",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="access_token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xL2FwaS9hdXRoL2xvZ2luIiwiaWF0IjoxNzI1NTgxNTMwLCJleHAiOjE3MjU1ODUxMzAsIm5iZiI6MTcyNTU4MTUzMCwianRpIjoiZ1NrMGs2dlVBaDdlMFc5MCIsInN1YiI6IjMiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0._TzomZkRZC4lBIBByEgfNtVvi3kU4IzuDPaShpYbqwY"),
     *             @OA\Property(property="token_type", type="string", example="bearer"),
     *             @OA\Property(property="expires_in", type="integer", example=3600)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized, token missing or invalid"
     *     )
     * )
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'status' => true,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
