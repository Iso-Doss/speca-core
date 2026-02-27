<?php

namespace Speca\SpecaCore\Http\Controllers\Api\V1;

use Speca\SpecaCore\Http\Controllers\Controller;
use Speca\SpecaCore\Http\Requests\Country\EnableDisableRequest;
use Speca\SpecaCore\Http\Requests\Country\FilterRequest;
use Speca\SpecaCore\Http\Requests\Country\FormRequest;
use Speca\SpecaCore\Http\Resources\SendApiResponse;
use Speca\SpecaCore\Models\Country;


class CountryController extends Controller
{
    /**
     * The country constructor.
     */
    public function __construct()
    {
        //$this->middleware('permission:list-country-category|show-country-category|create-country-category|update-country-category|enable-disable-country-category|delete-country-category|restore-country-category|force-delete-country-category', ['only' => ['index']]);
        //$this->middleware('permission:show-country-category', ['only' => ['show']]);
        //$this->middleware('permission:create-country-category', ['only' => ['create']]);
        //$this->middleware('permission:update-country-category', ['only' => ['update']]);
        //$this->middleware('permission:enable-disable-country-category', ['only' => ['enableOrDisable']]);
        //$this->middleware('permission:delete-country-category', ['only' => ['delete']]);
        //$this->middleware('permission:restore-country-category', ['only' => ['restore']]);
        //$this->middleware('permission:force-delete-country-category', ['only' => ['forceDelete']]);
    }

    /**
     * Country list.
     *
     * @param FilterRequest $request The request.
     * @return SendApiResponse The api response.
     */
    public static function index(FilterRequest $request): SendApiResponse
    {
        $requestData = $request->validated();
        $userPermissionCategories = self::countryRequest($requestData)
            ->paginate(perPage: $requestData['limit'] ?? 10, page: $requestData['page'] ?? 1);

        $output = [
            'countries' => $userPermissionCategories,
            'total_countries_activated' => Country::whereNotNull('activated_at')->count(),
            'total_countries_disabled' => Country::whereNull('activated_at')->count(),
            'total_countries_archived' => Country::onlyTrashed()->count(),
        ];
        $userPermissionCategories->count();

        addActivityLog(
            model: Country::getModel(),
            event: 'country-list',
            properties: ['input' => $requestData, 'output' => $output],
            logDescription: __('speca-core::activity-log.country.list')
        );

        return new SendApiResponse(
            success: true,
            message: __('speca-core::messages.country.list'),
            input: $requestData,
            data: $output,
            statusCode: 200,
        );
    }

    /**
     * Country details.
     *
     * @param string $countryId The country id.
     * @return SendApiResponse The api response.
     */
    public static function show(string $countryId): SendApiResponse
    {
        $requestData = ['id' => $countryId];

        $country = modelExist(Country::getModel(), $countryId, 'country', 'show', $requestData);

        $output = getOutput($country);

        addActivityLog(
            model: Country::getModel(),
            event: 'country-show',
            properties: ['input' => $requestData, 'output' => $output],
            logDescription: __('speca-core::activity-log.country.show')
        );

        return new SendApiResponse(
            success: true,
            message: __('speca-core::messages.country.show'),
            input: $requestData,
            data: $output,
            statusCode: 200,
        );
    }

    /**
     * Create the country.
     *
     * @param FormRequest $request The request.
     * @return SendApiResponse The api response.
     */
    public static function create(FormRequest $request): SendApiResponse
    {
        $requestData = $request->validated();

        $country = Country::create($requestData)?->refresh();

        $output = getOutput($country);

        addActivityLog(
            model: Country::getModel(),
            event: 'country-created',
            properties: ['input' => $requestData, 'output' => $output],
            logDescription: __('speca-core::activity-log.country.created')
        );

        return new SendApiResponse(
            success: true,
            message: __('speca-core::messages.country.created'),
            input: $requestData,
            data: $output,
            statusCode: 201,
        );
    }

    /**
     * Update the country.
     *
     * @param FormRequest $request The request.
     * @param string $countryId The country id.
     * @return SendApiResponse The api response.
     */
    public static function update(FormRequest $request, string $countryId): SendApiResponse
    {
        $requestData = $request->validated();

        $country = modelExist(Country::getModel(), $countryId, 'country', 'updated', $requestData);
        if ($country instanceof SendApiResponse) {
            return $country;
        }

        $country->update($requestData);
        if (!empty($requestData['user_permissions'])) {
            $country->userPermissions()->sync($requestData['user_permissions']);
        }

        $output = getOutput($country);

        addActivityLog(
            model: Country::getModel(),
            event: 'country-updated',
            properties: ['input' => $requestData, 'output' => $output],
            logDescription: __('speca-core::activity-log.country.updated')
        );

        return new SendApiResponse(
            success: true,
            message: __('speca-core::messages.country.updated'),
            input: $requestData,
            data: $output,
            statusCode: 200,
        );
    }

    /**
     * Enable or disable the country.
     *
     * @param EnableDisableRequest $request The request.
     * @param string $countryId The country id.
     * @return SendApiResponse The api response.
     */
    public static function enableOrDisable(EnableDisableRequest $request, string $countryId): SendApiResponse
    {
        $requestData = $request->validated();

        $country = modelExist(Country::getModel(), $countryId, 'country', ('enabled' == $requestData['new_status']) ? 'activated' : 'deactivated', $requestData);
        if ($country instanceof SendApiResponse) {
            return $country;
        }

        $oldStatus = (is_null($country->activated_at)) ? 'disabled' : 'enabled';
        $activatedAt = ('enabled' == $requestData['new_status']) ? now() : null;
        $toDo = ('enabled' == $requestData['new_status']) ? __('speca-core::messages.country.activated') : __('speca-core::messages.country.deactivated');

        if ($requestData['new_status'] !== $oldStatus) {
            $country->update(['activated_at' => $activatedAt]);
        }

        $output = getOutput($country);

        addActivityLog(
            model: Country::getModel(),
            event: 'country-' . ($activatedAt ? 'activated' : 'deactivated'),
            properties: ['input' => $requestData, 'output' => $output],
            logDescription: __('speca-core::activity-log.country.' . ($activatedAt ? 'activated' : 'deactivated'))
        );

        return new SendApiResponse(
            success: true,
            message: $toDo,
            input: $requestData,
            data: $output,
            statusCode: 200,
        );
    }

    /**
     * Delete the country.
     *
     * @param string $countryId The country id.
     * @return SendApiResponse The api response.
     */
    public static function delete(string $countryId): SendApiResponse
    {
        // Todo : Mettre en place une request pour récupérer le mot de passe de l'utilisateur qui souhaite effectuer l'action.
        // Todo : Mettre en place la verification du mot de passe de l'utilisateur qui souhaite effectuer l'action avant d'exécuter l'action.
        $requestData = ['id' => $countryId];

        $country = modelExist(Country::getModel(), $countryId, 'country', 'archived', $requestData);
        if ($country instanceof SendApiResponse) {
            return $country;
        }

        $country->delete();

        $output = getOutput($country);

        addActivityLog(
            model: Country::getModel(),
            event: 'country-archived',
            properties: ['input' => $requestData, 'output' => $output],
            logDescription: __('speca-core::activity-log.country.archived')
        );

        return new SendApiResponse(
            success: true,
            message: __('speca-core::messages.country.archived'),
            input: $requestData,
            data: $output,
            statusCode: 200,
        );
    }

    /**
     * Restore the country.
     *
     * @param string $countryId The country id.
     * @return SendApiResponse The api response.
     */
    public static function restore(string $countryId): SendApiResponse
    {
        $requestData = ['id' => $countryId];

        $country = modelExist(Country::getModel(), $countryId, 'country', 'restore', $requestData, true);
        if ($country instanceof SendApiResponse) {
            return $country;
        }

        $country->restore();

        $output = getOutput($country);

        addActivityLog(
            model: Country::getModel(),
            event: 'country-restore',
            properties: ['input' => $requestData, 'output' => $output],
            logDescription: __('speca-core::activity-log.country.restore')
        );

        return new SendApiResponse(
            success: true,
            message: __('speca-core::messages.country.restore'),
            input: $requestData,
            data: $output,
            statusCode: 200,
        );
    }

    /**
     * Force delete the country.
     *
     * @param string $countryId The country id.
     * @return SendApiResponse The api response.
     */
    public static function forceDelete(string $countryId): SendApiResponse
    {
        // Todo : Mettre en place une request pour récupérer le mot de passe de l'utilisateur qui souhaite effectuer l'action.
        // Todo : Mettre en place la verification du mot de passe de l'utilisateur qui souhaite effectuer l'action avant d'exécuter l'action.
        $requestData = ['id' => $countryId];

        $country = modelExist(Country::getModel(), $countryId, 'country', 'delete', $requestData, true);
        if ($country instanceof SendApiResponse) {
            return $country;
        }

        $country->forceDelete();

        $output = getOutput($country);

        addActivityLog(
            model: Country::getModel(),
            event: 'country-delete',
            properties: ['input' => $requestData, 'output' => $output],
            logDescription: __('speca-core::activity-log.country.delete')
        );

        return new SendApiResponse(
            success: true,
            message: __('speca-core::messages.country.delete'),
            input: $requestData,
            data: $output,
            statusCode: 200,
        );
    }

    /**
     * Country request.
     *
     * @param array $requestData The request data.
     * @return mixed The country request.
     */
    public static function countryRequest(array $requestData = []): mixed
    {
        return Country::with('userPermissions')
            ->when($requestData['country_id'] ?? '', fn($q) => $q->where('id', $requestData['country_id']))
            ->when($requestData['name'] ?? '', fn($q) => $q->where('name', 'like', '%' . $requestData['name'] . '%'))
            ->when($requestData['code'] ?? '', fn($q) => $q->where('code', 'like', '%' . $requestData['code'] . '%'))
            ->when($requestData['iso_code'] ?? '', fn($q) => $q->where('iso_code', 'like', '%' . $requestData['iso_code'] . '%'))
            ->when($requestData['phone_code'] ?? '', fn($q) => $q->where('iso_code', 'like', '%' . $requestData['phone_code'] . '%'))
            ->when($requestData['flag'] ?? '', fn($q) => $q->where('flag', 'like', '%' . $requestData['flag'] . '%'))
            ->when($requestData['search'] ?? '', function ($q) use ($requestData) {
                $q->where(function ($subQuery) use ($requestData) {
                    $subQuery->where('name', 'like', '%' . $requestData['name'] . '%')
                        ->orWhere('code', 'like', '%' . $requestData['code'] . '%')
                        ->orWhere('iso_code', 'like', '%' . $requestData['iso_code'] . '%')
                        ->orWhere('phone_code', 'like', '%' . $requestData['phone_code'] . '%');
                });
            })
            ->when($requestData['check'] ?? '', fn($q) => $q->whereIn('id', $requestData['check']))
            ->when($requestData['uncheck'] ?? '', fn($q) => $q->whereNotIn('id', $requestData['uncheck']))
            ->latest();
    }
}
