<?php

namespace Botble\Hotel\Http\Controllers;

use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Forms\FormBuilder;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Hotel\Forms\ServiceForm;
use Botble\Hotel\Http\Requests\ServiceRequest;
use Botble\Hotel\Repositories\Interfaces\ServiceInterface;
use Botble\Hotel\Tables\ServiceTable;
use Exception;
use Illuminate\Http\Request;
use Botble\Base\Facades\PageTitle;

class ServiceController extends BaseController
{
    public function __construct(protected ServiceInterface $serviceRepository)
    {
    }

    public function index(ServiceTable $table)
    {
        PageTitle::setTitle(trans('plugins/hotel::service.name'));

        return $table->renderTable();
    }

    public function create(FormBuilder $formBuilder)
    {
        PageTitle::setTitle(trans('plugins/hotel::service.create'));

        return $formBuilder->create(ServiceForm::class)->renderForm();
    }

    public function store(ServiceRequest $request, BaseHttpResponse $response)
    {
        $service = $this->serviceRepository->createOrUpdate($request->input());

        event(new CreatedContentEvent(SERVICE_MODULE_SCREEN_NAME, $request, $service));

        return $response
            ->setPreviousUrl(route('service.index'))
            ->setNextUrl(route('service.edit', $service->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit(int|string $id, FormBuilder $formBuilder)
    {
        $service = $this->serviceRepository->findOrFail($id);

        PageTitle::setTitle(trans('plugins/hotel::service.edit') . ' "' . $service->name . '"');

        return $formBuilder->create(ServiceForm::class, ['model' => $service])->renderForm();
    }

    public function update(int|string $id, ServiceRequest $request, BaseHttpResponse $response)
    {
        $service = $this->serviceRepository->findOrFail($id);

        $service->fill($request->input());

        $this->serviceRepository->createOrUpdate($service);

        event(new UpdatedContentEvent(SERVICE_MODULE_SCREEN_NAME, $request, $service));

        return $response
            ->setPreviousUrl(route('service.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(int|string $id, Request $request, BaseHttpResponse $response)
    {
        try {
            $service = $this->serviceRepository->findOrFail($id);

            $this->serviceRepository->delete($service);

            event(new DeletedContentEvent(SERVICE_MODULE_SCREEN_NAME, $request, $service));

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
            $service = $this->serviceRepository->findOrFail($id);
            $this->serviceRepository->delete($service);
            event(new DeletedContentEvent(SERVICE_MODULE_SCREEN_NAME, $request, $service));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }
}
