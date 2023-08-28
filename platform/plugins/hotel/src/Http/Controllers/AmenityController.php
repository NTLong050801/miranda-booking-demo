<?php

namespace Botble\Hotel\Http\Controllers;

use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Forms\FormBuilder;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Hotel\Forms\AmenityForm;
use Botble\Hotel\Http\Requests\AmenityRequest;
use Botble\Hotel\Repositories\Interfaces\AmenityInterface;
use Botble\Hotel\Tables\AmenityTable;
use Exception;
use Illuminate\Http\Request;
use Botble\Base\Facades\PageTitle;

class AmenityController extends BaseController
{
    public function __construct(protected AmenityInterface $amenitiesRepository)
    {
    }

    public function index(AmenityTable $table)
    {
        PageTitle::setTitle(trans('plugins/hotel::amenity.name'));

        return $table->renderTable();
    }

    public function create(FormBuilder $formBuilder)
    {
        PageTitle::setTitle(trans('plugins/hotel::amenity.create'));

        return $formBuilder->create(AmenityForm::class)->renderForm();
    }

    public function store(AmenityRequest $request, BaseHttpResponse $response)
    {
        $amenity = $this->amenitiesRepository->createOrUpdate($request->input());

        event(new CreatedContentEvent(AMENITIES_MODULE_SCREEN_NAME, $request, $amenity));

        return $response
            ->setPreviousUrl(route('amenity.index'))
            ->setNextUrl(route('amenity.edit', $amenity->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit(int|string $id, FormBuilder $formBuilder)
    {
        $amenity = $this->amenitiesRepository->findOrFail($id);

        PageTitle::setTitle(trans('plugins/hotel::amenity.edit') . ' "' . $amenity->name . '"');

        return $formBuilder->create(AmenityForm::class, ['model' => $amenity])->renderForm();
    }

    public function update(int|string $id, AmenityRequest $request, BaseHttpResponse $response)
    {
        $amenity = $this->amenitiesRepository->findOrFail($id);

        $amenity->fill($request->input());

        $this->amenitiesRepository->createOrUpdate($amenity);

        event(new UpdatedContentEvent(AMENITIES_MODULE_SCREEN_NAME, $request, $amenity));

        return $response
            ->setPreviousUrl(route('amenity.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(int|string $id, Request $request, BaseHttpResponse $response)
    {
        try {
            $amenity = $this->amenitiesRepository->findOrFail($id);

            $this->amenitiesRepository->delete($amenity);

            event(new DeletedContentEvent(AMENITIES_MODULE_SCREEN_NAME, $request, $amenity));

            return $response->setMessage(trans('core/base::notices.delete_success_message'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    public function deletes(Request $request, BaseHttpResponse $response)
    {
        $ids = $request->input('ids');
        if (empty($ids)) {
            return $response
                ->setError()
                ->setMessage(trans('core/base::notices.no_select'));
        }

        foreach ($ids as $id) {
            $amenity = $this->amenitiesRepository->findOrFail($id);
            $this->amenitiesRepository->delete($amenity);
            event(new DeletedContentEvent(AMENITIES_MODULE_SCREEN_NAME, $request, $amenity));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }
}
