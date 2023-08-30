<?php

namespace App\GenericNotification\Notification\Services\Interfaces;


interface GenericNotifiableInterface
{
    public function getUniqueIdentifier(): string;

    public function getType(): int;

    public function getMedium(): int;

    // public function getEvent(): string;

    public function getData(): array;

    public function setData(string $key, mixed $value): void;

    public function getStatus(): int;

    public function setStatus(int $status);
}
