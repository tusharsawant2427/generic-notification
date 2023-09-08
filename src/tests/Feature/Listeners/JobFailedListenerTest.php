<?php
namespace App\GenericNotification\Notification\Tests\Feature\Listeners;

use App\GenericNotification\Notification\Listeners\JobFailedListener;
use App\GenericNotification\Notification\Services\Constants\StatusType;
use Illuminate\Queue\Events\JobFailed;
use Mockery;
use Tests\TestCase;

class JobFailedListenerTest extends TestCase
{
    public function testJobFailedListenerHandleShouldHandleOnlyJobFailedEvent()
    {
        $listener = Mockery::mock(JobFailedListener::class)->makePartial();
        $jobFailedEvent = Mockery::mock(JobFailed::class);
        $listener->shouldReceive('handleEvent')->once()->with($jobFailedEvent, StatusType::FAILED);
        $listener->handle($jobFailedEvent);
    }
}
