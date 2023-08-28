<?php

namespace Botble\Hotel\Http\Controllers;

use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Forms\FormBuilder;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Hotel\Forms\CustomerForm;
use Botble\Hotel\Http\Requests\CustomerCreateRequest;
use Botble\Hotel\Http\Requests\CustomerEditRequest;
use Botble\Hotel\Repositories\Interfaces\CustomerInterface;
use Botble\Hotel\Tables\CustomerTable;
use Exception;
use Illuminate\Http\Request;
use Botble\Base\Facades\PageTitle;

class CustomerController extends BaseController
{
    public function __construct(protected CustomerInterface $customerRepository)
    {
    }

    public function index(CustomerTable $table)
    {
        PageTitle::setTitle(trans('plugins/hotel::customer.name'));

        return $table->renderTable();
    }

    public function create(FormBuilder $formBuilder)
    {
        PageTitle::setTitle(trans('plugins/hotel::customer.create'));

        return $formBuilder->create(CustomerForm::class)->renderForm();
    }

    public function store(CustomerCreateRequest $request, BaseHttpResponse $response)
    {
        $customer = $this->customerRepository->createOrUpdate($request->input());

        event(new CreatedContentEvent(CUSTOMER_MODULE_SCREEN_NAME, $request, $customer));

        return $response
            ->setPreviousUrl(route('customer.index'))
            ->setNextUrl(route('customer.edit', $customer->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit(int|string $id, FormBuilder $formBuilder)
    {
        $customer = $this->customerRepository->findOrFail($id);

        PageTitle::setTitle(trans('plugins/hotel::customer.edit') . ' "' . $customer->name . '"');

        return $formBuilder->create(CustomerForm::class, ['model' => $customer])->renderForm();
    }

    public function update(int|string $id, CustomerEditRequest $request, BaseHttpResponse $response)
    {
        $customer = $this->customerRepository->findOrFail($id);

        $customer->fill($request->input());

        $this->customerRepository->createOrUpdate($customer);

        event(new UpdatedContentEvent(CUSTOMER_MODULE_SCREEN_NAME, $request, $customer));

        return $response
            ->setPreviousUrl(route('customer.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(int|string $id, Request $request, BaseHttpResponse $response)
    {
        try {
            $customer = $this->customerRepository->findOrFail($id);

            $this->customerRepository->delete($customer);

            event(new DeletedContentEvent(CUSTOMER_MODULE_SCREEN_NAME, $request, $customer));

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
            $customer = $this->customerRepository->findOrFail($id);
            $this->customerRepository->delete($customer);
            event(new DeletedContentEvent(CUSTOMER_MODULE_SCREEN_NAME, $request, $customer));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }
}
