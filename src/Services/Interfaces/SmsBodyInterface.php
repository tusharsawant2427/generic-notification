<?php

namespace App\GenericNotification\Notification\Services\Interfaces;

interface SmsBodyInterface extends GenericNotifiableInterface
{
    public function getPhoneNumber(): string;
    public function getMessage(): string;
    public function getSmsProvider(): string;
}
