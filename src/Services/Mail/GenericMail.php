<?php

namespace App\GenericNotification\Notification\Services\Mail;

use App\GenericNotification\Notification\Services\Interfaces\MailBodyInterface;
use Illuminate\Mail\Mailable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;

class GenericMail extends Mailable
{
    use Queueable, SerializesModels;
    private MailBodyInterface $mailBody;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(MailBodyInterface $mailBody)
    {
        $this->mailBody = $mailBody;
    }

    /**
     * Build the message.
     *
     * @return Mailable
     */
    public function build(): Mailable
    {
        $email = $this->subject($this->mailBody->getSubject())
            ->markdown($this->mailBody->getView())
            ->with('body', $this->mailBody->getMessage())
            ->with('tracking_code', $this->mailBody->getTrackingHtmlContent());

        $attachments = $this->mailBody->getAttachments();
        if (!empty($attachments)) {
            foreach ($attachments as $attachment) {
                $email->attach($attachment);
            }
        }

        return $email;
    }

    /**
     * @return MailBody
     */
    public function getMailBody(): MailBody
    {
        return $this->mailBody;
    }
}
