<?php

use Botble\Widget\AbstractWidget;

class BlogTagsWidget extends AbstractWidget
{
    public function __construct()
    {
        parent::__construct([
            'name' => __('Blog Tags'),
            'description' => __('Widget displays blog tags'),
            'number_display' => 5,
        ]);
    }
}
