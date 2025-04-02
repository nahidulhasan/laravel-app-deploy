<?php

namespace Tests\Feature;

use App\Repositories\ComplianceEntryRepository;
use App\Services\ComplianceEntryService;
use App\Services\FormFieldMappingService;
use App\Services\TicketCreationApiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Tests\TestCase;

class NextDueDateTest extends TestCase
{
    /**
     * @dataProvider calculateNextDueDateDataProvider
     */
    public function testCalculateNextDueDate($startDate, $frequency, $dueMonth, $dueDate, $expectedOutput)
    {
        $repositoryMock = $this->createMock(ComplianceEntryRepository::class);
        $serviceMock = $this->createMock(FormFieldMappingService::class);
        $ticketCreationMock = $this->createMock(TicketCreationApiService::class);

        $service = new ComplianceEntryService($repositoryMock, $serviceMock,$ticketCreationMock); // replace YourClass with the actual class name

        // Call the method you want to test
        $result = $service->getNextDueDate($startDate, $frequency, $dueMonth, $dueDate);
        $this->assertEquals($expectedOutput, $result);

        // Add more assertions as needed based on the behavior of your method
    }

    public function calculateNextDueDateDataProvider()
    {
        return [
            // Yearly
            ['2023-04-30', 'yearly', '1', '10', '2024-01-10'],
            ['2025-01-01', 'yearly', '5', '10', '2025-05-10'],
            ['2023-04-30', 'yearly', '8', '10', '2023-08-10'],
            ['2023-05-02', 'yearly', '6', '10', '2023-06-10'],
            ['2023-02-01', 'yearly', '2', '10', '2023-02-10'],

    
            // Quarterly
            ['2023-04-30', 'quarterly', '1', '10', '2023-07-10'],
            ['2025-01-01', 'quarterly', '2', '10', '2025-02-10'],
            ['2023-04-30', 'quarterly', '2', '10', '2023-05-10'],
            ['2023-05-02', 'quarterly', '2', '10', '2023-05-10'],
            ['2023-02-01', 'quarterly', '2', '10', '2023-02-10'],
    
            // Monthly
            ['2023-04-30', 'monthly', null, '20', '2023-05-20'],
            ['2023-08-30', 'monthly', null, '20', '2023-09-20'],
            ['2023-04-30', 'monthly', null, '20', '2023-05-20'],
            ['2023-05-02', 'monthly', null, '20', '2023-05-20'],
            ['2023-02-01', 'monthly', null, '20', '2023-02-20'],

    
           // Fortnightly
            ['2023-04-02', 'fortnightly', null, '1', '2023-04-16'],
            ['2023-08-30', 'fortnightly', null, '10', '2023-09-10'],
            ['2023-04-30', 'fortnightly', null, '12', '2023-05-12'],
            ['2023-05-02', 'fortnightly', null, '10', '2023-05-10'],
            ['2023-02-11', 'fortnightly', null, '10', '2023-02-25'],

            // Weekly
            ['2023-04-01', 'weekly', null, '3', '2023-04-04'],
            ['2023-06-30', 'weekly', null, '3', '2023-07-04'],
            ['2023-04-30', 'weekly', null, '6', '2023-05-05'],
            ['2023-05-02', 'weekly', null, '5', '2023-05-04'],
            ['2023-02-01', 'weekly', null, '7', '2023-02-04'],

            // Daily
            ['2023-04-03', 'daily', null, null, '2023-04-04'],
            ['2023-06-30', 'daily', null, null, '2023-07-01'],
            // Add more daily test cases as needed
        ];
    }
    
}
