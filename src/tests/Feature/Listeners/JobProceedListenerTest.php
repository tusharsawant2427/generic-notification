<?php
namespace App\GenericNotification\Notification\Tests\Feature\Listeners;

use App\GenericNotification\Notification\Listeners\JobProcessedListener;
use App\GenericNotification\Notification\Services\Constants\StatusType;
use Illuminate\Queue\Events\JobProcessed;
use Mockery;
use Tests\TestCase;

class JobProceedListenerTest extends TestCase
{
    public function testJobProcessedListenerHandleShouldHandleOnlyJobProcessedEvent()
    {
        $listener = Mockery::mock(JobProcessedListener::class)->makePartial();
        $jobProcessedEvent = Mockery::mock(JobProcessed::class);
        $listener->shouldReceive('handleEvent')->once()->with($jobProcessedEvent, StatusType::SENT);
        $listener->handle($jobProcessedEvent);
    }
}
