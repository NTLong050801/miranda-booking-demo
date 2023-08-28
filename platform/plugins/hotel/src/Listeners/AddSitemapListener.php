<?php

namespace Botble\Hotel\Listeners;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Hotel\Repositories\Interfaces\RoomInterface;
use Botble\Theme\Events\RenderingSiteMapEvent;
use Botble\Theme\Facades\SiteMapManager;

class AddSitemapListener
{
    public function __construct(protected RoomInterface $roomRepository)
    {
    }

    public function handle(RenderingSiteMapEvent $event): void
    {
        if ($event->key == 'rooms') {
            $roomLastUpdated = $this->roomRepository
                ->getModel()
                ->where('status', BaseStatusEnum::PUBLISHED)
                ->latest('updated_at')
                ->value('updated_at');

            SiteMapManager::add(route('public.rooms'), $roomLastUpdated, '0.4', 'monthly');

            $rooms = $this->roomRepository->allBy(['status' => BaseStatusEnum::PUBLISHED], ['slugable']);

            foreach ($rooms as $room) {
                SiteMapManager::add($room->url, $room->updated_at, '0.6');
            }
        }

        $roomLastUpdated = $this->roomRepository
            ->getModel()
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->latest('updated_at')
            ->value('updated_at');

        SiteMapManager::addSitemap(SiteMapManager::route('rooms'), $roomLastUpdated);
    }
}
