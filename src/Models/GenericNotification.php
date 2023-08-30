<?php

namespace App\GenericNotification\Notification\Models;

use App\GenericNotification\Notification\Models\Constants\GenericNotificationConstant;
use App\GenericNotification\Notification\Services\Constants\StatusType;
use App\Helpers\Services\Utils;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class GenericNotification extends Model implements GenericNotificationConstant
{
    protected $fillable = ['identifier', 'type', 'medium', 'event', 'data', 'sent_at', 'status', 'created_by', 'description', 'opened_at'];

    /**
     * getCreateValidationRules
     *
     * @return array<string,string>
     */
    public static function getCreateValidationRules(): array {
        return self::CREATE_RULE;
    }

    /**
     * persistCreateGenericNotification
     *
     * @param  array<string,mixed> $genericNotificationData
     * @return GenericNotification
     */
    public static function persistCreateGenericNotification(array $genericNotificationData): GenericNotification
    {
        Utils::validateOrThrow(self::getCreateValidationRules(), $genericNotificationData);
        return GenericNotification::create($genericNotificationData);
    }

    /**
     * updateOpenAt
     *
     * @return bool
     */
    public function updateOpenAtAndOpenStatus(): bool
    {
        return $this->update([
            'opened_at' => Carbon::now(),
            'status' => StatusType::OPEN
        ]);
    }

    /**
     * incrementOpenCount
     *
     * @return bool
     */
    public function incrementOpenCount(): bool
    {
        return $this->update([
            'open_count' => $this->increment('open_count')
        ]);
    }
}
