<?php
namespace App\GenericNotification\Notification\Services\Interfaces;

interface Sms24x7Interface extends SmsServiceInterface
{
    public function getApiKey(): string;

    public function getSenderId(): string;

    public function getServiceName(): string;

    public function getUrl(): string;

    public function buildUrl(): string;

    public function send(): bool;
}
