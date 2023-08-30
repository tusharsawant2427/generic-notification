<?php
namespace App\GenericNotification\Notification\Tests\Feature\Services\Mail;

use App\GenericNotification\Notification\Services\Mail\GenericMail;
use App\GenericNotification\Notification\Services\Mail\MailBody;
use Illuminate\Support\Facades\Mail;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class GenericMailTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testBuildMethodWithAttachments()
    {
        /**
         * @var MockInterface $mailBody
         */
        $mailBody = Mockery::mock(MailBody::class);
        $mailBody->shouldReceive('getSubject')->andReturn('Test Subject');
        $mailBody->shouldReceive('getView')->andReturn('emails.test');
        $mailBody->shouldReceive('getMessage')->andReturn('Test Message');
        $mailBody->shouldReceive('getAttachments')->andReturn(['attachment1.txt', 'attachment2.pdf']);
        $mailBody->shouldReceive('getTrackingHtmlContent')->andReturn("<img src='/test/tracking/2213123/pixel.png' width='1' height='1' />");

        Mail::fake();

        $genericMail = new GenericMail($mailBody);
        $email = $genericMail->build();

        $this->assertEquals('Test Subject', $email->subject);
        $this->assertEquals('emails.test', $email->markdown);
        $this->assertEquals('Test Message', $email->viewData['body']);
        $this->assertEquals("<img src='/test/tracking/2213123/pixel.png' width='1' height='1' />", $email->viewData['tracking_code']);

        $this->assertCount(2, $email->attachments);
    }

    public function testBuildMethodWithoutAttachments()
    {
        /**
         * @var MockInterface $mailBody
         */
        $mailBody = Mockery::mock(MailBody::class);
        $mailBody->shouldReceive('getSubject')->andReturn('Test Subject');
        $mailBody->shouldReceive('getView')->andReturn('emails.test');
        $mailBody->shouldReceive('getMessage')->andReturn('Test Message');
        $mailBody->shouldReceive('getAttachments')->andReturn([]);
        $mailBody->shouldReceive('getTrackingHtmlContent')->andReturn("<img src='/test/tracking/2213123/pixel.png' width='1' height='1' />");

        Mail::fake();

        $genericMail = new GenericMail($mailBody);
        $email = $genericMail->build();
        $this->assertEquals('Test Subject', $email->subject);
        $this->assertEquals('emails.test', $email->markdown);
        $this->assertEquals('Test Message', $email->viewData['body']);
        $this->assertEquals("<img src='/test/tracking/2213123/pixel.png' width='1' height='1' />", $email->viewData['tracking_code']);
        $this->assertEmpty($email->attachments);
    }

    public function testGetMailBodyMethod()
    {
         /**
         * @var MockInterface $mailBody
         */
        $mailBody = Mockery::mock(MailBody::class);
        $genericMail = new GenericMail($mailBody);
        $this->assertSame($mailBody, $genericMail->getMailBody());
    }
}
