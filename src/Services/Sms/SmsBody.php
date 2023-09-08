<?php

namespace App\GenericNotification\Notification\Services\Sms;

use App\GenericNotification\Notification\Services\Constants\GenericNotificationType;
use App\GenericNotification\Notification\Services\Constants\MediumType;
use App\GenericNotification\Notification\Services\Constants\SmsServiceConstant;
use App\GenericNotification\Notification\Services\Constants\StatusType;
use App\GenericNotification\Notification\Services\Interfaces\SmsBodyInterface;

class SmsBody implements SmsBodyInterface
{
    public string $phoneNumber;
    public string $message;
    public string $smsProvider = SmsServiceConstant::SMS_24X7_PROVIDER;
    public int $type;
    public int $status = StatusType::IN_PROCESS;
    public int $medium = MediumType::SMS;

    /**
     * @var array<string,mixed>
     */
    public array $data;

    public string $identifier;

    /**
     * __construct
     *
     * @param  string $phoneNumber
     * @param  string $message
     * @param  int $type
     * @return void
     */
    public function __construct(string $phoneNumber, string $message, int $type = GenericNotificationType::GENERAL)
    {
        $this->message = $message;
        $this->phoneNumber = $phoneNumber;
        $this->type = $type;
        $this->identifier = \Ramsey\Uuid\Uuid::uuid4()->toString();
        $this->medium = MediumType::SMS;
    }

    /**
     * getPhoneNumber
     *
     * @return string
     */
    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    /**
     * getMessage
     *
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }


    /**
     * getSmsProvider
     *
     * @return string
     */
    public function getSmsProvider(): string
    {
        return $this->smsProvider;
    }


    /**
     * setSmsProvider
     *
     * @param  string $smsProvider
     * @return void
     */
    public function setSmsProvider(string $smsProvider): void
    {
        $this->smsProvider = $smsProvider;
    }

    /**
     * setType
     *
     * @param int $type
     * @return void
     */
    public function setType(int $type): void
    {
        $this->type = $type;
    }

    /**
     * getType
     *
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * getData
     *
     * @return array<string,mixed>
     */
    public function getData(): array
    {
        $this->data['phone_number'] = $this->getPhoneNumber();
        $this->data['message'] = $this->getMessage();
        $this->data['sms_provider'] = $this->getSmsProvider();

        return $this->data;
    }

    /**
     * getStatus
     *
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * getMedium
     *
     * @return int
     */
    public function getMedium(): int
    {
        return $this->medium;
    }

    /**
     * setStatus
     *
     * @param  int $status
     * @return void
     */
    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    /**
     * @param int $medium
     */
    public function setMedium(int $medium): void
    {
        $this->medium = $medium;
    }


    /**
     * setData
     *
     * @param  string $key
     * @param  mixed $value
     * @return void
     */
    public function setData(string $key, mixed $value): void
    {
        $this->data[$key] = $value;
    }

    /**
     * getUniqueIdentifier
     *
     * @return string
     */
    public function getUniqueIdentifier(): string
    {
        return $this->identifier;
    }
}
