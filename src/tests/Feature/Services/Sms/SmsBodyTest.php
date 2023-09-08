<?php
namespace App\GenericNotification\Notification\Tests\Feature\Services\Sms;

use App\GenericNotification\Notification\Services\Constants\GenericNotificationType;
use App\GenericNotification\Notification\Services\Constants\MediumType;
use App\GenericNotification\Notification\Services\Constants\SmsServiceConstant;
use App\GenericNotification\Notification\Services\Constants\StatusType;
use App\GenericNotification\Notification\Services\Sms\SmsBody;
use Tests\TestCase;

class SmsBodyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testConstructorShouldSetsPhoneNumberAndMessage()
    {
        $phoneNumber = '+917796967';
        $message = 'Test message';
        $smsBody = new SmsBody($phoneNumber, $message);

        $this->assertEquals($phoneNumber, $smsBody->getPhoneNumber());
        $this->assertEquals($message, $smsBody->getMessage());
        $this->assertEquals(SmsServiceConstant::SMS_24X7_PROVIDER, $smsBody->getSmsProvider());
        $this->assertEquals(GenericNotificationType::GENERAL, $smsBody->getType());
        $this->assertEquals(StatusType::IN_PROCESS, $smsBody->getStatus());
        $this->assertEquals(MediumType::SMS, $smsBody->getMedium());
        $this->assertIsArray($smsBody->getData());
        $this->assertNotEmpty($smsBody->getUniqueIdentifier());

    }

    public function testConstructorWithCustomValuesShouldSetsNotificationType()
    {
        $phoneNumber = '+917796967';
        $message = 'Test message';
        $type = GenericNotificationType::NEWS;

        $smsBody = new SmsBody($phoneNumber, $message, $type);

        $this->assertEquals($phoneNumber, $smsBody->getPhoneNumber());
        $this->assertEquals($message, $smsBody->getMessage());
        $this->assertEquals(SmsServiceConstant::SMS_24X7_PROVIDER, $smsBody->getSmsProvider());
        $this->assertEquals($type, $smsBody->getType());
        $this->assertEquals(StatusType::IN_PROCESS, $smsBody->getStatus());
        $this->assertEquals(MediumType::SMS, $smsBody->getMedium());
        $this->assertIsArray($smsBody->getData());
        $this->assertNotEmpty($smsBody->getUniqueIdentifier());
    }

    public function testDefaultSmsProvider()
    {
        $smsBody = new SmsBody('+917796967', 'Test message');
        $this->assertSame(SmsServiceConstant::SMS_24X7_PROVIDER, $smsBody->getSmsProvider());
    }

    public function testSetSmsProvider()
    {
        $smsBody = new SmsBody('+917796967', 'Test message');
        $smsBody->setSmsProvider('CustomProvider');
        $this->assertSame('CustomProvider', $smsBody->getSmsProvider());
    }

    public function testGetDataShouldReturnArray()
    {
        $phoneNumber = '+917796967';
        $message = 'Test message';
        $smsBody = new SmsBody($phoneNumber, $message);

        $data = $smsBody->getData();

        $this->assertIsArray($data);
        $this->assertEquals($phoneNumber, $data['phone_number']);
        $this->assertEquals($message, $data['message']);
        $this->assertEquals(SmsServiceConstant::SMS_24X7_PROVIDER, $data['sms_provider']);
    }

    public function testGetUniqueIdentifierShouldReturnString()
    {
        $smsBody = new SmsBody('+917796967', 'Test message');

        $uniqueIdentifier = $smsBody->getUniqueIdentifier();

        $this->assertNotEmpty($uniqueIdentifier);
        $this->assertIsString($uniqueIdentifier);
    }

}
