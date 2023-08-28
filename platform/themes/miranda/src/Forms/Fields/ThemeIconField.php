<?php

namespace Theme\Miranda\Forms\Fields;

use Botble\Base\Facades\Assets;
use Botble\Base\Forms\FormField;
use Botble\Theme\Facades\Theme;

class ThemeIconField extends FormField
{
    protected function getTemplate(): string
    {
        Assets::addScriptsDirectly(Theme::asset()->url('js/icons-field.js'))
            ->addStylesDirectly(Theme::asset()->url('css/flaticon.css'))
            ->addStylesDirectly(Theme::asset()->url('css/font-awesome.min.css'));

        return Theme::getThemeNamespace() . '::partials.forms.fields.theme-icon-field';
    }
}
