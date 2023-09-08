<?php

namespace App\GenericNotification\Notification\Tests\Feature\Services\Mail;

use App\GenericNotification\Notification\Services\Constants\GenericNotificationType;
use App\GenericNotification\Notification\Services\Constants\MediumType;
use App\GenericNotification\Notification\Services\Constants\StatusType;
use App\GenericNotification\Notification\Services\Mail\MailBody;
use Mockery;
use Tests\TestCase;

class MailBodyTest extends TestCase
{
    public MailBody $mailBody;
    protected function setUp(): void
    {
        parent::setUp();
        $this->mailBody = new MailBody(subject: "test subject", message: "test message");
    }

    public function testConstructorShouldSetsSubjectAndMessage()
    {
        $subject = 'test subject....';
        $message = 'Test message';
        $mailBody = new MailBody($subject, $message);

        $this->assertEquals($subject, $mailBody->getSubject());
        $this->assertEquals($message, $mailBody->getMessage());
        $this->assertEquals(GenericNotificationType::GENERAL, $mailBody->getType());
        $this->assertEquals(StatusType::IN_PROCESS, $mailBody->getStatus());
        $this->assertEquals(MediumType::MAIL, $mailBody->getMedium());
        $this->assertIsArray($mailBody->getData());
        $this->assertNotEmpty($mailBody->getUniqueIdentifier());
    }


    public function testConstructorWithCustomValues()
    {
        // Arrange
        $subject = 'test Subject';
        $message = 'Test Message';
        $type = GenericNotificationType::NEWS;

        // Act
        $mailBody = new MailBody($subject, $message, $type);

        // Assert
        $this->assertEquals($subject, $mailBody->getSubject());
        $this->assertEquals($message, $mailBody->getMessage());
        $this->assertEquals($type, $mailBody->getType());
        $this->assertEquals(StatusType::IN_PROCESS, $mailBody->getStatus());
        $this->assertEquals(MediumType::MAIL, $mailBody->getMedium());
        $this->assertIsArray($mailBody->getData());
        $this->assertNotEmpty($mailBody->getUniqueIdentifier());
    }

    public function testAddSingleAttachmentShouldPushAttachmentIntoAttachmentArray()
    {
        $this->assertCount(0, $this->mailBody->getAttachments());
        $this->mailBody->addAttachment("/test/abc/test.png");
        $this->mailBody->addAttachment("/test/abc/test2.png");
        $this->assertCount(2, $this->mailBody->getAttachments());
        $this->assertContains("/test/abc/test.png", $this->mailBody->getAttachments());
        $this->assertContains("/test/abc/test2.png", $this->mailBody->getAttachments());
    }

    public function testAddArrayAttachmentsShouldPushAttachmentsIntoAttachmentArray()
    {
        $this->assertCount(0, $this->mailBody->getAttachments());
        $this->mailBody->addAttachments(["/test/abc/test.png", "/test/abc/test2.png"]);
        $this->mailBody->addAttachments(["/test/abc/test3.png"]);
        $this->assertCount(3, $this->mailBody->getAttachments());
        $this->assertContains("/test/abc/test.png", $this->mailBody->getAttachments());
        $this->assertContains("/test/abc/test2.png", $this->mailBody->getAttachments());
        $this->assertContains("/test/abc/test3.png", $this->mailBody->getAttachments());
    }

    public function testAddSingleAttachmentShouldReturnMailBodyObject()
    {
        $this->assertCount(0, $this->mailBody->getAttachments());
        $this->mailBody->addAttachment("/test/abc/test.png");
        $this->assertInstanceOf(MailBody::class, $this->mailBody);
    }

    public function testAddArrayAttachmentsShouldReturnMailBodyObject()
    {
        $this->assertCount(0, $this->mailBody->getAttachments());
        $this->mailBody->addAttachments(["/test/abc/test.png"]);
        $this->assertInstanceOf(MailBody::class, $this->mailBody);
    }

    public function testGetSubjectShouldReturnSubjectString()
    {
        $subject = "Test Subject";
        $message = "Test Message";
        $mailBody = new MailBody($subject, $message);
        $this->assertEquals($subject, $mailBody->getSubject());
    }

    public function testGetMessageShouldReturnMessageString()
    {
        $subject = "Test Subject";
        $message = "Test Message";
        $mailBody = new MailBody($subject, $message);

        $this->assertEquals($message, $mailBody->getMessage());
    }

    public function testGetViewShouldReturnViewString()
    {
        $subject = "Test Subject";
        $message = "Test Message";
        $mailBody = new MailBody($subject, $message);
        $this->assertEquals('admin-panel.common.mail', $mailBody->getView());
    }

    public function testAddCcEmailShouldAddEmailIntoCcEmailArray()
    {
        $subject = "Test Subject";
        $message = "Test Message";
        $ccEmail = "cc@example.com";
        $mailBody = new MailBody($subject, $message);

        $mailBody->addCcEmail($ccEmail);

        $this->assertEquals([MailBody::DEFAULT_CC_MAIL, $ccEmail], $mailBody->getCcEmails());
    }

    public function testAddEmailShouldAddEmailIntoEmailArray()
    {
        $subject = "Test Subject";
        $message = "Test Message";
        $email = "test@example.com";
        $mailBody = new MailBody($subject, $message);
        $mailBody->addEmail($email);

        $this->assertEquals([$email], $mailBody->getEmails());
    }

    public function testMailBodyGetData()
    {
        $subject = 'Test Subject';
        $message = 'Test Message';
        $mailBody = new MailBody($subject, $message);

        $data = $mailBody->getData();

        $this->assertIsArray($data);
        $this->assertEquals($subject, $data['subject']);
        $this->assertEquals($mailBody->getCcEmails(), $data['cc_emails']);
        $this->assertEquals([], $data['emails']);
        $this->assertEquals([], $data['attachments']);
        $this->assertEquals($message, $data['message']);
        $this->assertEquals('', $data['html_content']);
    }

    public function testGetTrackingHtmlContent()
    {

        /**
         * @var MockInterface $mailBody
         */
        $mailBody = Mockery::mock(MailBody::class);
        $mailBody->shouldReceive('getUniqueIdentifier')->andReturn('231123213ss');
        $mailBody->shouldReceive('getTrackingHtmlContent')->andReturn('<img src=' . url("/track/email/231123213ss/pixel.png") . ' width="1" height="1" />');
        $this->assertEquals('231123213ss', $mailBody->getUniqueIdentifier());
        $trackingHtml = $mailBody->getTrackingHtmlContent();
        $this->assertStringContainsString(route('track.email', ['unique_identifier' => "231123213ss"]), $trackingHtml);
        $this->assertStringContainsString("231123213ss", $trackingHtml);
    }
}
