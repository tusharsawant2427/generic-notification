<?php

namespace App\GenericNotification\Notification\Tests\Services;

use App\Exceptions\Api\NotFoundException;
use App\GenericNotification\Notification\Models\GenericNotification;
use App\GenericNotification\Notification\Services\MarkMailRead;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarkMailReadTest extends TestCase
{
    use RefreshDatabase;

    public function testHandleMarkingMailAsRead()
    {
        $notification = GenericNotification::create(
            [
                'identifier' => '232112312313',
                'data' => ['key', 'value']
            ]
        );

        MarkMailRead::handle("232112312313");

        $updatedNotification = $notification->fresh();
        $this->assertNotNull($updatedNotification->opened_at);
    }

    public function testHandleMarkingNonExistentMailAsRead()
    {
        $this->expectException(ModelNotFoundException::class);
        MarkMailRead::handle('non_existent_identifier');
    }
}
