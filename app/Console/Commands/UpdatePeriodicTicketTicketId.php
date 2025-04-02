<?php

namespace App\Console\Commands;

use App\Models\PeriodicTicket;
use Illuminate\Console\Command;
use App\Traits\RequestService;

class UpdatePeriodicTicketTicketId extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update-periodic-ticket-ticket-id';
    protected $periodicTicketService;
    use RequestService;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Periodic Ticket';

    protected $ticketDetails='';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $macAddress = $this->getHost();
        $this->ticketDetails = $macAddress . '/api/v1/ticket/tickets-details';
    }
    /**
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle()
    {
        echo 'Process start ... ' . PHP_EOL;
        $updateTicket = $this->updateTicketId();
        echo 'Total ' . $updateTicket . ' Periodic ticket has been updated' . PHP_EOL;
        echo 'Process End ' . PHP_EOL;

    }

    public function updateTicketId()
    {
        $ticketList = PeriodicTicket::whereNull('ticket_id')->pluck('periodic_ticket_id', 'id')->toArray();
        $updateCount = 0;
        if (!empty($ticketList)) {
            foreach ($ticketList as $key => $value) {
                $url = $this->ticketDetails . '/' . $value;
                $response = RequestService::request('GET', $url);
                if (isset($response['status_code']) && $response['status_code'] == 200 & isset($response['data'])) {
                    $responseData = $response['data'][0];
                    if (!empty($responseData) && $responseData['serial'] == $value) {
                        $update = PeriodicTicket::where('periodic_ticket_id', $value)
                            ->update([
                                'ticket_id' => $responseData['id']
                            ]);

                        if ($update) {
                            $updateCount++;
                            echo $responseData['serial'] . ' Periodic ticket has been updated' . PHP_EOL;
                        }
                    }
                }

            }
        }
        return $updateCount;
    }

    /**
     * @return bool|mixed|string|null
     */
    public function getHost()
    {
        return env('CHT_HOST');
    }




}
