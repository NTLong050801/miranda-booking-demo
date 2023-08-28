<?php

namespace Botble\Hotel\Models;

use Botble\Base\Casts\SafeContent;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RoomCategory extends BaseModel
{
    protected $table = 'ht_room_categories';

    protected $fillable = [
        'name',
        'is_featured',
        'order',
        'status',
    ];

    protected $casts = [
        'status' => BaseStatusEnum::class,
        'name' => SafeContent::class,
    ];

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }
}
