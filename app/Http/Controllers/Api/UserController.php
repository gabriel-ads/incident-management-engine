<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * @OA\Get(
     *     path="/users",
     *     tags={"User"},
     *     summary="Get paginated list of users",
     *     description="Retrieve a paginated list of all users",
     *     security={{ "bearerAuth":{} }},
     *     @OA\Response(
     *         response=200,
     *         description="A paginated list of users",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(
     *                 property="users",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="data", type="array", @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=11),
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="email", type="string", example="john.doe@example.com"),
     *                     @OA\Property(property="email_verified_at", type="string", format="date-time", nullable=true),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2024-09-05T22:07:57.000000Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-09-05T22:07:57.000000Z")
     *                 )),
     *                 @OA\Property(property="first_page_url", type="string", example="http://127.0.0.1/api/users?page=1"),
     *                 @OA\Property(property="from", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=3),
     *                 @OA\Property(property="last_page_url", type="string", example="http://127.0.0.1/api/users?page=3"),
     *                 @OA\Property(property="links", type="array", @OA\Items(
     *                     @OA\Property(property="url", type="string", nullable=true),
     *                     @OA\Property(property="label", type="string"),
     *                     @OA\Property(property="active", type="boolean")
     *                 )),
     *                 @OA\Property(property="next_page_url", type="string", example="http://127.0.0.1/api/users?page=2"),
     *                 @OA\Property(property="path", type="string", example="http://127.0.0.1/api/users"),
     *                 @OA\Property(property="per_page", type="integer", example=2),
     *                 @OA\Property(property="prev_page_url", type="string", nullable=true),
     *                 @OA\Property(property="to", type="integer", example=2),
     *                 @OA\Property(property="total", type="integer", example=6)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $users = User::orderBy('id', 'DESC')->paginate(2);
        return response()->json([
            'status' => true,
            'users' => $users
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/users/{id}",
     *     tags={"User"},
     *     summary="Get a user by ID",
     *     description="Retrieve a single user by their ID",
     *     security={{ "bearerAuth":{} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the user to retrieve",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User details",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(
     *                 property="user",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=3),
     *                 @OA\Property(property="name", type="string", example="Kelly"),
     *                 @OA\Property(property="email", type="string", example="kelly@celke.com.br"),
     *                 @OA\Property(property="email_verified_at", type="string", format="date-time", nullable=true),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-09-03T13:54:04.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-09-03T13:54:04.000000Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     )
     * )
     */
    public function show(User $user): JsonResponse
    {
        return response()->json([
            'status' => true,
            'user' => $user
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/users",
     *     tags={"User"},
     *     summary="Create a new user",
     *     description="Register a new user",
     *     security={{ "bearerAuth":{} }},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password"},
     *             @OA\Property(property="name", type="string", example="Gabriel"),
     *             @OA\Property(property="email", type="string", example="gabriel.a@email.com.br"),
     *             @OA\Property(property="password", type="string", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(
     *                 property="user",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=13),
     *                 @OA\Property(property="name", type="string", example="Gabriel"),
     *                 @OA\Property(property="email", type="string", example="gabriel.a@email.com.br"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-09-06T00:15:48.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-09-06T00:15:48.000000Z")
     *             ),
     *             @OA\Property(property="message", type="string", example="Usuário cadastrado com sucesso!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function store(UserRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'user' => $user,
                'message' => 'Usuário cadastrado com sucesso!'
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Usuário não cadastrado'
            ], 400);
        }
    }

    /**
     * @OA\Put(
     *     path="/users/{id}",
     *     tags={"User"},
     *     summary="Update an existing user",
     *     description="Update user details",
     *     security={{ "bearerAuth":{} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the user to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email"},
     *             @OA\Property(property="name", type="string", example="Gabriel 41"),
     *             @OA\Property(property="email", type="string", example="gabriel@email.com.br")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(
     *                 property="user",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=5),
     *                 @OA\Property(property="name", type="string", example="Gabriel 41"),
     *                 @OA\Property(property="email", type="string", example="gabriel@email.com.br"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-09-03T13:54:05.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-09-05T16:34:47.000000Z")
     *             ),
     *             @OA\Property(property="message", type="string", example="Usuário editado com sucesso!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function update(UserRequest $request, User $user): JsonResponse
    {
        $currentUser = User::find($user->id);
        DB::beginTransaction();

        try {
            $user->update([
                'name' => ($request->name ? $request->name : $currentUser->name),
                'email' => ($request->email ? $request->email : $currentUser->email),
                'password' => ($request->password ? $request->password : $currentUser->password),
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'user' => $user,
                'message' => 'Usuário editado com sucesso!'
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Usuário não editado'
            ], 400);
        }
    }

    /**
     * @OA\Delete(
     *     path="/users/{id}",
     *     tags={"User"},
     *     summary="Delete an existing user",
     *     description="Remove a user by ID",
     *     security={{ "bearerAuth":{} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the user to delete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(
     *                 property="user",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=6),
     *                 @OA\Property(property="name", type="string", example="Gabriel 40"),
     *                 @OA\Property(property="email", type="string", example="gabriel@email.com.br"),
     *                 @OA\Property(property="email_verified_at", type="string", format="date-time", example=null),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-09-03T14:27:01.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-09-04T00:57:43.000000Z")
     *             ),
     *             @OA\Property(property="message", type="string", example="Usuário apagado com sucesso!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function destroy(User $user): JsonResponse
    {
        try {
            $user->delete();

            return response()->json([
                'status' => true,
                'user' => $user,
                'message' => 'Usuário apagado com sucesso!'
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Usuário não deletado'
            ], 400);
        }
    }
}
