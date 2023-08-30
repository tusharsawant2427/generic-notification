<?php

namespace App\GenericNotification\Notification\Services\Mail;

use App\GenericNotification\Notification\Services\Constants\GenericNotificationType;
use App\GenericNotification\Notification\Services\Constants\MediumType;
use App\GenericNotification\Notification\Services\Constants\StatusType;
use App\GenericNotification\Notification\Services\Interfaces\MailBodyInterface;

class MailBody implements MailBodyInterface
{

    public const DEFAULT_CC_MAIL = "notifications@quillplus.in";
    public const TRACKING_HTML_TAG = "<img src='%s' width='%s' height='%s' />";

    private string $subject;
    private string $message;
    private string $view = 'admin-panel.common.mail';

    /**
     * @var array<string> $attachments
     */
    private array $attachments = [];

    /**
     * @var array<string> $emails
     */
    private array $emails = [];

    /**
     * @var array<string> $ccEmails
     */
    private array $ccEmails = [self::DEFAULT_CC_MAIL];

    public int $type;

    public int $status = StatusType::SENT;

    public int $medium = MediumType::MAIL;

    /**
     * @var array<string,mixed> $data
     */
    public array $data;

    public string $htmlContent = "";

    public string $identifier;

    /**
     * __construct
     *
     * @param  string $subject
     * @param  string $message
     * @param  int $type
     * @return void
     */
    public function __construct(string $subject, string $message, int $type = GenericNotificationType::GENERAL)
    {
        $this->subject = $subject;
        $this->message = $message;
        $this->type = $type;
        $this->identifier = \Ramsey\Uuid\Uuid::uuid4()->toString();
    }

    /**
     * @return array<string>
     */
    public function getAttachments(): array
    {
        return $this->attachments;
    }

    /**
     * @param string $attachment
     * @return MailBody
     */
    public function addAttachment(string $attachment): MailBody
    {
        array_push($this->attachments, $attachment);
        return $this;
    }

    /**
     * @param array<string> $attachments
     * @return MailBody
     */
    public function addAttachments(array $attachments): MailBody
    {
        $this->attachments = array_merge($this->attachments, $attachments);
        return $this;
    }

    /**
     * @return array<string>
     */
    public function getCcEmails(): array
    {
        return array_unique($this->ccEmails);
    }

    /**
     * @param string $ccEmail
     * @return MailBody
     */
    public function addCcEmail(string $ccEmail): MailBody
    {
        array_push($this->ccEmails, $ccEmail);
        return $this;
    }

    /**
     * @return array<string>
     */
    public function getEmails(): array
    {
        return array_unique($this->emails);
    }

    /**
     * @param string $email
     * @return MailBody
     */
    public function addEmail(string $email): MailBody
    {
        array_push($this->emails, $email);
        return $this;
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public function getView(): string
    {
        return $this->view;
    }


    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @param int $type
     */
    public function setType(int $type): void
    {
        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getMedium(): int
    {
        return $this->medium;
    }

    /**
     * @param int $medium
     */
    public function setMedium(int $medium): void
    {
        $this->medium = $medium;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    /**
     * @return array<string,mixed>
     */
    public function getData(): array
    {
        $this->data['subject'] = $this->getSubject();
        $this->data['emails'] = $this->getEmails();
        $this->data['cc_emails'] = $this->getCcEmails();
        $this->data['attachments'] = $this->getAttachments();
        $this->data['message'] = $this->getMessage();
        $this->data['html_content'] = $this->getHtmlContent();

        return $this->data;
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
     * @return string
     */
    public function getHtmlContent(): string
    {
        return $this->htmlContent;
    }

    /**
     * @param string $htmlContent
     */
    public function setHtmlContent(string $htmlContent): void
    {
        $this->htmlContent = $htmlContent;
    }

    public function getUniqueIdentifier(): string
    {
        return $this->identifier;
    }

    public function getTrackingHtmlContent(): string
    {
        $trackingUrl = route('track.email', ['unique_identifier' => $this->getUniqueIdentifier()]);
        return sprintf(self::TRACKING_HTML_TAG, $trackingUrl, "1", "1");
    }
}
