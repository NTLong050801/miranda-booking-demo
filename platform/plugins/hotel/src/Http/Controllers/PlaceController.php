<?php

namespace Botble\Hotel\Http\Controllers;

use Botble\Hotel\Http\Requests\PlaceRequest;
use Botble\Hotel\Repositories\Interfaces\PlaceInterface;
use Botble\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Botble\Hotel\Tables\PlaceTable;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Hotel\Forms\PlaceForm;
use Botble\Base\Forms\FormBuilder;
use Botble\Base\Facades\PageTitle;

class PlaceController extends BaseController
{
    public function __construct(protected PlaceInterface $placeRepository)
    {
    }

    public function index(PlaceTable $table)
    {
        PageTitle::setTitle(trans('plugins/hotel::place.name'));

        return $table->renderTable();
    }

    public function create(FormBuilder $formBuilder)
    {
        PageTitle::setTitle(trans('plugins/hotel::place.create'));

        return $formBuilder->create(PlaceForm::class)->renderForm();
    }

    public function store(PlaceRequest $request, BaseHttpResponse $response)
    {
        $place = $this->placeRepository->createOrUpdate($request->input());

        event(new CreatedContentEvent(PLACE_MODULE_SCREEN_NAME, $request, $place));

        return $response
            ->setPreviousUrl(route('place.index'))
            ->setNextUrl(route('place.edit', $place->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit(int|string $id, FormBuilder $formBuilder)
    {
        $place = $this->placeRepository->findOrFail($id);

        PageTitle::setTitle(trans('plugins/hotel::place.edit') . ' "' . $place->name . '"');

        return $formBuilder->create(PlaceForm::class, ['model' => $place])->renderForm();
    }

    public function update(int|string $id, PlaceRequest $request, BaseHttpResponse $response)
    {
        $place = $this->placeRepository->findOrFail($id);

        $place->fill($request->input());

        $this->placeRepository->createOrUpdate($place);

        event(new UpdatedContentEvent(PLACE_MODULE_SCREEN_NAME, $request, $place));

        return $response
            ->setPreviousUrl(route('place.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(int|string $id, Request $request, BaseHttpResponse $response)
    {
        try {
            $place = $this->placeRepository->findOrFail($id);

            $this->placeRepository->delete($place);

            event(new DeletedContentEvent(PLACE_MODULE_SCREEN_NAME, $request, $place));

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
            $place = $this->placeRepository->findOrFail($id);
            $this->placeRepository->delete($place);
            event(new DeletedContentEvent(PLACE_MODULE_SCREEN_NAME, $request, $place));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }
}
