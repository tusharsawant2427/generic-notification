<?php

return [

    'url' => env('SMS_24X7_BASE_URL', 'http://localhost'),

    'key' => env('SMS_24X7_API_KEY', ''),

    'sender_id' => env('SMS_24X7_SENDER_ID', ''),

    'service_name' => env('SMS_24X7_SERVICE_NAME', ''),

    'queue' => env('SMS_24X7_SERVICE_NAME', true)
];
