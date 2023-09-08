<?php
namespace App\GenericNotification\Notification\Tests\Feature\Jobs;

use App\GenericNotification\Notification\Jobs\SmsJob;
use App\GenericNotification\Notification\Models\GenericNotification;
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

    public function testSendToQueueShouldUpdateStatusOfNotificationToInQueue()
    {
        $smsBody = new SmsBody("+917977796967", "Test Message");
        $sms = new GenericSms($smsBody);

        $genericNotification = new GenericNotification();
        $genericNotification->data = ['key' => 'value'];

        $smsJob = new SmsJob($sms);
        $smsJob->sendToQueue($genericNotification);

        $this->assertEquals(StatusType::IN_QUEUE, $genericNotification->status);
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

        $smsBody = Mockery::mock(new SmsBody("+91999999999", "test message"));
        $smsBody->shouldReceive('setStatus')->andReturn(StatusType::SENT);
        $smsBody->shouldReceive('setData')->twice();
        $smsBody->shouldReceive('setStatus');
        $smsBody->shouldReceive('getType');
        $smsBody->shouldReceive('getMedium');
        $smsBody->shouldReceive('getData');
        $smsServiceInterface->shouldReceive('getSmsBody')->andReturn($smsBody);
        $smsServiceInterface->shouldReceive('send');
        $smsServiceInterface->shouldReceive('getMessageUrl')->andReturn('http://example.com');
        $smsServiceInterface->shouldReceive('getServiceName')->andReturn('TestService');
        $genericSms->shouldReceive('loadSmsService')->andReturn($smsServiceInterface);
        $job = new SmsJob($genericSms);
        $this->assertInstanceOf(SmsJob::class, $job);
        $this->assertInstanceOf(SmsServiceInterface::class, $job->getSmsService());
        $this->assertInstanceOf(SmsBody::class, $job->getSmsBody());
    }


    public function testHandleMethodSendsSmsAndStoresInDatabase()
    {
        $smsBody = Mockery::mock(new SmsBody("+91999999999", "test message"));
        $smsBody->shouldReceive('setStatus')->andReturn(StatusType::IN_PROCESS);
        $smsBody->shouldReceive('setData');
        $smsBody->shouldReceive('setStatus');
        $smsBody->shouldReceive('getType');
        $smsBody->shouldReceive('getMedium');
        $smsBody->shouldReceive('getData')->andReturn(['key', 'value']);
        $sms = Mockery::mock(new GenericSms($smsBody));
        /**
         * @var MockInterface $smsService
         */
        $smsService = Mockery::mock(SmsServiceInterface::class);
        $smsService->shouldReceive('getSmsBody')->andReturn($smsBody);
        $smsService->shouldReceive('send');
        $smsService->shouldReceive('getMessageUrl')->andReturn('http://example.com');
        $smsService->shouldReceive('getServiceName')->andReturn('TestService');
        $sms->shouldReceive('loadSmsService')->andReturn($smsService);
        $job = new SmsJob($sms);
        $job->handle();
        $this->assertEquals(StatusType::IN_PROCESS, $smsBody->getStatus());
    }

    public function testSmsJobHandleExceptionAndStoresInDatabaseWithFailedStatus()
    {
        $smsBody = Mockery::mock(new SmsBody("+91999999999", "test message"));
        $smsBody->shouldReceive('setStatus');
        $smsBody->shouldReceive('setData');
        $smsBody->shouldReceive('setStatus');
        $smsBody->shouldReceive('getUniqueIdentifier', "232q3q3q2");
        $smsBody->shouldReceive('getType');
        $smsBody->shouldReceive('getMedium');
        $smsBody->shouldReceive('getData')->andReturn(['key', 'value']);
        $sms = Mockery::mock(new GenericSms($smsBody));
        $exception = new Exception("test exception");
        $smsService = Mockery::mock(SmsServiceInterface::class);

        /**
         * @var MockInterface $smsService
         */
        $smsService->shouldReceive('getSmsBody')->andReturn($smsBody);
        $smsService->shouldReceive('send')->andThrow($exception);
        $smsService->shouldReceive('getMessageUrl')->andReturn('http://example.com');
        $smsService->shouldReceive('getServiceName')->andReturn('TestService');
        $sms->shouldReceive('loadSmsService')->andReturn($smsService);
        $job = new SmsJob($sms);
        $this->expectException(Throwable::class);
        $job->handle();
        $this->assertEquals(StatusType::FAILED, $smsBody->getStatus());
    }

    public function testSetAdditionalData()
    {
        $smsBody = Mockery::mock(new SmsBody("+91999999999", "test message"));

        $smsService = Mockery::mock(SmsServiceInterface::class);
        /**
         * @var MockInterface $smsService
         */
        $smsService->shouldReceive('getMessageUrl')->andReturn('http://example.com');
        $smsService->shouldReceive('getServiceName')->andReturn('TestService');
        $smsService->shouldReceive('getSmsBody')->andReturn($smsBody);
        $smsService->shouldReceive('send');
        $sms = Mockery::mock(new GenericSms($smsBody));
        $sms->shouldReceive('loadSmsService')->andReturn($smsService);
        $job = new SmsJob($sms);
        $job->handle();
        $this->assertEquals('http://example.com', $job->getSmsBody()->getData()['sms_url']);
        $this->assertEquals('TestService', $job->getSmsBody()->getData()['sms_service_name']);
    }
}
