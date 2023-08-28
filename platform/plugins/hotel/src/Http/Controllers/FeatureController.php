<?php

namespace Botble\Hotel\Http\Controllers;

use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Forms\FormBuilder;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Hotel\Forms\FeatureForm;
use Botble\Hotel\Http\Requests\FeatureRequest;
use Botble\Hotel\Repositories\Interfaces\FeatureInterface;
use Botble\Hotel\Tables\FeatureTable;
use Exception;
use Illuminate\Http\Request;
use Botble\Base\Facades\PageTitle;

class FeatureController extends BaseController
{
    public function __construct(protected FeatureInterface $featureRepository)
    {
    }

    public function index(FeatureTable $table)
    {
        PageTitle::setTitle(trans('plugins/hotel::feature.name'));

        return $table->renderTable();
    }

    public function create(FormBuilder $formBuilder)
    {
        PageTitle::setTitle(trans('plugins/hotel::feature.create'));

        return $formBuilder->create(FeatureForm::class)->renderForm();
    }

    public function store(FeatureRequest $request, BaseHttpResponse $response)
    {
        $feature = $this->featureRepository->createOrUpdate($request->input());

        event(new CreatedContentEvent(FEATURE_MODULE_SCREEN_NAME, $request, $feature));

        return $response
            ->setPreviousUrl(route('feature.index'))
            ->setNextUrl(route('feature.edit', $feature->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit(int|string $id, FormBuilder $formBuilder)
    {
        $feature = $this->featureRepository->findOrFail($id);

        PageTitle::setTitle(trans('plugins/hotel::feature.edit') . ' "' . $feature->name . '"');

        return $formBuilder->create(FeatureForm::class, ['model' => $feature])->renderForm();
    }

    public function update(int|string $id, FeatureRequest $request, BaseHttpResponse $response)
    {
        $feature = $this->featureRepository->findOrFail($id);

        $feature->fill($request->input());

        $this->featureRepository->createOrUpdate($feature);

        event(new UpdatedContentEvent(FEATURE_MODULE_SCREEN_NAME, $request, $feature));

        return $response
            ->setPreviousUrl(route('feature.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(int|string $id, Request $request, BaseHttpResponse $response)
    {
        try {
            $feature = $this->featureRepository->findOrFail($id);

            $this->featureRepository->delete($feature);

            event(new DeletedContentEvent(FEATURE_MODULE_SCREEN_NAME, $request, $feature));

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
            $feature = $this->featureRepository->findOrFail($id);
            $this->featureRepository->delete($feature);
            event(new DeletedContentEvent(FEATURE_MODULE_SCREEN_NAME, $request, $feature));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }
}
