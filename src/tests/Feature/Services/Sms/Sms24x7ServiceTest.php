<?php
namespace App\GenericNotification\Notification\Services\Sms;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

class Sms24x7ServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testSms24x7ServiceConstructShouldSetSmsBody()
    {
         $smsBody = new SmsBody('+917796967', 'Test message');
         $smsService = new Sms24x7Service($smsBody);

         $this->assertInstanceOf(Sms24x7Service::class, $smsService);
         $this->assertSame($smsBody, $smsService->getSmsBody());
    }


    public function testGetApiKeyShouldReturnString()
    {
        Config::shouldReceive('get')->with('gn24x7sms.key')->andReturn('test_api_key');
        $smsService = new Sms24x7Service(new SmsBody('+917796967', 'Test message'));

        $this->assertEquals('test_api_key', $smsService->getApiKey());
    }

    public function testGetMessageUrlShouldReturnString()
    {
        Config::shouldReceive('get')->with('gn24x7sms.key')->andReturn('test_api_key');
        Config::shouldReceive('get')->with('gn24x7sms.sender_id')->andReturn('test_sender_id');
        Config::shouldReceive('get')->with('gn24x7sms.service_name')->andReturn('test_service_name');
        Config::shouldReceive('get')->with('gn24x7sms.url')->andReturn('https://example.com/sms');

        $smsBody = new SmsBody('+917796967', 'Test message');
        $smsService = new Sms24x7Service($smsBody);

        $url = $smsService->getMessageUrl();

        $expectedUrl = 'https://example.com/sms?APIKEY=test_api_key&MobileNo=+917796967&SenderID=test_sender_id&Message=Test message&ServiceName=test_service_name';
        $this->assertEquals($expectedUrl, $url);
    }
    public function testSendSuccessShouldReturnTrue()
    {
        /**
         * @var MockObject $smsBody
         */
         $smsBody = $this->createMock(SmsBody::class);
         $smsBody->method('getPhoneNumber')->willReturn('+917796967');
         $smsBody->method('getMessage')->willReturn("1212");

         Http::fake([
             '*' => Http::response('Sms send successfully', 200),
         ]);
         $smsService = new Sms24x7Service($smsBody);
         $result = $smsService->send();
         $this->assertTrue($result);
    }

    public function testSendFailedShouldReturnFalse()
    {
        /**
         * @var MockObject $smsBody
         */
         $smsBody = $this->createMock(SmsBody::class);
         $smsBody->method('getPhoneNumber')->willReturn('+917796967');
         $smsBody->method('getMessage')->willReturn("1212");

         Http::fake([
             '*' => Http::response('Sms send failed', 301),
         ]);

         $smsService = new Sms24x7Service($smsBody);
         $result = $smsService->send();
         $this->assertFalse($result);
    }

    public function testSendExceptionShouldReturnFalse()
    {
        Config::shouldReceive('get')->with('gn24x7sms.key')->andReturn('test_api_key');
        Config::shouldReceive('get')->with('gn24x7sms.sender_id')->andReturn('test_sender_id');
        Config::shouldReceive('get')->with('gn24x7sms.service_name')->andReturn('test_service_name');
        Config::shouldReceive('get')->with('gn24x7sms.url')->andReturn('https://example.com/sms');
        Http::shouldReceive('get')->andThrow(new \Exception('Test exception'));

        $smsBody = new SmsBody('+917796967', 'Test message');
        $smsService = new Sms24x7Service($smsBody);

        $result = $smsService->send();

        $this->assertFalse($result);
    }

}
