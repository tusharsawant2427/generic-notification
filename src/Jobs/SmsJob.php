<?php

namespace App\GenericNotification\Notification\Jobs;

use App\GenericNotification\Notification\Jobs\NotificationJob;
use App\GenericNotification\Notification\Models\GenericNotification;
use App\GenericNotification\Notification\Services\Constants\StatusType;
use App\GenericNotification\Notification\Services\Interfaces\GenericNotifiableInterface;
use App\GenericNotification\Notification\Services\Interfaces\SmsBodyInterface;
use App\GenericNotification\Notification\Services\Interfaces\SmsServiceInterface;
use App\GenericNotification\Notification\Services\Sms\GenericSms;
use Illuminate\Support\Facades\Log;

class SmsJob extends NotificationJob
{
    const JOB_UUID_KEY = "job_uuid";
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
        $this->setAdditionalDataToSmsBody();
    }


    /**
     * sendToQueue
     *
     * @return void
     */
    public function sendToQueue(GenericNotification $genericNotification)
    {
        /**
         * @var SmsServiceInterface $smsService
         */
        $smsService = $this->getSmsService();


        $smsService->send();

        $genericNotification->updateStatus(StatusType::IN_QUEUE);

        Log::info("GenericNotification Notification Job Status: " . $genericNotification->status);
    }

    /**
     * getNotificationBody
     *
     * @return GenericNotifiableInterface
     */
    protected function getNotificationBody(): GenericNotifiableInterface
    {
        $this->getSmsBody()->setData(key: self::JOB_UUID_KEY, value: $this->getUuid());
        return $this->getSmsBody();
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
     * setAdditionalDataToSmsBody
     *
     * @return void
     */
    private function setAdditionalDataToSmsBody()
    {
        $messageUrl = ($this->getSmsService()->getMessageUrl()) ? $this->getSmsService()->getMessageUrl() : '';
        $serviceName = ($this->getSmsService()->getServiceName()) ? $this->getSmsService()->getServiceName() : '';

        $this->getSmsBody()->setData(key: self::URL_KEY, value: $messageUrl);
        $this->getSmsBody()->setData(key: self::SMS_SERVICE_NAME_KEY, value: $serviceName);
    }
}
