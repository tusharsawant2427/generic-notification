<?php
namespace App\GenericNotification\Notification\Tests\Feature\Models;

use App\GenericNotification\Notification\Models\GenericNotification;
use App\GenericNotification\Notification\Services\Constants\GenericNotificationType;
use App\GenericNotification\Notification\Services\Constants\MediumType;
use App\GenericNotification\Notification\Services\Constants\StatusType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GenericNotificationTest extends TestCase
{

    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testCreateGenericNotificationShouldCreateGenericNotification()
    {
        $genericNotificationData = [
            'identifier' => '12345',
            'type' => GenericNotificationType::GENERAL,
            'medium' => MediumType::MAIL,
            'event' => 'Test Event',
            'data' => json_encode(['key' => 'value']),
            'sent_at' => now(),
            'status' => StatusType::SENT,
            'created_by' => 1,
            'description' => 'Test Notification',
            'opened_at' => null,
        ];

        $genericNotification = GenericNotification::persistCreateGenericNotification($genericNotificationData);

        $this->assertInstanceOf(GenericNotification::class, $genericNotification);
        $this->assertEquals($genericNotificationData['identifier'], $genericNotification->identifier);
    }

    public function testUpdateOpenStatusShouldUpdateStatusAndCount()
    {
        $genericNotificationData = [
            'identifier' => '12345',
            'type' => GenericNotificationType::GENERAL,
            'medium' => MediumType::MAIL,
            'event' => 'Test Event',
            'data' => json_encode(['key' => 'value']),
            'sent_at' => now(),
            'status' => StatusType::SENT,
            'created_by' => 1,
            'description' => 'Test Notification',
            'opened_at' => null,
        ];

        $genericNotification = GenericNotification::create($genericNotificationData);

        $result = $genericNotification->updateOpenAtAndOpenStatus();

        $this->assertTrue($result);
        $this->assertNotNull($genericNotification->opened_at);
        $this->assertEquals(StatusType::OPEN, $genericNotification->status);
    }

    public function testIncrementOpenCountShouldReturnOne()
    {
        $genericNotificationData = [
            'identifier' => '12345',
            'type' => GenericNotificationType::GENERAL,
            'medium' => MediumType::MAIL,
            'event' => 'Test Event',
            'data' => json_encode(['key' => 'value']),
            'sent_at' => now(),
            'status' => StatusType::SENT,
            'created_by' => 1,
            'description' => 'Test Notification',
            'opened_at' => null,
            'open_count' => 0,
        ];

        $genericNotification = GenericNotification::create($genericNotificationData);

        $result = $genericNotification->incrementOpenCount();

        $this->assertTrue($result);
        $this->assertEquals(1, $genericNotification->open_count);
    }
}
