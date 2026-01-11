<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;

use App\Models\User;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

use Exception;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        DB::beginTransaction();

        try {

            $user = User::create($request->validated());

            DB::commit();

            Log::channel('tickets')->info('User registered successfully', ['user_id' => $user->id]);

            return response()->json(['status' => 'success', 'message' => 'User Registration successfully.'], 200);
        } catch (Exception $e) {
            DB::rollBack();

            Log::channel('tickets')->error('Registration failed', [
                'error' => $e->getMessage(),
                'all_request' => $request()->all()
            ]);

            return response()->json(['status' => 'error', 'message' => 'Registration failed: ' . $e->getMessage()], 500);
        }
    }


    public function login(LoginRequest $request)
    {
        // Start transaction for atomic operations (e.g. updating login stats)
        DB::beginTransaction();

        try {
            if (!Auth::attempt($request->validated())) {
                // Log failed attempt to your custom channel
                Log::channel('tickets')->warning('Failed login attempt', [
                    'email' => $request->email,
                ]);

                DB::rollBack();

                return response()->json(['status' => 'error', 'message' => 'Invalid credentials'], 422);
            }

            $user = Auth::user();
            $token = $user->createToken('api')->plainTextToken;

            // the transaction ensures everything succeeds together.
            DB::commit();

            Log::channel('tickets')->info('User logged in successfully', ['user_id' => $user->id]);

            return response()->json(['status' => 'success', 'message' => 'User logged in successfully', 'user_details' => $user, 'access_token' => $token], 200);
        } catch (Exception $e) {
            DB::rollBack();

            Log::channel('tickets')->error('Login process error', [
                'error' => $e->getMessage(),
                'email' => $request->email
            ]);

            return response()->json(['status' => 'error', 'message' => 'An unexpected error occurred during login.'], 500);
        }
    }

    public function logout(Request $request)
    {
        try {

            $user = $request->user();

            // Perform the token deletion
            $user->currentAccessToken()->delete();

            // Log the success to your custom channel
            Log::channel('tickets')->info('User logged out successfully', [
                'user_id' => $user->id,
                'ip' => $request->ip()
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'User logged out successfully'
            ], 200);
        } catch (Exception $e) {
            // Log the specific failure for debugging
            Log::channel('tickets')->error('Logout failed', [
                'user_id' => $request->user()?->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to logout, please try again.'
            ], 500);
        }
    }

    public function getProfile(Request $request)
    {
        try {

            // Perform the token deletion
            $user = User::with(['tickets', 'replies'])
                ->withCount(['tickets', 'replies'])
                ->findOrFail(auth()->id());


            // Log the success to your custom channel
            Log::channel('tickets')->info('User profile fetched successfully', [
                'user_id' => $user->id,
                'ticket_count' => $user->tickets_count, // Use the new count attribute
                'reply_count' => $user->replies_count, // Use the new count attribute
                'ip' => $request->ip()
            ]);


            return response()->json([
                'status' => 'success',
                'message' => 'User profile retrieved successfully.',
                'data' => [
                    'user' => $user,
                    'total_tickets' => $user->tickets_count,
                    'reply_count' => $user->reply_count
                ]
            ], 200);
        } catch (Exception $e) {
            // Log the specific failure for debugging
            Log::channel('tickets')->error('User profile fetch failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Could not retrieve profile information.'
            ], 500);
        }
    }
}
