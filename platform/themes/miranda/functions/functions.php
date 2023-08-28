<?php

use Botble\Base\Facades\MetaBox;
use Botble\Base\Forms\FormAbstract;
use Botble\Media\Facades\RvMedia;
use Botble\Menu\Facades\Menu;
use Botble\Theme\Facades\Theme;
use Botble\Base\Facades\Form;
use Botble\Base\Forms\FormHelper;
use Botble\Hotel\Models\Amenity;
use Botble\Hotel\Models\Feature;
use Botble\Hotel\Models\FoodType;
use Theme\Miranda\Forms\Fields\ThemeIconField;

register_page_template([
    'no-sidebar' => __('No Sidebar'),
    'full-width' => __('Full width'),
    'homepage' => __('Homepage'),
]);

register_sidebar([
    'id' => 'footer_sidebar',
    'name' => __('Footer sidebar'),
    'description' => __('Sidebar in the footer of site'),
]);

Menu::removeMenuLocation('main-menu')
    ->addMenuLocation('header-menu', __('Header Navigation'))
    ->addMenuLocation('side-menu', __('Side Navigation'));

RvMedia::setUploadPathAndURLToPublic();

RvMedia::addSize('380x280', 380, 280)
    ->addSize('380x575', 380, 575)
    ->addSize('775x280', 775, 280)
    ->addSize('770x460', 770, 460)
    ->addSize('550x580', 550, 580)
    ->addSize('1170x570', 1170, 570);

Form::component('themeIcon', Theme::getThemeNamespace() . '::partials.forms.fields.icons-field', [
    'name',
    'value' => null,
    'attributes' => [],
]);

add_filter('form_custom_fields', function (FormAbstract $form, FormHelper $formHelper) {
    if (! $formHelper->hasCustomField('themeIcon')) {
        $form->addCustomField('themeIcon', ThemeIconField::class);
    }

    return $form;
}, 29, 2);

if (is_plugin_active('hotel')) {
    add_filter(BASE_FILTER_BEFORE_RENDER_FORM, function ($form, $data) {
        if (in_array(get_class($data), [Amenity::class, Feature::class, FoodType::class])) {
            $iconImage = null;

            if ($data->id) {
                $iconImage = MetaBox::getMetaData($data, 'icon_image', true);
            }

            $form
                ->modify('icon', 'themeIcon', ['label' => __('Font Icon')], true)
                ->addAfter('icon', 'icon_image', 'mediaImage', [
                    'value' => $iconImage,
                    'label' => __('Icon Image (It will replace Font Icon if it is present)'),
                ]);
        }

        return $form;
    }, 127, 2);

    add_action([BASE_ACTION_AFTER_CREATE_CONTENT, BASE_ACTION_AFTER_UPDATE_CONTENT], function ($type, $request, $object) {
        if (in_array(get_class($object), [Amenity::class, Feature::class, FoodType::class]) && $request->has('icon_image')) {
            MetaBox::saveMetaBoxData($object, 'icon_image', $request->input('icon_image'));
        }
    }, 230, 3);
}
