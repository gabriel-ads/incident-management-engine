<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\IncidentRequest;
use App\Jobs\CreateIncidentJob;
use App\Jobs\DeleteIncidentJob;
use App\Jobs\UpdateIncidentJob;
use App\Models\Incident;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class IncidentController extends Controller
{
    /**
     * @OA\Get(
     *     path="/incidents",
     *     tags={"Incident"},
     *     summary="Get all incidents",
     *     description="Retrieve a paginated list of all incidents",
     *     security={{ "bearerAuth":{} }},
     *     @OA\Response(
     *         response=200,
     *         description="A list of incidents",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(
     *                 property="incidents",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="data", type="array", 
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=35),
     *                         @OA\Property(property="name", type="string", example="Exemplo swagger"),
     *                         @OA\Property(property="evidence", type="string", example="Nosso e-mail corporativo chega pishing a todo momento, precisamos de ajuda para bloquear alguns remetentes"),
     *                         @OA\Property(property="criticality", type="integer", example=1),
     *                         @OA\Property(property="host", type="string", example="ambev.tech.br"),
     *                         @OA\Property(property="created_at", type="string", format="date-time", example="2024-09-05T17:41:58.000000Z"),
     *                         @OA\Property(property="updated_at", type="string", format="date-time", example="2024-09-05T17:41:58.000000Z"),
     *                         @OA\Property(property="user_id", type="integer", example=5),
     *                         @OA\Property(property="deleted_at", type="string", format="date-time", example="null")
     *                     )
     *                 ),
     *                 @OA\Property(property="first_page_url", type="string", example="http://127.0.0.1/api/incidents?page=1"),
     *                 @OA\Property(property="from", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=1),
     *                 @OA\Property(property="last_page_url", type="string", example="http://127.0.0.1/api/incidents?page=1"),
     *                 @OA\Property(property="links", type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="url", type="string", example="null"),
     *                         @OA\Property(property="label", type="string", example="&laquo; Previous"),
     *                         @OA\Property(property="active", type="boolean", example=false)
     *                     )
     *                 ),
     *                 @OA\Property(property="next_page_url", type="string", example="null"),
     *                 @OA\Property(property="path", type="string", example="http://127.0.0.1/api/incidents"),
     *                 @OA\Property(property="per_page", type="integer", example=5),
     *                 @OA\Property(property="prev_page_url", type="string", example="null"),
     *                 @OA\Property(property="to", type="integer", example=3),
     *                 @OA\Property(property="total", type="integer", example=3)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $incidents = Incident::orderBy('id', 'DESC')->paginate(10);

        return response()->json([
            'status' => true,
            'incidents' => $incidents
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/incidents",
     *     tags={"Incident"},
     *     summary="Create a new incident",
     *     description="Submit a new incident for processing",
     *     security={{ "bearerAuth":{} }},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "evidence", "criticality", "host", "user_id"},
     *             @OA\Property(property="name", type="string", example="Exemplo swagger"),
     *             @OA\Property(property="evidence", type="string", example="Nosso e-mail corporativo chega pishing a todo momento, precisamos de ajuda para bloquear alguns remetentes"),
     *             @OA\Property(property="criticality", type="integer", example=1),
     *             @OA\Property(property="host", type="string", example="ambev.tech.br"),
     *             @OA\Property(property="user_id", type="integer", example=5)
     *         )
     *     ),
     *     @OA\Response(
     *         response=202,
     *         description="Incident being processed",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Incidente sendo processado!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Incidente não cadastrado")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function store(IncidentRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            CreateIncidentJob::dispatch($request->validated())->onQueue('default');
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Incidente sendo processado!'
            ], 202);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Incidente não cadastrado'
            ], 400);
        }
    }

    /**
     * @OA\Put(
     *     path="/incidents/{id}",
     *     tags={"Incident"},
     *     summary="Update an existing incident",
     *     description="Update the details of an existing incident",
     *     security={{ "bearerAuth":{} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="The ID of the incident to update"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "evidence", "criticality", "host"},
     *             @OA\Property(property="name", type="string", example="Exemplo swagger"),
     *             @OA\Property(property="evidence", type="string", example="Nosso e-mail corporativo chega pishing a todo momento, precisamos de ajuda para bloquear alguns remetentes"),
     *             @OA\Property(property="criticality", type="integer", example=1),
     *             @OA\Property(property="host", type="string", example="ambev.tech.br")
     *         )
     *     ),
     *     @OA\Response(
     *         response=202,
     *         description="Incident being processed",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Incidente editado está sendo processado!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Incidente não atualizado")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */

    public function update(IncidentRequest $request, Incident $incident): JsonResponse
    {
        DB::beginTransaction();

        try {
            UpdateIncidentJob::dispatch($incident, $request->validated())->onQueue('default');

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Incidente editado está sendo processado!'
            ], 202);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Incidente não atualizado'
            ], 400);
        }
    }

    /**
     * @OA\Delete(
     *     path="/incidents/{id}",
     *     tags={"Incident"},
     *     summary="Delete an incident",
     *     description="Delete an existing incident record by ID",
     *     security={{ "bearerAuth":{} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the incident to delete",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=202,
     *         description="Incident deletion being processed",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Incidente apagado está sendo processado!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Incidente não deletado")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */

    public function destroy(Incident $incident): JsonResponse
    {
        try {
            DeleteIncidentJob::dispatch($incident->id)->onQueue('default');

            return response()->json([
                'status' => true,
                'message' => 'Incidente apagado está sendo processado!'
            ], 202);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Incidente não deletado'
            ], 400);
        }
    }
}
