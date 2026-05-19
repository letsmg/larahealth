<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    /**
     * List all users (Staff only).
     * Used by Messages.vue to populate the recipient combobox.
     */
    public function index(): JsonResponse
    {
        $users = User::select('id', 'name', 'email', 'role')
            ->orderBy('name')
            ->get();

        return response()->json(['data' => $users]);
    }
}
