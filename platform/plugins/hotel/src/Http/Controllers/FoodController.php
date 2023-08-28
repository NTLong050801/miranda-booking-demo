<?php

namespace Botble\Hotel\Http\Controllers;

use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Forms\FormBuilder;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Hotel\Forms\FoodForm;
use Botble\Hotel\Http\Requests\FoodRequest;
use Botble\Hotel\Repositories\Interfaces\FoodInterface;
use Botble\Hotel\Tables\FoodTable;
use Exception;
use Illuminate\Http\Request;
use Botble\Base\Facades\PageTitle;

class FoodController extends BaseController
{
    public function __construct(protected FoodInterface $foodRepository)
    {
    }

    public function index(FoodTable $table)
    {
        PageTitle::setTitle(trans('plugins/hotel::food.name'));

        return $table->renderTable();
    }

    public function create(FormBuilder $formBuilder)
    {
        PageTitle::setTitle(trans('plugins/hotel::food.create'));

        return $formBuilder->create(FoodForm::class)->renderForm();
    }

    public function store(FoodRequest $request, BaseHttpResponse $response)
    {
        $food = $this->foodRepository->createOrUpdate($request->input());

        event(new CreatedContentEvent(FOOD_MODULE_SCREEN_NAME, $request, $food));

        return $response
            ->setPreviousUrl(route('food.index'))
            ->setNextUrl(route('food.edit', $food->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit(int|string $id, FormBuilder $formBuilder)
    {
        $food = $this->foodRepository->findOrFail($id);

        PageTitle::setTitle(trans('plugins/hotel::food.edit') . ' "' . $food->name . '"');

        return $formBuilder->create(FoodForm::class, ['model' => $food])->renderForm();
    }

    public function update(int|string $id, FoodRequest $request, BaseHttpResponse $response)
    {
        $food = $this->foodRepository->findOrFail($id);

        $food->fill($request->input());

        $this->foodRepository->createOrUpdate($food);

        event(new UpdatedContentEvent(FOOD_MODULE_SCREEN_NAME, $request, $food));

        return $response
            ->setPreviousUrl(route('food.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(int|string $id, Request $request, BaseHttpResponse $response)
    {
        try {
            $food = $this->foodRepository->findOrFail($id);

            $this->foodRepository->delete($food);

            event(new DeletedContentEvent(FOOD_MODULE_SCREEN_NAME, $request, $food));

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
            $food = $this->foodRepository->findOrFail($id);
            $this->foodRepository->delete($food);
            event(new DeletedContentEvent(FOOD_MODULE_SCREEN_NAME, $request, $food));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }
}
