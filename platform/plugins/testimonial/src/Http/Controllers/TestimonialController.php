<?php

namespace Botble\Testimonial\Http\Controllers;

use Botble\Base\Facades\PageTitle;
use Botble\Base\Traits\HasDeleteManyItemsTrait;
use Botble\Testimonial\Http\Requests\TestimonialRequest;
use Botble\Testimonial\Models\Testimonial;
use Botble\Testimonial\Repositories\Interfaces\TestimonialInterface;
use Botble\Base\Http\Controllers\BaseController;
use Exception;
use Illuminate\Http\Request;
use Botble\Testimonial\Tables\TestimonialTable;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Testimonial\Forms\TestimonialForm;
use Botble\Base\Forms\FormBuilder;

class TestimonialController extends BaseController
{
    use HasDeleteManyItemsTrait;

    public function __construct(protected TestimonialInterface $testimonialRepository)
    {
    }

    public function index(TestimonialTable $table)
    {
        PageTitle::setTitle(trans('plugins/testimonial::testimonial.name'));

        return $table->renderTable();
    }

    public function create(FormBuilder $formBuilder)
    {
        PageTitle::setTitle(trans('plugins/testimonial::testimonial.create'));

        return $formBuilder->create(TestimonialForm::class)->renderForm();
    }

    public function store(TestimonialRequest $request, BaseHttpResponse $response)
    {
        $testimonial = $this->testimonialRepository->createOrUpdate($request->input());

        event(new CreatedContentEvent(TESTIMONIAL_MODULE_SCREEN_NAME, $request, $testimonial));

        return $response
            ->setPreviousUrl(route('testimonial.index'))
            ->setNextUrl(route('testimonial.edit', $testimonial->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit(Testimonial $testimonial, FormBuilder $formBuilder)
    {
        PageTitle::setTitle(trans('core/base::forms.edit_item', ['name' => $testimonial->name]));

        return $formBuilder->create(TestimonialForm::class, ['model' => $testimonial])->renderForm();
    }

    public function update(Testimonial $testimonial, TestimonialRequest $request, BaseHttpResponse $response)
    {
        $testimonial->fill($request->input());

        $this->testimonialRepository->createOrUpdate($testimonial);

        event(new UpdatedContentEvent(TESTIMONIAL_MODULE_SCREEN_NAME, $request, $testimonial));

        return $response
            ->setPreviousUrl(route('testimonial.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(Testimonial $testimonial, Request $request, BaseHttpResponse $response)
    {
        try {
            $this->testimonialRepository->delete($testimonial);

            event(new DeletedContentEvent(TESTIMONIAL_MODULE_SCREEN_NAME, $request, $testimonial));

            return $response->setMessage(trans('core/base::notices.delete_success_message'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    public function deletes(Request $request, BaseHttpResponse $response)
    {
        return $this->executeDeleteItems(
            $request,
            $response,
            $this->testimonialRepository,
            TESTIMONIAL_MODULE_SCREEN_NAME
        );
    }
}
