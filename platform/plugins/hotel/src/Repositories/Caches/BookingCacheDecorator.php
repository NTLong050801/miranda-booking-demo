<?php

namespace Botble\Hotel\Repositories\Caches;

use Botble\Hotel\Repositories\Interfaces\BookingInterface;
use Botble\Support\Repositories\Caches\CacheAbstractDecorator;

class BookingCacheDecorator extends CacheAbstractDecorator implements BookingInterface
{
    public function getPendingBookings(array $select = ['*'], array $with = [])
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }

    public function countPendingBookings(): int
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }
}
