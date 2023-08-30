<?php

namespace App\GenericNotification\Notification\Tests\Feature\Http\Controllers;

use App\GenericNotification\Notification\Models\GenericNotification;
use App\GenericNotification\Notification\Services\Mail\MailBody;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GenericNotificationControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testTrackEmailSuccess()
    {
        $mailBody = new MailBody("test subject", "test message");
        $data =  [
            'identifier' => $mailBody->getUniqueIdentifier(),
            'type' => $mailBody->getType(),
            'medium' => $mailBody->getMedium(),
            'data' => json_encode($mailBody->getData()),
            'sent_at' => Carbon::now(),
            'status' => $mailBody->getStatus()
        ];

        $notification = GenericNotification::persistCreateGenericNotification($data);
        $trackingUrl = route('track.email', ['unique_identifier' => $mailBody->getUniqueIdentifier()]);
        $response = $this->get($trackingUrl);
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/png');
        $this->assertEquals(1, $notification->fresh()->open_count);
    }

    public function testTrackEmailNotFound()
    {
        $trackingUrl = route('track.email', ['unique_identifier' => "13213123"]);
        $response = $this->get($trackingUrl);
        $response->assertStatus(404);
    }

    public function testTrackEmailMultipleRequests()
    {
        $mailBody = new MailBody("test subject", "test message");
        $data =  [
            'identifier' => $mailBody->getUniqueIdentifier(),
            'type' => $mailBody->getType(),
            'medium' => $mailBody->getMedium(),
            'data' => json_encode($mailBody->getData()),
            'sent_at' => Carbon::now(),
            'status' => $mailBody->getStatus()
        ];

        $notification = GenericNotification::persistCreateGenericNotification($data);
        $trackingUrl = route('track.email', ['unique_identifier' => $mailBody->getUniqueIdentifier()]);
        $response = $this->get($trackingUrl);
        $response2 = $this->get($trackingUrl);
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/png');
        $response2->assertStatus(200);
        $response2->assertHeader('Content-Type', 'image/png');
        $this->assertEquals(2, $notification->fresh()->open_count);
    }

    public function testTrackEmailPixelImageIsSame()
    {
        $mailBody = new MailBody("test subject", "test message");
        $data =  [
            'identifier' => $mailBody->getUniqueIdentifier(),
            'type' => $mailBody->getType(),
            'medium' => $mailBody->getMedium(),
            'data' => json_encode($mailBody->getData()),
            'sent_at' => Carbon::now(),
            'status' => $mailBody->getStatus()
        ];

        $notification = GenericNotification::persistCreateGenericNotification($data);
        $trackingUrl = route('track.email', ['unique_identifier' => $mailBody->getUniqueIdentifier()]);
        $response = $this->get($trackingUrl);

        $this->assertFileEquals(
            public_path('generic-notification/pixel.png'),
            $response->getFile()->getPathname()
        );
    }
}
