<?php

namespace Modules\Ticket\Http\Controllers\Api;

use Exception;
use Illuminate\Http\Request;
use Modules\Ticket\Models\Ticket;
use Modules\Taxido\Enums\RoleEnum;
use App\Exceptions\ExceptionHandler;
use App\Http\Controllers\Controller;
use Modules\Ticket\Repositories\Api\TicketRepository;
use Modules\Ticket\Http\Requests\Api\CreateReplyRequest;
use Modules\Ticket\Http\Requests\Api\CreateTicketRequest;

class TicketController extends Controller
{
    public $repository;

    public function  __construct(TicketRepository $repository)
    {
        $this->repository = $repository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            return $this->filter($request);
        } catch (Exception $e) {
            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateTicketRequest $request)
    {
        return $this->repository->store($request);
    }

    public function reply(CreateReplyRequest $request)
    {
        $ticketId = $request->ticket_id;
        return $this->repository->reply($request, $ticketId);
    }

    /**
     * Display the specified resource.
     */
    public function show(Ticket $ticket)
    {
        return $this->repository->show($ticket?->id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    public function filter(Request $request)
    {
        $user_id = $request->user_id ?? getCurrentUserId();
        $roleName = getCurrentRoleName();

        $ticketQuery = Ticket::query();
        
        if ($roleName == RoleEnum::RIDER) {
            $ticketQuery->where('user_id', $user_id);

        } elseif ($roleName == RoleEnum::DRIVER) {
            $ticketQuery->where('user_id', $user_id);
        }

        $ticketQuery->where(function ($query) use ($user_id) {
            $query->where('user_id', $user_id)
            ->orWhere('user_id', $user_id);
        });

        if (isset($request->status)) {
            $ticketQuery->where('status', $request->status);
        }

        if ($request->field && $request->sort) {
            $ticketQuery->orderBy($request->field, $request->sort);
        }

        $paginate = $request->paginate ?? 15;
        $tickets = $ticketQuery->paginate($paginate);

        return response()->json($tickets); 
    }

}
