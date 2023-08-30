<?php

namespace App\GenericNotification\Notification\Jobs;

use App\GenericNotification\Notification\Services\Constants\StatusType;
use App\GenericNotification\Notification\Services\GenericNotificationService;
use App\GenericNotification\Notification\Services\Interfaces\SmsBodyInterface;
use App\GenericNotification\Notification\Services\Interfaces\SmsServiceInterface;
use App\GenericNotification\Notification\Services\Sms\GenericSms;
use App\GenericNotification\Notification\Services\Sms\SmsBody;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class SmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const URL_KEY = "sms_url";
    const SMS_SERVICE_NAME_KEY = "sms_service_name";
    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected GenericSms $sms;
    protected SmsServiceInterface $smsService;
    protected SmsBodyInterface $smsBody;

    /**
     * @param GenericSms $sms
     */
    public function __construct(GenericSms $sms)
    {
        $this->sms = $sms;
        $this->smsService = $sms->loadSmsService();
        $this->smsBody = $this->smsService->getSmsBody();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $genericNotificationService = new GenericNotificationService();
        /**
         * @var SmsServiceInterface $smsService
         */
        $smsService = $this->getSmsService();
        $this->setAdditionalData();
        try {

            $smsService->send();
            $genericNotificationService->store($this->getSmsBody());
        } catch (Throwable $ex) {

            $this->getSmsBody()->setStatus(StatusType::FAILED);
            $genericNotificationService->store($this->getSmsBody());
            throw $ex;
        }
    }

    /**
     * @return GenericSms
     */
    public function getSms(): GenericSms
    {
        return $this->sms;
    }


    /**
     * @return SmsServiceInterface
     */
    public function getSmsService(): SmsServiceInterface
    {
        return $this->smsService;
    }

    /**
     * @return SmsBodyInterface
     */
    public function getSmsBody(): SmsBodyInterface
    {
        return $this->smsBody;
    }

    /**
     * setAdditionalData
     *
     * @return void
     */
    public function setAdditionalData()
    {
        $messageUrl = ($this->getSmsService()->getMessageUrl()) ? $this->getSmsService()->getMessageUrl() : '';
        $serviceName = ($this->getSmsService()->getServiceName()) ? $this->getSmsService()->getServiceName() : '';

        $this->getSmsBody()->setData(key: self::URL_KEY, value: $messageUrl);
        $this->getSmsBody()->setData(key: self::SMS_SERVICE_NAME_KEY, value: $serviceName);
    }
}
