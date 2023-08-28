<?php

namespace Botble\Hotel\Http\Controllers;

use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Forms\FormBuilder;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Hotel\Forms\RoomCategoryForm;
use Botble\Hotel\Http\Requests\RoomCategoryRequest;
use Botble\Hotel\Repositories\Interfaces\RoomCategoryInterface;
use Botble\Hotel\Tables\RoomCategoryTable;
use Exception;
use Illuminate\Http\Request;
use Botble\Base\Facades\PageTitle;

class RoomCategoryController extends BaseController
{
    public function __construct(protected RoomCategoryInterface $roomCategoryRepository)
    {
    }

    public function index(RoomCategoryTable $table)
    {
        PageTitle::setTitle(trans('plugins/hotel::room-category.name'));

        return $table->renderTable();
    }

    public function create(FormBuilder $formBuilder)
    {
        PageTitle::setTitle(trans('plugins/hotel::room-category.create'));

        return $formBuilder->create(RoomCategoryForm::class)->renderForm();
    }

    public function store(RoomCategoryRequest $request, BaseHttpResponse $response)
    {
        $roomCategory = $this->roomCategoryRepository->createOrUpdate($request->input());

        event(new CreatedContentEvent(ROOM_CATEGORY_MODULE_SCREEN_NAME, $request, $roomCategory));

        return $response
            ->setPreviousUrl(route('room-category.index'))
            ->setNextUrl(route('room-category.edit', $roomCategory->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit(int|string $id, FormBuilder $formBuilder)
    {
        $roomCategory = $this->roomCategoryRepository->findOrFail($id);

        PageTitle::setTitle(trans('plugins/hotel::room-category.edit') . ' "' . $roomCategory->name . '"');

        return $formBuilder->create(RoomCategoryForm::class, ['model' => $roomCategory])->renderForm();
    }

    public function update(int|string $id, RoomCategoryRequest $request, BaseHttpResponse $response)
    {
        $roomCategory = $this->roomCategoryRepository->findOrFail($id);

        $roomCategory->fill($request->input());

        $this->roomCategoryRepository->createOrUpdate($roomCategory);

        event(new UpdatedContentEvent(ROOM_CATEGORY_MODULE_SCREEN_NAME, $request, $roomCategory));

        return $response
            ->setPreviousUrl(route('room-category.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(int|string $id, Request $request, BaseHttpResponse $response)
    {
        try {
            $roomCategory = $this->roomCategoryRepository->findOrFail($id);

            $this->roomCategoryRepository->delete($roomCategory);

            event(new DeletedContentEvent(ROOM_CATEGORY_MODULE_SCREEN_NAME, $request, $roomCategory));

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
            $roomCategory = $this->roomCategoryRepository->findOrFail($id);
            $this->roomCategoryRepository->delete($roomCategory);
            event(new DeletedContentEvent(ROOM_CATEGORY_MODULE_SCREEN_NAME, $request, $roomCategory));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }
}
