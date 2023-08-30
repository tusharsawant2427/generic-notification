<?php
namespace App\GenericNotification\Notification\Services;

use App\GenericNotification\Notification\Models\GenericNotification;
use App\GenericNotification\Notification\Services\Interfaces\GenericNotifiableInterface;
use Carbon\Carbon;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class GenericNotificationServiceTest extends TestCase
{
    use RefreshDatabase;

    public function testStoreSuccess()
    {
        /**
         * @var MockInterface $genericNotifiable
         */
        $genericNotifiable = Mockery::mock(GenericNotifiableInterface::class);
        $genericNotifiable->shouldReceive('getUniqueIdentifier')->andReturn('123');
        $genericNotifiable->shouldReceive('getType')->andReturn(1);
        $genericNotifiable->shouldReceive('getMedium')->andReturn(2);
        $genericNotifiable->shouldReceive('getData')->andReturn(['key' => 'value']);
        $genericNotifiable->shouldReceive('getStatus')->andReturn(3);
        Carbon::setTestNow(Carbon::now());
        Log::shouldReceive('error')->never();
        $genericNotification = Mockery::mock(GenericNotification::class);
        $genericNotification->shouldReceive('persistCreateGenericNotification')->andReturn(true);
        $service = new GenericNotificationService();
        $result = $service->store($genericNotifiable);
        $this->assertInstanceOf(GenericNotification::class, $result);
        $this->assertEquals(123, $result->identifier);

    }

    public function testStoreFailure()
    {
        /**
         * @var MockInterface $genericNotifiable
         */
        $genericNotifiable = Mockery::mock(GenericNotifiableInterface::class);
        $genericNotifiable->shouldReceive('getUniqueIdentifier')->andReturn('');
        $genericNotifiable->shouldReceive('getType')->andReturn(1);
        $genericNotifiable->shouldReceive('getMedium')->andReturn(2);
        $genericNotifiable->shouldReceive('getData')->andReturn(['key' => 'value']);
        $genericNotifiable->shouldReceive('getStatus')->andReturn(3);
        Log::shouldReceive('error')->once();
        $genericNotification = Mockery::mock(GenericNotification::class);
        $genericNotification->shouldReceive('persistCreateGenericNotification')->andReturn(false);
        $service = new GenericNotificationService();
        $this->expectException(Exception::class);
        $service->store($genericNotifiable);
    }

    public function testSetOpenAtAndOpenStatusShouldUpdateOpenAtAndOpenStatusAndReturnTrue()
    {
        $genericNotification = Mockery::mock(GenericNotification::class);
        $genericNotification->shouldReceive('updateOpenAtAndOpenStatus')->andReturn(true);
        $service = new GenericNotificationService();

        $result = $service->setOpenAtAndOpenStatus($genericNotification);

        $this->assertTrue($result);
    }

    public function testUpdateOpenCountShouldIncrementOpenCountAndReturnTru()
    {
        $genericNotification = Mockery::mock(GenericNotification::class);
        $genericNotification->shouldReceive('incrementOpenCount')->andReturn(true);
        $service = new GenericNotificationService();
        $result = $service->updateOpenCount($genericNotification);
        $this->assertTrue($result);
    }
}
