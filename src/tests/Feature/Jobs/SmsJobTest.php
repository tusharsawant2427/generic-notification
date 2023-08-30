<?php
namespace App\GenericNotification\Notification\Tests\Feature\Jobs;

use App\GenericNotification\Notification\Jobs\SmsJob;
use App\GenericNotification\Notification\Services\Constants\StatusType;
use App\GenericNotification\Notification\Services\Interfaces\SmsServiceInterface;
use App\GenericNotification\Notification\Services\Sms\GenericSms;
use App\GenericNotification\Notification\Services\Sms\SmsBody;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;
use Throwable;

class SmsJobTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testConstructorShouldSetsSmsServiceSmsBody()
    {
        Bus::fake();

        /**
         * @var MockInterface $genericSms
         */
        $genericSms = Mockery::mock(GenericSms::class);

        /**
         * @var MockInterface $smsServiceInterface
         */
        $smsServiceInterface = Mockery::mock(SmsServiceInterface::class);
        $smsServiceInterface->shouldReceive('getSmsBody')->andReturn(Mockery::mock(new SmsBody('+91799997', 'Test message')));
        $genericSms->shouldReceive('loadSmsService')->andReturn($smsServiceInterface);
        $job = new SmsJob($genericSms);
        $this->assertInstanceOf(SmsJob::class, $job);
        $this->assertInstanceOf(SmsServiceInterface::class, $job->getSmsService());
        $this->assertInstanceOf(SmsBody::class, $job->getSmsBody());
    }


    public function testHandleMethodSendsSmsAndStoresInDatabase()
    {
        $smsBody = Mockery::mock(new SmsBody("+91999999999", "test message"));
        $smsBody->shouldReceive('setStatus')->andReturn(StatusType::SENT);
        $smsBody->shouldReceive('setData')->twice();
        $smsBody->shouldReceive('setStatus');
        $smsBody->shouldReceive('getType');
        $smsBody->shouldReceive('getMedium');
        $smsBody->shouldReceive('getData');
        $sms = Mockery::mock(new GenericSms($smsBody));
        $genericNotificationService = Mockery::mock(GenericNotificationService::class);
        $smsService = Mockery::mock(SmsServiceInterface::class);
        $smsService->shouldReceive('getSmsBody')->andReturn($smsBody);
        $smsService->shouldReceive('send');
        $smsService->shouldReceive('getMessageUrl')->andReturn('http://example.com');
        $smsService->shouldReceive('getServiceName')->andReturn('TestService');
        $sms->shouldReceive('loadSmsService')->andReturn($smsService);

        $genericNotificationService->shouldReceive('store');
        $job = new SmsJob($sms);
        $job->handle();
        $this->assertEquals(StatusType::SENT, $smsBody->getStatus());
    }

    public function testSmsJobHandleExceptionAndStoresInDatabaseWithFailedStatus()
    {
        $smsBody = Mockery::mock(new SmsBody("+91999999999", "test message"));
        $smsBody->shouldReceive('setStatus')->once()->with(StatusType::FAILED);
        $smsBody->shouldReceive('setData')->twice();
        $smsBody->shouldReceive('setStatus');
        $smsBody->shouldReceive('getUniqueIdentifier', "232q3q3q2");
        $smsBody->shouldReceive('getType');
        $smsBody->shouldReceive('getMedium');
        $smsBody->shouldReceive('getData');
        $sms = Mockery::mock(new GenericSms($smsBody));
        $genericNotificationService = Mockery::mock(GenericNotificationService::class);
        $exception = new Exception("test exception");
        $smsService = Mockery::mock(SmsServiceInterface::class);
        $smsService->shouldReceive('getSmsBody')->andReturn($smsBody);
        $smsService->shouldReceive('send')->andThrow($exception);
        $smsService->shouldReceive('getMessageUrl')->andReturn('http://example.com');
        $smsService->shouldReceive('getServiceName')->andReturn('TestService');
        $sms->shouldReceive('loadSmsService')->andReturn($smsService);

        $genericNotificationService->shouldReceive('store');
        $job = new SmsJob($sms);
        $this->expectException(Throwable::class);
        $job->handle();
        $this->assertEquals(StatusType::FAILED, $smsBody->getStatus());

    }


    public function testSmsJobsGetter()
    {
        $sms = new GenericSms(new SmsBody("+91999999999", "test message"));
        $job = new SmsJob($sms);
        $this->assertInstanceOf(GenericSms::class, $job->getSms());
        $this->assertInstanceOf(SmsServiceInterface::class, $job->getSmsService());
        $this->assertInstanceOf(SmsBody::class, $job->getSmsBody());
    }

    public function testSetAdditionalData()
    {
        $smsBody = Mockery::mock(new SmsBody("+91999999999", "test message"));

        $smsService = Mockery::mock(SmsServiceInterface::class);
        $smsService->shouldReceive('getMessageUrl')->andReturn('http://example.com');
        $smsService->shouldReceive('getServiceName')->andReturn('TestService');
        $smsService->shouldReceive('getSmsBody')->andReturn($smsBody);
        $sms = Mockery::mock(new GenericSms($smsBody));
        $sms->shouldReceive('loadSmsService')->andReturn($smsService);
        $job = new SmsJob($sms);
        $job->setAdditionalData();
        $this->assertEquals('http://example.com', $job->getSmsBody()->getData()['sms_url']);
        $this->assertEquals('TestService', $job->getSmsBody()->getData()['sms_service_name']);
    }
}
