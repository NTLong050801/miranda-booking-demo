<?php

namespace Botble\Hotel\Repositories\Caches;

use Botble\Hotel\Repositories\Interfaces\RoomInterface;
use Botble\Support\Repositories\Caches\CacheAbstractDecorator;

class RoomCacheDecorator extends CacheAbstractDecorator implements RoomInterface
{
    public function getRooms(array $filters = [], array $params = [])
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }

    public function getRelatedRooms(int $roomId, int $limit = 4, array $params = [])
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }
}
