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
    public function index(): JsonResponse
    {
        $incidents = Incident::orderBy('id', 'DESC')->paginate(5);

        return response()->json([
            'status' => true,
            'incidents' => $incidents
        ], 200);
    }

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

    public function destroy(Incident $incident): JsonResponse
    {
        try {
            DeleteIncidentJob::dispatch($incident)->onQueue('default');

            return response()->json([
                'status' => true,
                'message' => 'Incidente apagado está sendo processado!'
            ], 202);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Incidente não apagado'
            ], 400);
        }
    }
}
