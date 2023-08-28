<?php

namespace Botble\Hotel\Http\Controllers;

use Botble\Base\Facades\Assets;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Hotel\Http\Requests\UpdateSettingsRequest;
use Botble\Hotel\Repositories\Interfaces\CurrencyInterface;
use Botble\Hotel\Services\StoreCurrenciesService;
use Botble\Setting\Supports\SettingStore;
use Botble\Base\Facades\PageTitle;

class HotelController extends BaseController
{
    public function __construct(protected CurrencyInterface $currencyRepository)
    {
    }

    public function getSettings()
    {

        PageTitle::setTitle(trans('plugins/hotel::hotel.settings'));

        Assets::addScripts(['jquery-ui'])
            ->addScriptsDirectly([
                'vendor/core/plugins/hotel/js/currencies.js',
            ])
            ->addStylesDirectly([
                'vendor/core/plugins/hotel/css/currencies.css',
            ]);

        $currencies = $this->currencyRepository
            ->getAllCurrencies()
            ->toArray();

        return view('plugins/hotel::settings.index', compact('currencies'));
    }

    public function postSettings(
        UpdateSettingsRequest $request,
        BaseHttpResponse $response,
        StoreCurrenciesService $service,
        SettingStore $settingStore
    ) {
        foreach ($request->except(['_token', 'currencies', 'deleted_currencies']) as $settingKey => $settingValue) {
            $settingStore->set($settingKey, $settingValue);
        }

        $settingStore->save();

        $currencies = json_decode($request->input('currencies'), true) ?: [];

        if (! $currencies) {
            return $response
                ->setNextUrl(route('hotel.settings'))
                ->setError()
                ->setMessage(trans('plugins/hotel::currency.require_at_least_one_currency'));
        }

        $deletedCurrencies = json_decode($request->input('deleted_currencies', []), true) ?: [];

        $service->execute($currencies, $deletedCurrencies);

        return $response
            ->setNextUrl(route('hotel.settings'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }
}
