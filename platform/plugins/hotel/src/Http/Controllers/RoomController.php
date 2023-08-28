<?php

namespace Botble\Hotel\Http\Controllers;

use Botble\Base\Facades\Assets;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Forms\FormBuilder;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Hotel\Forms\RoomForm;
use Botble\Hotel\Http\Requests\RoomRequest;
use Botble\Hotel\Http\Requests\RoomUpdateOrderByRequest;
use Botble\Hotel\Models\RoomDate;
use Botble\Hotel\Repositories\Interfaces\RoomDateInterface;
use Botble\Hotel\Repositories\Interfaces\RoomInterface;
use Botble\Hotel\Tables\RoomTable;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Botble\Base\Facades\PageTitle;

class RoomController extends BaseController
{
    public function __construct(protected RoomInterface $roomRepository)
    {
    }

    public function index(RoomTable $table)
    {
        PageTitle::setTitle(trans('plugins/hotel::room.name'));

        Assets::addScripts(['bootstrap-editable'])
            ->addStyles(['bootstrap-editable']);

        return $table->renderTable();
    }

    public function create(FormBuilder $formBuilder)
    {
        PageTitle::setTitle(trans('plugins/hotel::room.create'));

        return $formBuilder->create(RoomForm::class)->renderForm();
    }

    public function store(RoomRequest $request, BaseHttpResponse $response)
    {
        $request->merge([
            'images' => json_encode(array_filter($request->input('images', []))),
        ]);

        $room = $this->roomRepository->createOrUpdate($request->input());

        event(new CreatedContentEvent(ROOM_MODULE_SCREEN_NAME, $request, $room));

        if ($room) {
            $room->amenities()->sync($request->input('amenities', []));
        }

        return $response
            ->setPreviousUrl(route('room.index'))
            ->setNextUrl(route('room.edit', $room->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit(int|string $id, FormBuilder $formBuilder)
    {
        $room = $this->roomRepository->findOrFail($id);

        PageTitle::setTitle(trans('plugins/hotel::room.edit') . ' "' . $room->name . '"');

        return $formBuilder->create(RoomForm::class, ['model' => $room])->renderForm();
    }

    public function update(int|string $id, RoomRequest $request, BaseHttpResponse $response)
    {
        $room = $this->roomRepository->findOrFail($id);

        $room->fill($request->input());
        $room->images = json_encode(array_filter($request->input('images', [])));

        $this->roomRepository->createOrUpdate($room);

        event(new UpdatedContentEvent(ROOM_MODULE_SCREEN_NAME, $request, $room));

        $room->amenities()->sync($request->input('amenities', []));

        return $response
            ->setPreviousUrl(route('room.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(int|string $id, Request $request, BaseHttpResponse $response)
    {
        try {
            $room = $this->roomRepository->findOrFail($id);

            $this->roomRepository->delete($room);

            event(new DeletedContentEvent(ROOM_MODULE_SCREEN_NAME, $request, $room));

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
            $room = $this->roomRepository->findOrFail($id);
            $this->roomRepository->delete($room);
            event(new DeletedContentEvent(ROOM_MODULE_SCREEN_NAME, $request, $room));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }

    public function getRoomAvailability(int $id, Request $request)
    {
        $request->validate([
            'start' => 'required',
            'end' => 'required',
        ]);

        $startDate = Carbon::parse($request->input('start'));
        $endDate = Carbon::parse($request->input('end'));

        $room = $this->roomRepository->findOrFail($id, [
            'activeBookingRooms' => function ($query) use ($startDate, $endDate) {
                return $query
                    ->where(function ($query) use ($startDate, $endDate) {
                        return $query
                            ->whereDate('start_date', '>=', $startDate)
                            ->whereDate('start_date', '<=', $endDate);
                    })
                    ->orWhere(function ($query) use ($startDate, $endDate) {
                        return $query
                            ->whereDate('end_date', '>=', $startDate)
                            ->whereDate('end_date', '<=', $endDate);
                    })
                    ->orWhere(function ($query) use ($startDate, $endDate) {
                        return $query
                            ->whereDate('start_date', '<=', $startDate)
                            ->whereDate('end_date', '>=', $endDate);
                    })
                    ->orWhere(function ($query) use ($startDate, $endDate) {
                        return $query
                            ->whereDate('start_date', '>=', $startDate)
                            ->whereDate('end_date', '<=', $endDate);
                    });
            },
        ]);

        $allDates = [];

        for ($i = strtotime($request->query('start')); $i <= strtotime($request->query('end')); $i += 60 * 60 * 24) {
            $date = [
                'id' => rand(0, 999),
                'price' => $room->price,
                'number_of_rooms' => $room->number_of_rooms,
                'is_default' => true,
                'textColor' => '#2791fe',
            ];
            $date['price_html'] = format_price($date['price']);
            $date['title'] = $date['event'] = $date['price_html'];
            $date['start'] = $date['end'] = date('Y-m-d', $i);

            $date['active'] = 1;
            $allDates[date('Y-m-d', $i)] = $date;
        }

        $rows = RoomDate::where('room_id', $id)
            ->where('start_date', '>=', date('Y-m-d H:i:s', strtotime($request->query('start'))))
            ->where('end_date', '<=', date('Y-m-d H:i:s', strtotime($request->query('end'))))
            ->take(40)
            ->get();

        if (! empty($rows)) {
            foreach ($rows as $row) {
                $row->start = date('Y-m-d', strtotime($row->start_date));
                $row->end = date('Y-m-d', strtotime($row->start_date));
                $row->textColor = '#2791fe';
                $price = $row->price;
                if (empty($price)) {
                    $price = $room->price;
                }
                $row->title = $row->event = format_price($price);
                $row->price = $price;

                if (! $row->active) {
                    $row->title = $row->event = trans('plugins/hotel::room.blocked');
                    $row->backgroundColor = '#fe2727';
                    $row->classNames = ['blocked-event'];
                    $row->textColor = '#fe2727';
                    $row->active = 0;
                } else {
                    $row->classNames = ['active-event'];
                    $row->active = 1;
                }

                $allDates[date('Y-m-d', strtotime($row->start_date))] = $row->toArray();
            }
        }

        $bookings = $room->activeBookingRooms;

        if (! empty($bookings)) {
            foreach ($bookings as $booking) {
                for ($i = strtotime($booking->start_date); $i < strtotime($booking->end_date); $i += 60 * 60 * 24) {
                    if (isset($allDates[date('Y-m-d', $i)])) {
                        $allDates[date('Y-m-d', $i)]['number_of_rooms'] -= $booking->number_of_rooms;
                        if ($allDates[date('Y-m-d', $i)]['number_of_rooms'] <= 0) {
                            $allDates[date('Y-m-d', $i)]['active'] = 0;
                            $allDates[date('Y-m-d', $i)]['event'] = trans('plugins/hotel::room.full_book');
                            $allDates[date('Y-m-d', $i)]['title'] = trans('plugins/hotel::room.full_book');
                            $allDates[date('Y-m-d', $i)]['classNames'] = ['full-book-event'];
                            $allDates[date('Y-m-d', $i)]['backgroundColor'] = '#ffc107';
                            $allDates[date('Y-m-d', $i)]['textColor'] = '#000';
                        }
                    }
                }
            }
        }

        $data = array_values($allDates);

        return response()->json($data);
    }

    public function storeRoomAvailability(
        int $id,
        Request $request,
        BaseHttpResponse $response,
        RoomDateInterface $roomDateRepository
    ) {
        $request->validate([
            'start_date' => 'required',
            'end_date' => 'required',
        ]);

        for ($i = strtotime($request->input('start_date')); $i <= strtotime($request->input('end_date')); $i += 60 * 60 * 24) {
            $roomDate = $roomDateRepository->getFirstBy([
                'start_date' => date('Y-m-d', $i),
                'room_id' => $id,
            ]);

            if (empty($roomDate)) {
                $roomDate = $roomDateRepository->getModel();
                $roomDate->room_id = $id;
            }

            $roomDate->fill($request->input());

            $roomDate->start_date = date('Y-m-d H:i:s', $i);
            $roomDate->end_date = date('Y-m-d H:i:s', $i);

            $roomDateRepository->createOrUpdate($roomDate);
        }

        return $response
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function postUpdateOrderBy(RoomUpdateOrderByRequest $request, BaseHttpResponse $response)
    {
        $room = $this->roomRepository->findOrFail($request->input('pk'));
        $room->order = $request->input('value', 0);
        $this->roomRepository->createOrUpdate($room);

        return $response->setMessage(trans('core/base::notices.update_success_message'));
    }
}
