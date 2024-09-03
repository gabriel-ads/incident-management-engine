<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\IncidentRequest;
use App\Models\Incident;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
            $incident = Incident::create([
                'name' => $request->name,
                'evidence' => $request->evidence,
                'criticality' => $request->criticality,
                'host' => $request->host,
                'user_id' => $request->user_id
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'incident' => $incident,
                'message' => 'Incidente cadastrado com sucesso!'
            ], 201);
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
        $currentIncident = Incident::find($incident->id);
        DB::beginTransaction();

        try {
            $incident->update([
                'name' => ($request->name ? $request->name : $currentIncident->name),
                'evidence' => ($request->evidence ? $request->evidence : $currentIncident->evidence),
                'criticality' => ($request->criticality ? $request->criticality : $currentIncident->criticality),
                'host' => ($request->host ? $request->host : $currentIncident->host),
                'user_id' => $currentIncident->user_id
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'incident' => $incident,
                'message' => 'Incidente editado com sucesso!'
            ], 200);
        } catch (Exception $e) {
            dd($e);
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
            $incident->delete();

            return response()->json([
                'status' => true,
                'incident' => $incident,
                'message' => 'Incidente apagado com sucesso!'
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Incidente não apagado'
            ], 400);
        }
    }
}
