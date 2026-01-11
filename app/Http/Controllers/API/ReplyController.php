<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\ReplyRequest;

use App\Models\{Ticket, Reply};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

use Exception;


class ReplyController extends Controller
{
    public function store(ReplyRequest $request, Ticket $ticket)
    {
        // Start the transaction to ensure data integrity
        DB::beginTransaction();

        try {

            $reply = Reply::create([
                'ticket_id' => $ticket->id,
                'user_id'   => $request->user()->id,
                'message'   => $request->validated()['message']
            ]);

            DB::commit();

            // Log the success in the 'tickets' channel (IST Timezone: Asia/Kolkata)
            Log::channel('tickets')->info('New reply added to ticket', [
                'ticket_id' => $ticket->id,
                'reply_id'  => $reply->id,
                'user_id'   => $request->user()->id,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Reply posted successfully.',
                'data' => $reply
            ], 200);
        } catch (Exception $e) {
            // Rollback the database if any error occurs
            DB::rollBack();

            // Log the detailed error for debugging
            Log::channel('tickets')->error('Failed to store ticket reply', [
                'ticket_id' => $ticket->id,
                'user_id'   => $request->user()->id,
                'error'     => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while posting your reply.'
            ], 500);
        }
    }
}
