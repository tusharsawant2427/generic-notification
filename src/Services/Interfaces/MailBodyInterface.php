<?php

namespace App\GenericNotification\Notification\Services\Interfaces;

interface MailBodyInterface extends GenericNotifiableInterface
{
    public function getEmails(): array;
    public function addEmail(string $email);
    public function getAttachments(): array;
    public function addAttachment(string $attachment);
    public function addAttachments(array $attachments);
    public function getCcEmails(): array;
    public function addCcEmail(string $email);
    public function getSubject(): string;
    public function getView(): string;
    public function getMessage();
    public function getHtmlContent(): string;
    public function setHtmlContent(string $htmlContent):void;
    public function getTrackingHtmlContent(): string;
}
