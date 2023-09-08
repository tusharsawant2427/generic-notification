<?php
namespace App\GenericNotification\Notification\Tests\Services;

use App\GenericNotification\Notification\Models\GenericNotification;
use App\GenericNotification\Notification\Services\Interfaces\GenericNotifiableInterface;
use App\GenericNotification\Notification\Services\NotificationPersist;
use Exception;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class NotificationPersistTest extends TestCase
{
    public function testPersistCreatesGenericNotification()
    {
        $genericNotifiable = Mockery::mock(GenericNotifiableInterface::class);

        /**
         * @var MockInterface $genericNotifiable
         */
        $genericNotifiable->shouldReceive('getUniqueIdentifier')->andReturn('1234567890');
        $genericNotifiable->shouldReceive('getType')->andReturn(0);
        $genericNotifiable->shouldReceive('getMedium')->andReturn(0);
        $genericNotifiable->shouldReceive('getStatus')->andReturn(0);
        $genericNotifiable->shouldReceive('getData')->andReturn(['message' => 'Test notification']);
        $genericNotifiable->shouldReceive('getStatus')->andReturn('pending');
        $result = NotificationPersist::persist($genericNotifiable);
        $this->assertInstanceOf(GenericNotification::class, $result);
    }

    public function testPersistLogsErrorAndThrowsExceptionOnFailure()
    {
        $genericNotifiable = Mockery::mock(GenericNotifiableInterface::class);

        $genericNotification = Mockery::mock(GenericNotification::class);

        $genericNotification->shouldReceive('persistCreateGenericNotification')->andThrow(
            new Exception('Failed to create notification')
        );

        $this->expectException(Exception::class);
        NotificationPersist::persist($genericNotifiable);
    }
}
