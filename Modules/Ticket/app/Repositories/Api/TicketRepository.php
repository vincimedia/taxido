<?php

namespace Modules\Ticket\Repositories\Api;

use Exception;
use Modules\Ticket\Models\Ticket;
use Illuminate\Support\Facades\DB;
use App\Exceptions\ExceptionHandler;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

class TicketRepository extends BaseRepository
{
    public function model()
    {
        return Ticket::class;
    }

    public function boot()
    {
        try {

            $this->pushCriteria(app(RequestCriteria::class));
        } catch (Exception $e) {

            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function show($id)
    {
        try {
            $ticket = Ticket::with('messages')->findOrFail($id);
            return $ticket;
        } catch (Exception $e) {
            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function store($request)
    {
        DB::beginTransaction();
        try {

            $ticket = $this->model->create([
                "ticket_number" => $this->generateTicketNumber(),
                'user_id' => getCurrentUserId(),
                'subject' => $request->subject,
                'priority_id' => $request->priority_id,
                'department_id' => $request->department_id,
            ]);

            $message = $ticket->messages()->create([
                'message' => $request->description,
                'created_by_id' => getCurrentUserId(),
                'ticket_id' => $ticket->id,
            ]);

            if ($request->hasFile('attachments')) {
                $files = $request->attachments;
                foreach ($files as $file) {
                    $message->addMedia($file)->toMediaCollection('attachment');
                    $message->media;
                }
            }

            DB::commit();
            $ticket = $ticket->fresh();
            return $ticket;
        } catch (Exception $e) {

            DB::rollback();
            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function reply($request, $ticketId)
    {
        DB::beginTransaction();

        try {
            $ticket = $this->model->findOrFail($ticketId);

            if ($ticket->created_by_id != getCurrentUserId()) {
                throw new Exception("You are not authorized to reply to this ticket.", 400);
            }

            $message = $ticket->messages()->create([
                'message' => $request->message,
                'ticket_id' => $ticketId,
                'created_by_id' => getCurrentUserId(),
            ]);

            if ($request->hasFile('attachments')) {
                $files = $request->attachments;
                foreach ($files as $file) {
                    $message->addMedia($file)->toMediaCollection('attachment');
                }
            }
            $message->load('media');

            DB::commit();

            return $message;
        } catch (Exception $e) {
            DB::rollback();
            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }


    public function destroy($id)
    {
        try {

            return $this->model->findOrFail($id)->destroy($id);
        } catch (Exception $e) {

            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function generateTicketNumber($digit = 3)
    {
        $settings = tx_getSettings();
        $ticket_prefix = $settings['general']['ticket_prefix'];
        $ticket_suffix = $settings['general']['ticket_suffix'];

        $index = 0;
        do {
            if ($ticket_suffix == 'incremental') {

                $numbers = pow(10, $digit) + $index++;
                $ticket_number = $ticket_prefix . $numbers;
            } else {

                $numbers = rand(pow(10, $digit), pow(10, ++$digit));
                $ticket_number = $ticket_prefix . $numbers;
            }
        } while ($this->model->where('ticket_number', '=', $ticket_number)->exists());

        return $ticket_number;
    }
}
