<?php
namespace App\GenericNotification\Notification\Tests\Services;

use App\GenericNotification\Notification\Jobs\MailJob;
use App\GenericNotification\Notification\Jobs\SmsJob;
use App\GenericNotification\Notification\Services\Exceptions\NotificationBodyNotFoundException;
use App\GenericNotification\Notification\Services\Interfaces\GenericNotifiableInterface;
use App\GenericNotification\Notification\Services\Mail\MailBody;
use App\GenericNotification\Notification\Services\NotificationService;
use App\GenericNotification\Notification\Services\Sms\SmsBody;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Mockery;
use Tests\TestCase;

class NotificationServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testSendMailDispatchesMailJob()
    {
        Bus::fake();
        Mail::fake();
        $mailBody = new MailBody(subject: "Test Subject", message: "Hello test suite is running...");
        NotificationService::notify($mailBody);
        Bus::assertDispatched(MailJob::class);
    }

    public function testSendSmsDispatchesSmsJob()
    {
        Bus::fake();
        Http::fake([
            '*' => Http::response('Sms send successfully', 200),
        ]);
        $smsBody = new SmsBody(phoneNumber: "+917977796967", message: "Hello test suite is running...");
        $res = NotificationService::notify($smsBody);
        Bus::assertDispatched(SmsJob::class);
    }

    public function testNotifyThrowsExceptionForUnknownNotification()
    {
        $this->expectException(NotificationBodyNotFoundException::class);

        $notifiable = Mockery::mock(GenericNotifiableInterface::class);
        NotificationService::notify($notifiable);
    }
}
