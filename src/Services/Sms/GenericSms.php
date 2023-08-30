<?php

namespace App\GenericNotification\Notification\Services\Sms;

use App\GenericNotification\Notification\Services\Constants\SmsServiceConstant;
use App\GenericNotification\Notification\Services\Exceptions\UnSupportedSmsProvider;
use App\GenericNotification\Notification\Services\Interfaces\SmsBodyInterface;
use App\GenericNotification\Notification\Services\Interfaces\SmsServiceInterface;

class GenericSms
{
    public SmsBodyInterface $smsBody;

    /**
     * __construct
     *
     * @param  SmsBodyInterface $smsBody
     * @return void
     */
    public function __construct(SmsBodyInterface $smsBody)
    {
        $this->smsBody = $smsBody;
    }

    /**
     * loadSmsService
     *
     * @return SmsServiceInterface
     * @throws UnSupportedSmsProvider
     */
    public function loadSmsService(): SmsServiceInterface
    {
        if ($this->getSmsBody()->getSmsProvider() == SmsServiceConstant::SMS_24X7_PROVIDER) {
            return new Sms24x7Service($this->getSmsBody());
        }

        throw new UnSupportedSmsProvider();
    }

    /**
     * @return SmsBodyInterface
     */
    public function getSmsBody(): SmsBodyInterface
    {
        return $this->smsBody;
    }
}
