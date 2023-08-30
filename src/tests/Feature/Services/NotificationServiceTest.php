<?php
namespace App\GenericNotification\Notification\Tests\Services;

use App\GenericNotification\Notification\Jobs\MailJob;
use App\GenericNotification\Notification\Jobs\SmsJob;
use App\GenericNotification\Notification\Services\Mail\MailBody;
use App\GenericNotification\Notification\Services\NotificationService;
use App\GenericNotification\Notification\Services\Sms\SmsBody;
use Illuminate\Mail\Mailer;
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

    public function testNotifySendEmailShouldSendMailAndReturnTrueFalse()
    {
        Bus::fake();
        Mail::fake();
        $mailBody = new MailBody(subject: "Test Subject", message: "Hello test suite is running...");
        $notificationService = new NotificationService($mailBody);
        $res = $notificationService->notify();
        $this->assertTrue($res);
    }

    public function testSendMailDispatchesMailJob()
    {
        Bus::fake();
        $mailBody = new MailBody(subject: "Test Subject", message: "Hello test suite is running...");
        $notificationService = new NotificationService($mailBody);
        $notificationService->notify();
        Bus::assertDispatched(MailJob::class);
    }

    public function testNotifySendSmsShouldSendSmsAndReturnTrue()
    {
        Bus::fake();
        Http::fake([
            '*' => Http::response('Sms send successfully', 200),
        ]);
        $smsBody = new SmsBody(phoneNumber: "+917977796967", message: "Hello test suite is running...");
        $notificationService = new NotificationService($smsBody);
        $res = $notificationService->notify();
        $this->assertTrue($res);
    }

    public function testSendSmsDispatchesSmsJob()
    {
        Bus::fake();
        $smsBody = new SmsBody(phoneNumber: "+917977796967", message: "Hello test suite is running...");
        $notificationService = new NotificationService($smsBody);
        $notificationService->notify();
        Bus::assertDispatched(SmsJob::class);
    }
}
