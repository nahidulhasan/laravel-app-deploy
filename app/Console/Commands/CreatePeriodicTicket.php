<?php

namespace App\Console\Commands;

use App\Http\Controllers\API\V1\PeriodicTicketController;
use App\Services\PeriodicTicketService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CreatePeriodicTicket extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CreatePeriodicTicket';
    protected $periodicTicketService;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Periodic Ticket';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(PeriodicTicketService $periodicTicketService)
    {
        parent::__construct();
        $this->periodicTicketService = $periodicTicketService;
    }

    /**
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle()
    {
        Log::info('Periodic ticket create schedule has been run. Start Time ==>  ' . date('d-M-y H:i:s'));
        $this->periodicTicketService->create();
        return true;
    }
}
