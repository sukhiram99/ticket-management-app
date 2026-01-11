<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\TicketRequest;
use App\Http\Requests\UpdateTicketRequest;

use App\Models\Ticket;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

use Exception;

class TicketController extends Controller
{

    public function index(Request $request)
    {
        try {
            // Retrieve tickets for the authenticated user
            $user = $request->user();

            $tickets = Ticket::where('user_id', $user->id)->get();

            // Optional: Log the access for audit trails
            Log::channel('tickets')->info('Tickets retrieved', [
                'user_id' => $user->id,
                'count' => $tickets->count()
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Ticket retrieve successfully',
                'data' => $tickets
            ], 200);
        } catch (Exception $e) {
            // Log the failure to your custom channel
            Log::channel('tickets')->error('Failed to retrieve tickets', [
                'user_id' => $request->user()?->id,
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Unable to fetch tickets at this time.'
            ], 500);
        }
    }

    public function store(TicketRequest $request)
    {
        DB::beginTransaction();

        try {

            $ticket = Ticket::create($request->validated());

            DB::commit();

            Log::channel('tickets')->info('Ticket created successfully', ['ticket_id' => $ticket->id]);

            return response()->json(['status' => 'success', 'message' => 'Ticket created successfully.'], 200);
        } catch (Exception $e) {
            DB::rollBack();

            Log::channel('tickets')->error('Ticket creation failed', [
                'error' => $e->getMessage(),
                'all_request' => $request()->all()
            ]);

            return response()->json(['status' => 'error', 'message' => 'Ticket creation failed: ' . $e->getMessage()], 500);
        }
    }

    public function update(UpdateTicketRequest $request, Ticket $ticket)
    {

        DB::beginTransaction();

        try {
            // Perform the update using validated data
            $ticket->update($request->only('title', 'description', 'status'));

            DB::commit();

            // Log the successful update
            Log::channel('tickets')->info('Ticket updated successfully', [
                'ticket_id' => $ticket->id,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Ticket updated successfully.',
                'data' => $ticket
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error with request details
            Log::channel('tickets')->error('Ticket update failed', [
                'ticket_id' => $ticket->id,
                'error' => $e->getMessage(),
                'payload' => $request->only('title', 'description', 'status')
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Ticket update failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function close(Ticket $ticket)
    {
        try {
            // Perform the update
            $ticket->update(['status' => 'closed']);

            Log::channel('tickets')->info('Ticket status updated to closed', [
                'ticket_id' => $ticket->id,
                'updated_by' => auth()->id()
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Ticket closed successfully.'
            ], 200);
        } catch (Exception $e) {
            // Log the error for debugging
            Log::channel('tickets')->error('Failed to close ticket', [
                'ticket_id' => $ticket->id,
                'error_message' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while closing the ticket.'
            ], 500);
        }
    }
}
