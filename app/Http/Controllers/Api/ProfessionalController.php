<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Professional;
use Illuminate\Http\JsonResponse;

class ProfessionalController extends Controller
{
    /**
     * List all active professionals (Staff only).
     * Used by Unavailability.vue and Dashboard.vue.
     */
    public function index(): JsonResponse
    {
        $professionals = Professional::select('id', 'full_name', 'specialty', 'is_active')
            ->orderBy('full_name')
            ->get();

        return response()->json(['data' => $professionals]);
    }
}
