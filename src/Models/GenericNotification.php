<?php

namespace App\GenericNotification\Notification\Models;

use App\GenericNotification\Notification\Models\Constants\GenericNotificationConstant;
use App\GenericNotification\Notification\Services\Constants\StatusType;
use App\Helpers\Services\Utils;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;

class GenericNotification extends Model implements \OwenIt\Auditing\Contracts\Auditable, GenericNotificationConstant
{
    use Auditable;

    protected $fillable = ['identifier', 'type', 'medium', 'event', 'data', 'sent_at', 'status', 'created_by', 'description', 'opened_at'];

    protected $casts =[
        'data' => 'array'
    ];
    /**
     * getCreateValidationRules
     *
     * @return array<string,string>
     */
    public static function getCreateValidationRules(): array
    {
        return self::CREATE_RULE;
    }


    public static function findByJobUuid($uuid)
    {
        return static::whereJsonContains('data->job_uuid', $uuid)->first();
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
     * updateNotificationOpenCount
     *
     * @return bool
     */
    public function updateNotificationOpenCount(): bool
    {
        return $this->update([
            'opened_at' => Carbon::now(),
            'status' => StatusType::OPEN,
            'open_count' => $this->increment('open_count')
        ]);
    }

    /**
     * updateStatus
     *
     * @param  int $status
     * @return bool
     */
    public function updateStatus(int $status, ?array $data = null): bool
    {
        $this->status = $status;
        if (!empty($data)) {
            $this->data = $data;
        }
        return $this->save();
    }
}
