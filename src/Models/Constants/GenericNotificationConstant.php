<?php
namespace App\GenericNotification\Notification\Models\Constants;

interface GenericNotificationConstant
{
    public const CREATE_RULE = [
        "identifier" => 'required',
        "type" => 'required|integer',
        "medium"=>'required|integer',
        "data" => 'required',
        'sent_at'=>'required',
    ];
}
