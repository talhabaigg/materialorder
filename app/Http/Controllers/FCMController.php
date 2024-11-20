<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class FCMController extends Controller
{
    public function saveToken(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string',
        ]);

        $user = Auth::user();

        // Check if the user is authenticated
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // If the token is different, update it
        if ($user->fcm_token !== $request->fcm_token) {
            $user->fcm_token = $request->fcm_token;
            $user->save();
            return response()->json([
                'message' => 'FCM Token updated successfully',
                'fcm_token' => $request->fcm_token, // Include the updated token in the response
            ], 200);
        }

        return response()->json(['message' => 'Token is already up-to-date'], 200);
    }
}
