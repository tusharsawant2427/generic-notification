<?php
namespace App\GenericNotification\Notification\Tests\Feature\Jobs;

use App\GenericNotification\Notification\Jobs\MailJob;
use App\GenericNotification\Notification\Models\GenericNotification;
use App\GenericNotification\Notification\Services\Constants\StatusType;
use App\GenericNotification\Notification\Services\Mail\GenericMail;
use App\GenericNotification\Notification\Services\Mail\MailBody;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class MailJobTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testSendToQueueWillQueueMailable()
    {
        Mail::fake();
        $mailBody = new MailBody("Test Subject", "Test Message");
        $mailBody->addEmail("testmail@gmail.com");
        $mailBody->addCcEmail("testccmail@gmail.com");
        $mailable = new GenericMail($mailBody);

        $genericNotification = new GenericNotification();
        $genericNotification->data = ['key' => 'value'];

        $mailJob = new MailJob($mailable);
        $mailJob->sendToQueue($genericNotification);

        Mail::assertQueued(GenericMail::class, function ($mail) use ($mailBody) {
            return $mail->to[0]['address'] === $mailBody->getEmails()[0];
        });

        $this->assertEquals(StatusType::IN_QUEUE, $genericNotification->status);
    }

    public function testJobIsDispatchedWithMailable()
    {
        Mail::fake();
        Bus::fake();
        /**
         * @var MailBody $mailBody
         */

         $mailBody = new MailBody("Test Subject", "Test Message");
         $mailBody->addEmail("testmail@gmail.com");
         $mailBody->addCcEmail("testccmail@gmail.com");

        MailJob::dispatch(new GenericMail($mailBody))->allOnQueue('emails');
        Bus::assertDispatched(MailJob::class);
    }

    public function testMailBodyIsStored()
    {
        $subject = 'test subject....';
        $message = 'Test message';
        $mailBody = new MailBody($subject, $message);
        MailJob::dispatch(new GenericMail($mailBody));
        $this->assertDatabaseCount('generic_notifications', 1);
        $this->assertDatabaseHas('generic_notifications', ['type' => $mailBody->getType(), 'identifier' => $mailBody->getUniqueIdentifier()]);

    }

}
