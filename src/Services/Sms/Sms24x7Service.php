<?php

namespace App\GenericNotification\Notification\Services\Sms;

use App\GenericNotification\Notification\Services\Interfaces\SmsServiceInterface;
use Exception;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class Sms24x7Service implements SmsServiceInterface
{
    public SmsBody $smsBody;

    public const SMS24X7_URL_FORMAT = "%s?APIKEY=%s&MobileNo=%s&SenderID=%s&Message=%s&ServiceName=%s";

    /**
     * __construct
     *
     * @param  SmsBody $smsBody
     * @return void
     */
    public function __construct(SmsBody $smsBody)
    {
        $this->smsBody = $smsBody;
    }

    /**
     * getApiKey
     *
     * @return string
     */
    public function getApiKey(): string
    {
        /**
         * @var string $key
         */

        $key = Config::get('gn24x7sms.key');

        return $key;
    }

    /**
     * getSenderId
     *
     * @return string
     */
    public function getSenderId(): string
    {
        /**
         * @var string $senderId
         */

        $senderId = Config::get('gn24x7sms.sender_id');

        return $senderId;
    }

    /**
     * getServiceName
     *
     * @return string
     */
    public function getServiceName(): string
    {
        /**
         * @var string $serviceName
         */

        $serviceName = Config::get('gn24x7sms.service_name');

        return $serviceName;
    }


    /**
     * getUrl
     *
     * @return string
     */
    public function getUrl(): string
    {
        /**
         * @var string $url
         */

        $url = Config::get('gn24x7sms.url');

        return $url;
    }


    /**
     * getMessageUrl
     *
     * @return string
     */
    public function getMessageUrl(): string
    {
        return sprintf(self::SMS24X7_URL_FORMAT, $this->getUrl(), $this->getApiKey(), $this->getSmsBody()->getPhoneNumber(), $this->getSenderId(), $this->getSmsBody()->getMessage(), $this->getServiceName());
    }

    /**
     * @return SmsBody
     */
    public function getSmsBody(): SmsBody
    {
        return $this->smsBody;
    }

    /**
     * send
     *
     * @return bool
     */
    public function send(): bool
    {
        $url = $this->getMessageUrl();
        try {
            $response = Http::get($url);

            if (!$response->successful()) {
                error_log("HTTP Error: " . $response->status());
                return false;
            }

            error_log($response->body());
            return true;
        } catch (\Exception $e) {
            error_log("Exception: " . $e->getMessage());
            return false;
        }
    }
}
