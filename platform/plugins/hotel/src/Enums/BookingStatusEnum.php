<?php

namespace Botble\Hotel\Enums;

use Botble\Base\Supports\Enum;
use Collective\Html\HtmlFacade as Html;

/**
 * @method static BookingStatusEnum PENDING()
 * @method static BookingStatusEnum PROCESSING()
 * @method static BookingStatusEnum COMPLETED()
 * @method static BookingStatusEnum CANCELLED()
 */
class BookingStatusEnum extends Enum
{
    public const PENDING = 'pending';
    public const PROCESSING = 'processing';
    public const COMPLETED = 'completed';
    public const CANCELLED = 'cancelled';

    public static $langPath = 'plugins/hotel::booking.statuses';

    public function toHtml()
    {
        return match ($this->value) {
            self::PENDING => Html::tag('span', self::PENDING()->label(), ['class' => 'label-warning status-label'])
                ->toHtml(),
            self::PROCESSING => Html::tag('span', self::PROCESSING()->label(), ['class' => 'label-info status-label'])
                ->toHtml(),
            self::COMPLETED => Html::tag('span', self::COMPLETED()->label(), ['class' => 'label-success status-label'])
                ->toHtml(),
            self::CANCELLED => Html::tag('span', self::CANCELLED()->label(), ['class' => 'label-danger status-label'])
                ->toHtml(),
            default => parent::toHtml(),
        };
    }
}
