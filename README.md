# Generic Notification Package

## About

This package is designed to store notification data and track whether it has been delivered or not, as well as how many times the user has viewed the notification. It supports three types of notifications: Mail, SMS, and App.

## Installation

1. Install via Composer:

    ```shell
    composer require genericnotification/notification
    ```

2. Publish vendor files:

    Publish vendor files by running the following command:

    ```shell
    php artisan vendor:publish --tag=generic-notification-config
    ```

3. Update the application service provider:

    Add the following path to your `app.php` file in the `providers` array:

    ```php
    App\GenericNotification\Notification\GenericNotificationServiceProvider::class
    ```

## How to Use

### Mail Notification

```php
use App\GenericNotification\Notification\Jobs\MailJob;
use App\GenericNotification\Notification\Services\Mail\GenericMail;
use App\GenericNotification\Notification\Services\Mail\MailBody;

$mailBody = new MailBody(subject: "Subject", message: "Message");
$mailBody->addEmail("test@example.com");
$mailBody->addAttachment("abc.png");
$mailBody->addCcEmail("test@example.com");

MailJob::dispatch(new GenericMail($mailBody));

```
If you want to track whether the email has been opened and how many times it has been opened, 
You need to add tracking code to your email view body `mail.blade.php`

```php
{!! html_entity_decode($tracking_code ?? '') !!}
```

### SMS Notification
```php
use App\GenericNotification\Notification\Jobs\SmsJob;
use App\GenericNotification\Notification\Services\Sms\GenericSms;
use App\GenericNotification\Notification\Services\Sms\SmsBody;

$message = "Sms text";
SmsJob::dispatch(new GenericSms(new SmsBody(phoneNumber: "+9199999999999", message: $message)));
```

You can also send SMS for specific service providers. Currently SMS service is available only for the *24x7* provider.


Available Methods
    `App\GenericNotification\Notification\Services\Mail\MailBody`

            Methods:

            getEmails(): array;
            addEmail(string $email);
            getAttachments(): array;
            addAttachment(string $attachment);
            addAttachments(array $attachments);
            getCcEmails(): array;
            addCcEmail(string $email);
            getSubject(): string;
            getView(): string;
            getMessage();
            getHtmlContent(): string;
            setHtmlContent(string $htmlContent):void;
            getTrackingHtmlContent(): string;
            getUniqueIdentifier(): string;
            getType(): int;
            getMedium(): int;
            getData(): array;
            setData(string $key, mixed $value): void;
            getStatus(): int;
            setStatus(int $status);

`App\GenericNotification\Notification\Services\Sms\SmsBody`:

        Methods:
            getPhoneNumber(): string;
            getMessage(): string;
            getSmsProvider(): string;
            getUniqueIdentifier(): string;
            getType(): int;
            getMedium(): int;
            getData(): array;
            setData(string $key, mixed $value): void;
            getStatus(): int;
            setStatus(int $status);
