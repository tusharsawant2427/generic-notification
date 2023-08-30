<?php
namespace App\GenericNotification\Notification\Tests\Feature\Services\Sms;

use App\GenericNotification\Notification\Services\Constants\SmsServiceConstant;
use App\GenericNotification\Notification\Services\Exceptions\UnSupportedSmsProvider;
use App\GenericNotification\Notification\Services\Sms\GenericSms;
use App\GenericNotification\Notification\Services\Sms\Sms24x7Service;
use App\GenericNotification\Notification\Services\Sms\SmsBody;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

class GenericSmsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testConstructShouldSetSmsBody()
    {
        $smsBodyMock =  $this->createMock(SmsBody::class);
        $sms = new GenericSms($smsBodyMock);
        $this->assertSame($smsBodyMock, $sms->getSmsBody());
    }

    public function testLoadSmsServiceWithSupportedProvider()
    {
        /**
         * @var MockObject $smsBodyMock
         */
        $smsBodyMock = $this->createMock(SmsBody::class);
        $smsBodyMock->method('getSmsProvider')->willReturn(SmsServiceConstant::SMS_24X7_PROVIDER);
        $smsBody = $smsBodyMock;
        $sms = new GenericSms($smsBody);
        $smsService = $sms->loadSmsService();
        $this->assertInstanceOf(Sms24x7Service::class, $smsService);

    }

    public function testLoadSmsProviderWithUnsupportedProvider()
    {
        /**
         * @var MockObject $smsBodyMock
         */
        $smsBodyMock = $this->createMock(SmsBody::class);
        $smsBodyMock->method('getSmsProvider')->willReturn("unsupported provider");
        $sms = new GenericSms($smsBodyMock);
        $this->expectException(UnSupportedSmsProvider::class);
        $this->expectExceptionMessage("Unsupported SMS provider...");
        $sms->loadSmsService();
    }
}
