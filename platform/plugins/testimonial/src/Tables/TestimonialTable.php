<?php

namespace Botble\Testimonial\Tables;

use Botble\Base\Facades\BaseHelper;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Testimonial\Models\Testimonial;
use Botble\Testimonial\Repositories\Interfaces\TestimonialInterface;
use Botble\Base\Facades\Html;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Botble\Table\DataTables;

class TestimonialTable extends TableAbstract
{
    protected $hasActions = true;

    protected $hasFilter = true;

    public function __construct(
        DataTables $table,
        UrlGenerator $urlGenerator,
        TestimonialInterface $testimonialRepository
    ) {
        parent::__construct($table, $urlGenerator);

        $this->repository = $testimonialRepository;

        if (! Auth::user()->hasAnyPermission(['testimonial.edit', 'testimonial.destroy'])) {
            $this->hasOperations = false;
            $this->hasActions = false;
        }
    }

    public function ajax(): JsonResponse
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('image', function (Testimonial $item) {
                return $this->displayThumbnail($item->image, ['width' => 70]);
            })
            ->editColumn('name', function (Testimonial $item) {
                if (! Auth::user()->hasPermission('testimonial.edit')) {
                    return BaseHelper::clean($item->name);
                }

                return Html::link(route('testimonial.edit', $item->id), BaseHelper::clean($item->name));
            })
            ->editColumn('checkbox', function (Testimonial $item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('created_at', function (Testimonial $item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->addColumn('operations', function (Testimonial $item) {
                return $this->getOperations('testimonial.edit', 'testimonial.destroy', $item);
            });

        return $this->toJson($data);
    }

    public function query(): Relation|Builder|QueryBuilder
    {
        $query = $this->repository->getModel()->select([
            'id',
            'name',
            'created_at',
            'image',
        ]);

        return $this->applyScopes($query);
    }

    public function columns(): array
    {
        return [
            'id' => [
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
            ],
            'image' => [
                'title' => trans('core/base::tables.image'),
                'width' => '100px',
            ],
            'name' => [
                'title' => trans('core/base::tables.name'),
                'class' => 'text-start',
            ],
            'created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'width' => '100px',
            ],
        ];
    }

    public function buttons(): array
    {
        return $this->addCreateButton(route('testimonial.create'), 'testimonial.create');
    }

    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('testimonial.deletes'), 'testimonial.destroy', parent::bulkActions());
    }

    public function getBulkChanges(): array
    {
        return [
            'name' => [
                'title' => trans('core/base::tables.name'),
                'type' => 'text',
                'validate' => 'required|max:120',
            ],
            'created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'type' => 'datePicker',
            ],
        ];
    }
}
