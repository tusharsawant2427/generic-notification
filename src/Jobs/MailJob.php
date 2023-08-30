<?php

namespace App\GenericNotification\Notification\Jobs;

use App\GenericNotification\Notification\Services\Constants\StatusType;
use App\GenericNotification\Notification\Services\GenericNotificationService;
use App\GenericNotification\Notification\Services\Mail\GenericMail;
use App\GenericNotification\Notification\Services\Mail\MailBody;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Throwable;

class MailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected Mailable $mailable;

    /**
     * @param Mailable $mailable
     */
    public function __construct(Mailable $mailable)
    {
        $this->mailable = $mailable;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $genericNotificationService = new GenericNotificationService();

        /**
         * @var GenericMail $mailable
         */
        $mailable = $this->mailable;

        /**
         * @var MailBody $mailBody
         */
        $mailBody = $mailable->getMailBody();

        /** It will give an html content */
        $mailBody->setHtmlContent($this->mailable->render());
        try {
            Mail::to($mailBody->getEmails())
                ->cc($mailBody->getCcEmails())
                ->queue($this->mailable);
            $genericNotificationService->store($mailBody);
        } catch (Throwable $ex) {

            $mailBody->setStatus(StatusType::FAILED);
            $genericNotificationService->store($mailBody);

            throw $ex;
        }
    }
}
