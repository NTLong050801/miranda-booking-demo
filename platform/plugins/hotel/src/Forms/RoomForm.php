<?php

namespace Botble\Hotel\Forms;

use Botble\Base\Facades\Assets;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Forms\FormAbstract;
use Botble\Hotel\Http\Requests\RoomRequest;
use Botble\Hotel\Models\Room;
use Botble\Hotel\Repositories\Interfaces\AmenityInterface;
use Botble\Hotel\Repositories\Interfaces\CurrencyInterface;
use Botble\Hotel\Repositories\Interfaces\RoomCategoryInterface;

class RoomForm extends FormAbstract
{
    public function __construct(
        protected AmenityInterface $amenityRepository,
        protected CurrencyInterface $currencyRepository,
        protected RoomCategoryInterface $roomCategoryRepository
    ) {
        parent::__construct();
    }

    public function buildForm(): void
    {
        Assets::addScripts(['input-mask', 'moment'])
            ->addScriptsDirectly([
                'vendor/core/plugins/hotel/libraries/full-calendar-5.3.2/main.min.js',
                'vendor/core/plugins/hotel/js/room-availability.js',
            ])
            ->addStylesDirectly([
                'vendor/core/plugins/hotel/libraries/full-calendar-5.3.2/main.min.css',
                'vendor/core/plugins/hotel/css/hotel.css',
            ]);

        Assets::usingVueJS();

        $currencies = $this->currencyRepository->pluck('title', 'id');

        $roomCategories = $this->roomCategoryRepository->pluck('name', 'id');

        $amenities = $this->amenityRepository->allBy([], [], ['id', 'name']);

        $selectedAmenities = [];
        if ($this->getModel()) {
            $selectedAmenities = $this->getModel()->amenities()->pluck('ht_amenities.id')->all();
        }

        $this
            ->setupModel(new Room())
            ->setValidatorClass(RoomRequest::class)
            ->withCustomFields()
            ->add('name', 'text', [
                'label' => trans('core/base::forms.name'),
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'placeholder' => trans('core/base::forms.name_placeholder'),
                    'data-counter' => 120,
                ],
            ])
            ->add('description', 'textarea', [
                'label' => trans('core/base::forms.description'),
                'label_attr' => ['class' => 'control-label'],
                'attr' => [
                    'rows' => 4,
                    'placeholder' => trans('core/base::forms.description_placeholder'),
                    'data-counter' => 400,
                ],
            ])
            ->add('is_featured', 'onOff', [
                'label' => trans('core/base::forms.is_featured'),
                'label_attr' => ['class' => 'control-label'],
                'default_value' => false,
            ])
            ->add('content', 'editor', [
                'label' => trans('core/base::forms.content'),
                'label_attr' => ['class' => 'control-label'],
                'attr' => [
                    'rows' => 4,
                    'placeholder' => trans('core/base::forms.description_placeholder'),
                    'with-short-code' => true,
                ],
            ])
            ->add('order', 'number', [
                'label' => trans('core/base::forms.order'),
                'label_attr' => ['class' => 'control-label'],
                'attr' => [
                    'placeholder' => trans('core/base::forms.order_by_placeholder'),
                ],
                'default_value' => 0,
            ])
            ->add('rowOpen1', 'html', [
                'html' => '<div class="row">',
            ])
            ->add('price', 'text', [
                'label' => trans('plugins/hotel::room.form.price'),
                'label_attr' => ['class' => 'control-label required'],
                'wrapper' => [
                    'class' => 'form-group col-md-6',
                ],
                'attr' => [
                    'id' => 'price-number',
                    'placeholder' => trans('plugins/hotel::room.form.price'),
                    'class' => 'form-control input-mask-number',
                ],
            ])
            ->add('currency_id', 'customSelect', [
                'label' => trans('plugins/hotel::room.form.currency'),
                'label_attr' => ['class' => 'control-label'],
                'wrapper' => [
                    'class' => 'form-group col-md-6',
                ],
                'attr' => [
                    'class' => 'form-control select-full',
                ],
                'choices' => $currencies,
            ])
            ->add('rowClose1', 'html', [
                'html' => '</div>',
            ])
            ->add('rowOpen2', 'html', [
                'html' => '<div class="row">',
            ])
            ->add('number_of_rooms', 'text', [
                'label' => trans('plugins/hotel::room.form.number_of_rooms'),
                'label_attr' => ['class' => 'control-label'],
                'wrapper' => [
                    'class' => 'form-group col-md-6',
                ],
                'attr' => [
                    'id' => 'number-of-rooms-number',
                    'placeholder' => trans('plugins/hotel::room.form.number_of_rooms'),
                    'class' => 'form-control input-mask-number',
                ],
                'default_value' => 1,
            ])
            ->add('number_of_beds', 'text', [
                'label' => trans('plugins/hotel::room.form.number_of_beds'),
                'label_attr' => ['class' => 'control-label'],
                'wrapper' => [
                    'class' => 'form-group col-md-6',
                ],
                'attr' => [
                    'id' => 'number-of-beds-number',
                    'placeholder' => trans('plugins/hotel::room.form.number_of_beds'),
                    'class' => 'form-control input-mask-number',
                ],
                'default_value' => 0,
            ])
            ->add('rowClose2', 'html', [
                'html' => '</div>',
            ])
            ->add('rowOpen3', 'html', [
                'html' => '<div class="row">',
            ])
            ->add('max_adults', 'text', [
                'label' => trans('plugins/hotel::room.form.max_adults'),
                'label_attr' => ['class' => 'control-label'],
                'wrapper' => [
                    'class' => 'form-group col-md-4',
                ],
                'attr' => [
                    'id' => 'max-adults-number',
                    'placeholder' => trans('plugins/hotel::room.form.max_adults'),
                    'class' => 'form-control input-mask-number',
                ],
                'default_value' => 1,
            ])
            ->add('max_children', 'text', [
                'label' => trans('plugins/hotel::room.form.max_children'),
                'label_attr' => ['class' => 'control-label'],
                'wrapper' => [
                    'class' => 'form-group col-md-4',
                ],
                'attr' => [
                    'id' => 'max-children-number',
                    'placeholder' => trans('plugins/hotel::room.form.max_children'),
                    'class' => 'form-control input-mask-number',
                ],
                'default_value' => 0,
            ])
            ->add('size', 'text', [
                'label' => trans('plugins/hotel::room.form.size'),
                'label_attr' => ['class' => 'control-label'],
                'wrapper' => [
                    'class' => 'form-group col-md-4',
                ],
                'attr' => [
                    'id' => 'size-number',
                    'placeholder' => trans('plugins/hotel::room.form.size'),
                    'class' => 'form-control input-mask-number',
                ],
                'default_value' => 0,
            ])
            ->add('rowClose3', 'html', [
                'html' => '</div>',
            ])
            ->add('images[]', 'mediaImages', [
                'label' => trans('plugins/hotel::room.images'),
                'label_attr' => ['class' => 'control-label'],
                'values' => $this->getModel()->id ? $this->getModel()->images : [],
            ])
            ->add('status', 'customSelect', [
                'label' => trans('core/base::tables.status'),
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'class' => 'form-control select-full',
                ],
                'choices' => BaseStatusEnum::labels(),
            ])
            ->add('room_category_id', 'customSelect', [
                'label' => trans('plugins/hotel::room.form.category'),
                'label_attr' => ['class' => 'control-label required'],
                'wrapper' => [
                    'class' => 'form-group col-md-4',
                ],
                'attr' => [
                    'class' => 'form-control select-full',
                ],
                'choices' => $roomCategories,
            ])
            ->addMetaBoxes([
                'amenities' => [
                    'title' => trans('plugins/hotel::room.amenities'),
                    'content' => view(
                        'plugins/hotel::forms.amenities',
                        compact('selectedAmenities', 'amenities')
                    )->render(),
                    'priority' => 1,
                ],
            ])
            ->setBreakFieldPoint('status');

        if ($this->getModel()->id) {
            $this->addMetaBoxes([
                'room-availability' => [
                    'title' => trans('plugins/hotel::room.form.room_availability'),
                    'content' => view(
                        'plugins/hotel::forms.room-availability',
                        ['object' => $this->getModel()]
                    )->render(),
                    'priority' => 2,
                ],
            ]);
        }
    }
}
