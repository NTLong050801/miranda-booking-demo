<?php

namespace Botble\Hotel\Tables;

use Illuminate\Support\Facades\Auth;
use Botble\Base\Facades\BaseHelper;
use Botble\Hotel\Enums\BookingStatusEnum;
use Botble\Hotel\Models\Booking;
use Botble\Hotel\Repositories\Interfaces\BookingInterface;
use Botble\Payment\Enums\PaymentStatusEnum;
use Botble\Table\Abstracts\TableAbstract;
use Collective\Html\HtmlFacade as Html;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Relations\Relation as EloquentRelation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Botble\Table\DataTables;

class BookingTable extends TableAbstract
{
    protected $hasActions = true;

    protected $hasFilter = true;

    public function __construct(DataTables $table, UrlGenerator $urlGenerator, BookingInterface $bookingRepository)
    {
        parent::__construct($table, $urlGenerator);

        $this->repository = $bookingRepository;

        if (! Auth::user()->hasAnyPermission(['booking.edit', 'booking.destroy'])) {
            $this->hasOperations = false;
            $this->hasActions = false;
        }
    }

    public function ajax(): JsonResponse
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('amount', function ($item) {
                return format_price($item->amount, $item->currency_id);
            })
            ->editColumn('payment_status', function ($item) {
                return $item->payment->status->label() ? BaseHelper::clean($item->payment->status->toHtml()) : '&mdash;';
            })
            ->editColumn('payment_method', function ($item) {
                return BaseHelper::clean($item->payment->payment_channel->label() ?: '&mdash;');
            })
            ->editColumn('customer_id', function ($item) {
                return $item->address->id ? BaseHelper::clean($item->address->first_name . ' ' . $item->address->last_name) : '&mdash;';
            })
            ->editColumn('room_id', function ($item) {
                return $item->room->room->id ? Html::link(
                    $item->room->room->url,
                    BaseHelper::clean($item->room->room->name),
                    ['target' => '_blank']
                ) : '&mdash;';
            })
            ->editColumn('updated_at', function ($item) {
                return BaseHelper::formatDate($item->room->start_date) . ' -> ' . BaseHelper::formatDate($item->room->end_date);
            })
            ->editColumn('created_at', function ($item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->editColumn('status', function ($item) {
                return $item->status->toHtml();
            })
            ->addColumn('operations', function ($item) {
                return $this->getOperations('booking.edit', 'booking.destroy', $item);
            })
            ->filter(function ($query) {
                $keyword = $this->request->input('search.value');
                if ($keyword) {
                    return $query->whereHas('address', function ($subQuery) use ($keyword) {
                        return $subQuery
                            ->where('ht_booking_addresses.first_name', 'LIKE', '%' . $keyword . '%')
                            ->orWhere('ht_booking_addresses.last_name', 'LIKE', '%' . $keyword . '%')
                            ->orWhere(DB::raw('CONCAT(ht_booking_addresses.first_name, " ", ht_booking_addresses.last_name)'), 'LIKE', '%' . $keyword . '%')
                            ->orWhere(DB::raw('CONCAT(ht_booking_addresses.last_name, " ", ht_booking_addresses.first_name)'), 'LIKE', '%' . $keyword . '%');
                    });
                }

                return $query;
            });

        return $this->toJson($data);
    }

    public function query(): Relation|Builder|QueryBuilder
    {
        $query = $this->repository->getModel()
            ->select([
                'id',
                'created_at',
                'status',
                'amount',
                'currency_id',
                'payment_id',
            ])
            ->with(['address', 'room', 'payment']);

        return $this->applyScopes($query);
    }

    public function columns(): array
    {
        return [
            'id' => [
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
            ],
            'customer_id' => [
                'title' => trans('plugins/hotel::booking.customer'),
                'class' => 'text-start',
                'orderable' => false,
                'searchable' => false,
            ],
            'room_id' => [
                'title' => trans('plugins/hotel::booking.room'),
                'class' => 'text-start',
                'orderable' => false,
                'searchable' => false,
            ],
            'amount' => [
                'title' => trans('plugins/hotel::booking.amount'),
                'class' => 'text-start',
            ],
            'payment_method' => [
                'name' => 'payment_id',
                'title' => trans('plugins/hotel::booking.payment_method'),
                'class' => 'text-center',
                'orderable' => false,
                'searchable' => false,
            ],
            'payment_status' => [
                'name' => 'payment_id',
                'title' => trans('plugins/hotel::booking.payment_status_label'),
                'class' => 'text-center',
                'orderable' => false,
                'searchable' => false,
            ],
            'updated_at' => [
                'title' => trans('plugins/hotel::booking.booking_period'),
                'class' => 'text-start',
            ],
            'created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'width' => '100px',
                'class' => 'text-start',
            ],
            'status' => [
                'title' => trans('core/base::tables.status'),
                'width' => '100px',
                'class' => 'text-start',
            ],
        ];
    }

    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('booking.deletes'), 'booking.destroy', parent::bulkActions());
    }

    public function getFilters(): array
    {
        return $this->getBulkChanges();
    }

    public function getBulkChanges(): array
    {
        return [
            'status' => [
                'title' => trans('core/base::tables.status'),
                'type' => 'select',
                'choices' => BookingStatusEnum::labels(),
                'validate' => 'required|in:' . implode(',', BookingStatusEnum::values()),
            ],
            'payment_status' => [
                'title' => trans('plugins/hotel::booking.payment_status_label'),
                'type' => 'select',
                'choices' => PaymentStatusEnum::labels(),
                'validate' => 'required|in:' . implode(',', PaymentStatusEnum::values()),
            ],
            'created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'type' => 'datePicker',
            ],
        ];
    }

    public function applyFilterCondition(EloquentBuilder|QueryBuilder|EloquentRelation $query, string $key, string $operator, ?string $value): EloquentRelation|EloquentBuilder|QueryBuilder
    {
        if ($key === 'payment_status') {
            return $query->whereHas('payment', function ($query) use ($value) {
                return $query->where('status', $value);
            });
        }

        return parent::applyFilterCondition($query, $key, $operator, $value);
    }

    public function saveBulkChangeItem(Model|Booking $item, string $inputKey, ?string $inputValue): Model|bool
    {
        if ($inputKey === 'payment_status') {
            /**
             * @var Booking $item
             */
            $item->payment()->update(['status' => $inputValue]);

            return $item;
        }

        return parent::saveBulkChangeItem($item, $inputKey, $inputValue);
    }
}
