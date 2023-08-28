<?php

use Botble\Hotel\Models\BookingAddress;
use Botble\Hotel\Models\Customer;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration
{
    public function up(): void
    {
        $bookingAddresses = BookingAddress::query()
            ->distinct('email')
            ->select([
                'first_name',
                'last_name',
                'email',
                'phone',
                'created_at',
                'updated_at',
            ])
            ->latest()
            ->get();

        if ($bookingAddresses->count()) {
            Customer::query()->insertOrIgnore($bookingAddresses->toArray());
        }
    }
};
