<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateTicketStatusRequest;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class AdminTicketController extends Controller
{
    /**
     * List all tickets with user information for Admin view.
     */
    public function index()
    {
        try {
            // Fetch tickets with the associated user, sorted by newest first
            $tickets = Ticket::with('user')->latest()->get();

            // Log the access for administrative auditing
            Log::channel('tickets')->info('Admin retrieved all tickets list', [
                'admin_id' => auth()->id(),
                'count' => $tickets->count()
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Tickets retrieved successfully.',
                'data' => $tickets
            ], 200);
        } catch (\Exception $e) {
            // Log the specific error for server-side debugging
            Log::channel('tickets')->error('Failed to retrieve tickets for admin', [
                'admin_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Unable to fetch tickets at this time. Please try again later.'
            ], 500);
        }
    }

    /**
     * Update ticket status with transaction and logging.
     */
    public function updateStatus(UpdateTicketStatusRequest $request, Ticket $ticket)
    {
        $validatedData = $request->validated();

        DB::beginTransaction();

        try {
            // Update the ticket status
            $ticket->update([
                'status' => $validatedData['status']
            ]);

            DB::commit();

            // Log the action in the 'tickets' channel (Asia/Kolkata timezone)
            Log::channel('tickets')->info('Admin updated ticket status', [
                'ticket_id' => $ticket->id,
                'new_status' => $validatedData['status'],
                'admin_id' => auth()->id(),
                'ip_address' => $request->ip()
            ]);

            return response()->json([
                'status' => 'success',
                'message' => "Ticket status updated to {$validatedData['status']}.",
                'data' => $ticket
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();

            // Log the failure for debugging
            Log::channel('tickets')->error('Admin status update failed', [
                'ticket_id' => $ticket->id,
                'admin_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update ticket status: ' . $e->getMessage()
            ], 500);
        }
    }
}
