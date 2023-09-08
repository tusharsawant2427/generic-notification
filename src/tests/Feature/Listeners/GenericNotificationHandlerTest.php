<?php
namespace App\GenericNotification\Notification\Tests\Feature\Listeners;

use App\GenericNotification\Notification\Models\GenericNotification;
use App\GenericNotification\Notification\Services\Constants\StatusType;
use Illuminate\Contracts\Queue\Job;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class GenericNotificationHandlerTest extends TestCase
{
    public function testHandleEventWithExistingNotification()
    {
        $jobProcessedEvent = $this->getMockBuilder(JobProcessed::class)
            ->disableOriginalConstructor()
            ->getMock();

        $job = $this->getMockBuilder(Job::class)
            ->disableOriginalConstructor()
            ->getMock()
            ->method('uuid')->willReturn('1212121212');
        $jobProcessedEvent->job = $job;

        $genericNotification = $this->getMockBuilder(GenericNotification::class)
            ->disableOriginalConstructor()
            ->getMock();
        $genericNotification->data = ['key' => 'value'];
        $genericNotification->status = StatusType::IN_QUEUE;

        $genericNotification->expects($this->once())->method('updateStatus');
        $handler = $this->getMockForAbstractClass(GenericNotificationHandler::class);

        $handler->handleEvent($jobProcessedEvent, StatusType::SENT);
        Log::shouldReceive('info')->with("GenericNotification Notification Job Status: ")->once();

        self::assertEquals(StatusType::SENT, $genericNotification->status);
    }
}
