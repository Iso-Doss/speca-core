<?php

namespace Speca\SpecaCore\Http\Controllers\Api\V1;


use Maatwebsite\Excel\Facades\Excel;
use Speca\SpecaCore\Enums\GroupActionType;
use Speca\SpecaCore\Export\ExportModel;
use Speca\SpecaCore\Http\Controllers\Controller;
use Speca\SpecaCore\Http\Requests\BasePasswordConfirmationRequest;
use Speca\SpecaCore\Http\Requests\UserProfile\EnableDisableRequest;
use Speca\SpecaCore\Http\Requests\UserProfile\FilterRequest;
use Speca\SpecaCore\Http\Requests\UserProfile\FormRequest;
use Speca\SpecaCore\Http\Requests\UserProfile\GroupActionRequest;
use Speca\SpecaCore\Http\Resources\SendApiResponse;
use Speca\SpecaCore\Models\UserProfile;

class UserProfileController extends Controller
{
    /**
     * The user constructor.
     */
    public function __construct()
    {
        //$this->middleware('permission:list-user-profile|show-user-profile|create-user-profile|update-user-profile|enable-disable-user-profile|delete-user-profile|restore-user-profile|force-delete-user-profile', ['only' => ['index']]);
        //$this->middleware('permission:show-user-profile', ['only' => ['show']]);
        //$this->middleware('permission:create-user-profile', ['only' => ['create']]);
        //$this->middleware('permission:update-user-profile', ['only' => ['update']]);
        //$this->middleware('permission:enable-disable-user-profile', ['only' => ['enableOrDisable']]);
        //$this->middleware('permission:group-action-user-profile', ['only' => ['groupAction']]);
        //$this->middleware('permission:export-user-profile', ['only' => ['export']]);
        //$this->middleware('permission:delete-user-profile', ['only' => ['delete']]);
        //$this->middleware('permission:restore-user-profile', ['only' => ['restore']]);
        //$this->middleware('permission:force-delete-user-profile', ['only' => ['forceDelete']]);
    }

    /**
     * User profile list.
     *
     * @param FilterRequest $request The request.
     * @return SendApiResponse The api response.
     */
    public function index(FilterRequest $request): SendApiResponse
    {
        $requestData = $request->validated();

        $users = self::userProfileRequest($requestData)
            ->paginate(perPage: $requestData['limit'] ?? 10, page: $requestData['page'] ?? 1);

        $output = [
            'user_profiles' => $users,
            'total_user_profiles_activated' => UserProfile::whereNotNull('activated_at')->count(),
            'total_user_profiles_disabled' => UserProfile::whereNull('activated_at')->count(),
            'total_user_profiles_archived' => UserProfile::onlyTrashed()->count(),
        ];

        addActivityLog(
            model: UserProfile::getModel(),
            event: 'user-profile-list',
            properties: ['input' => $requestData, 'output' => $output],
            logDescription: __('speca-core::activity-log.user-profile.list')
        );

        return new SendApiResponse(
            success: true,
            message: __('speca-core::messages.user-profile.list'),
            input: $requestData,
            data: $output,
            statusCode: 200,
        );
    }

    /**
     * User profile details.
     *
     * @param string $userProfileId The user id.
     * @return SendApiResponse The api response.
     */
    public function show(string $userProfileId): SendApiResponse
    {
        $requestData = ['user_profile_id' => $userProfileId];

        $userProfile = modelExist(UserProfile::getModel(), $userProfileId, 'user-profile', 'show', $requestData);
        if ($userProfile instanceof SendApiResponse) {
            return $userProfile;
        }

        $output = getOutput($userProfile);

        addActivityLog(
            model: UserProfile::getModel(),
            event: 'user-profile-show',
            properties: ['input' => $requestData, 'output' => $output],
            logDescription: __('speca-core::activity-log.user-profile.show', ['name' => $userProfile->name])
        );

        return new SendApiResponse(
            success: true,
            message: __('speca-core::messages.user-profile.show'),
            input: $requestData,
            data: $output,
            statusCode: 200,
        );
    }

    /**
     * Create the user profile.
     *
     * @param FormRequest $request The request.
     * @return SendApiResponse The api response.
     */
    public function create(FormRequest $request): SendApiResponse
    {
        $requestData = $request->validated();
        $userProfile = UserProfile::create($requestData)?->refresh();

        $output = getOutput($userProfile);

        addActivityLog(
            model: UserProfile::getModel(),
            event: 'user-profile-created',
            properties: ['input' => $requestData, 'output' => $output],
            logDescription: __('speca-core::activity-log.user-profile.created', ['name' => $userProfile->name])
        );

        return new SendApiResponse(
            success: true,
            message: __('speca-core::messages.user-profile.created'),
            input: $requestData,
            data: $output,
            statusCode: 201,
        );
    }


    /**
     * Update the user profile.
     *
     * @param FormRequest $request The request.
     * @param string $userProfileId The user profile id.
     * @return SendApiResponse The api response.
     */
    public function update(FormRequest $request, string $userProfileId): SendApiResponse
    {
        $requestData = $request->validated();

        $userProfile = modelExist(UserProfile::getModel(), $userProfileId, 'user-profile', 'updated', $requestData);
        if ($userProfile instanceof SendApiResponse) {
            return $userProfile;
        }

        $userProfile->update($requestData);

        $output = getOutput($userProfile);

        addActivityLog(
            model: UserProfile::getModel(),
            event: 'user-profile-updated',
            properties: ['input' => $requestData, 'output' => $output],
            logDescription: __('speca-core::activity-log.user-profile.updated', ['name' => $userProfile->name])
        );

        return new SendApiResponse(
            success: true,
            message: __('speca-core::messages.user-profile.updated'),
            input: $requestData,
            data: $output,
            statusCode: 200,
        );
    }

    /**
     * Enable or disable the user profile.
     *
     * @param EnableDisableRequest $request The request.
     * @param string $userProfileId The user profile id.
     * @return SendApiResponse The api response.
     */
    public function enableOrDisable(EnableDisableRequest $request, string $userProfileId): SendApiResponse
    {
        $requestData = $request->validated();

        $userProfile = modelExist(UserProfile::getModel(), $userProfileId, 'user-profile', ('enabled' == $requestData['new_status']) ? 'activated' : 'deactivated', $requestData);
        if ($userProfile instanceof SendApiResponse) {
            return $userProfile;
        }

        $oldStatus = (is_null($userProfile->activated_at)) ? 'disabled' : 'enabled';
        $activatedAt = ('enabled' == $requestData['new_status']) ? now() : null;
        $toDo = ('enabled' == $requestData['new_status']) ? __('speca-core::messages.user-profile.activated') : __('speca-core::messages.user-profile.deactivated');

        if ($requestData['new_status'] !== $oldStatus) {
            $userProfile->update(['activated_at' => $activatedAt]);
        }

        $output = getOutput($userProfile);

        addActivityLog(
            model: UserProfile::getModel(),
            event: 'user-' . ($activatedAt ? 'activated' : 'deactivated'),
            properties: ['input' => $requestData, 'output' => $output],
            logDescription: __('speca-core::activity-log.user-profile.' . ($activatedAt ? 'activated' : 'deactivated'), ['name' => $userProfile->name])
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
     * Apply a group action on the user profile.
     *
     * @param GroupActionRequest $request The request.
     * @return SendApiResponse The api response.
     */
    public static function groupAction(GroupActionRequest $request): SendApiResponse
    {
        $requestData = $request->validated();
        $userProfiles = self::userProfileRequest($requestData);

        if ($userProfiles->count() > 0) {
            match ($requestData['action']) {
                GroupActionType::ACTIVATED->value => $userProfiles->update(['activated_at' => now()]),
                GroupActionType::DEACTIVATED->value => $userProfiles->update(['activated_at' => null]),
                GroupActionType::ARCHIVED->value => $userProfiles->delete(),
                GroupActionType::DELETED->value => $userProfiles->forceDelete(),
                GroupActionType::RESTORED->value => $userProfiles->restore(),
                // Todo : A mettre a jour.
                GroupActionType::UPDATED->value => $userProfiles->update(),
            };

            addActivityLog(
                model: UserProfile::getModel(),
                event: 'user-group-action-' . strtolower($requestData['action']),
                properties: ['input' => $requestData, 'output' => $userProfiles->get()->toArray()],
                logDescription: __('speca-core::activity-log.user-profile.group-action', ['action' => GroupActionType::from($requestData['action'])->label()])
            );

            return new SendApiResponse(
                success: true,
                message: __('speca-core::messages.user-profile.group-action', ['action' => GroupActionType::from($requestData['action'])->label()]),
                input: $requestData,
                data: $userProfiles->get()->toArray(),
                statusCode: 200,
            );
        } else {
            addActivityLog(
                model: UserProfile::getModel(),
                event: 'user-group-action-' . strtolower($requestData['action']) . '-attempt',
                properties: ['input' => $requestData, 'output' => $userProfiles->get()->toArray()],
                logDescription: __('speca-core::activity-log.user-profile.group-action-attempt', ['action' => GroupActionType::from($requestData['action'])->label()])
            );

            return new SendApiResponse(
                success: false,
                message: __('speca-core::messages.user-profile.group-action-attempt', ['action' => GroupActionType::from($requestData['action'])->label()]),
                input: $requestData,
                data: $userProfiles->get()->toArray(),
                statusCode: 200,
            );
        }
    }

    /**
     * Export the user profile.
     *
     * @param FilterRequest $request The request.
     * @return SendApiResponse The api response.
     */
    public static function export(FilterRequest $request): SendApiResponse
    {
        $requestData = $request->validated();
        $columnsName = ['name', 'description', 'created_at'];
        $columnsLabel = ['Nom complet', 'Description', 'Date de création'];
        $users = self::userProfileRequest($requestData)->get($columnsName)->toArray();

        Excel::store(new ExportModel($users, $columnsLabel), 'user-profiles/users-profiles_export' . now()->format('Y-m-d') . '.xlsx', 'public');
        // Todo : Mettre en place l'exportation sur le space de Digital Ocean (S3).
        // Excel::store(new ExportModel($users, $columnsLabel), 'user-profiles/users-profiles_export' . now()->format('Y-m-d') . '.xlsx', 'public');

        addActivityLog(
            model: UserProfile::getModel(),
            event: 'user-profile-exported',
            properties: ['input' => $requestData, 'output' => $users],
            logDescription: __('speca-core::activity-log.user-profile.exported')
        );

        return new SendApiResponse(
            success: true,
            message: __('speca-core::messages.user-profile.exported'),
            input: $requestData,
            data: $users,
            statusCode: 200,
        );
    }

    /**
     * Delete the user profile.
     *
     * @param BasePasswordConfirmationRequest $request The request.
     * @param string $userProfileId The user profile id.
     * @return SendApiResponse The api response.
     */
    public function delete(BasePasswordConfirmationRequest $request, string $userProfileId): SendApiResponse
    {
        $requestData = ['user_profile_id' => $userProfileId];

        $confirmation = passwordConfirmation(UserProfile::getModel(), $request->get('password'), 'archived', $requestData);

        if ($confirmation instanceof SendApiResponse) {
            return $confirmation;
        }

        $userProfile = modelExist(UserProfile::getModel(), $userProfileId, 'user-profile', 'archived', $requestData);
        if ($userProfile instanceof SendApiResponse) {
            return $userProfile;
        }

        $userProfile->delete();

        $output = getOutput($userProfile);

        addActivityLog(
            model: UserProfile::getModel(),
            event: 'user-archived',
            properties: ['input' => $requestData, 'output' => $output],
            logDescription: __('speca-core::activity-log.user-profile.archived', ['name' => $userProfile->name])
        );

        return new SendApiResponse(
            success: true,
            message: __('speca-core::messages.user-profile.archived'),
            input: $requestData,
            data: $output,
            statusCode: 200,
        );
    }

    /**
     * Restore the user profile.
     *
     * @param string $userProfileId The user profile id.
     * @return SendApiResponse The api response.
     */
    public function restore(string $userProfileId): SendApiResponse
    {
        $requestData = ['user_profile_id' => $userProfileId];

        $userProfile = modelExist(UserProfile::getModel(), $userProfileId, 'user-profile', 'restored', $requestData, true);
        if ($userProfile instanceof SendApiResponse) {
            return $userProfile;
        }

        $userProfile->restore();

        $output = getOutput($userProfile);

        addActivityLog(
            model: UserProfile::getModel(),
            event: 'user-profile-restored',
            properties: ['input' => $requestData, 'output' => $output],
            logDescription: __('speca-core::activity-log.user-profile.restored', ['name' => $userProfile->name])
        );

        return new SendApiResponse(
            success: true,
            message: __('speca-core::messages.user-profile.restored'),
            input: $requestData,
            data: $output,
            statusCode: 200,
        );
    }

    /**
     * Force delete the user profile.
     *
     * @param BasePasswordConfirmationRequest $request The request.
     * @param string $userProfileId The user id.
     * @return SendApiResponse The api response.
     */
    public function forceDelete(BasePasswordConfirmationRequest $request, string $userProfileId): SendApiResponse
    {
        $requestData = ['user_profile_id' => $userProfileId];

        $confirmation = passwordConfirmation(UserProfile::getModel(), $request->get('password'), 'deleted', $requestData);

        if ($confirmation instanceof SendApiResponse) {
            return $confirmation;
        }

        $userProfile = modelExist(UserProfile::getModel(), $userProfileId, 'user', 'deleted', $requestData, true);
        if ($userProfile instanceof SendApiResponse) {
            return $userProfile;
        }

        $userProfile->forceDelete();

        $output = getOutput($userProfile);

        addActivityLog(
            model: UserProfile::getModel(),
            event: 'user-profile-deleted',
            properties: ['input' => $requestData, 'output' => $output],
            logDescription: __('speca-core::activity-log.user-profile.deleted', ['name' => $userProfile->name])
        );

        return new SendApiResponse(
            success: true,
            message: __('speca-core::messages.user-profile.deleted'),
            input: $requestData,
            data: $output,
            statusCode: 200,
        );
    }

    /**
     * User profile request.
     *
     * @param array $requestData The request data.
     * @return mixed The user profile request.
     */
    public static function userProfileRequest(array $requestData = []): mixed
    {
        return UserProfile::when($requestData['user_profile_id'] ?? '', fn($q) => $q->where('id', $requestData['user_profile_id']))
            ->when($requestData['name'] ?? '', fn($q) => $q->where('name', 'like', '%' . $requestData['name'] . '%'))
            ->when($requestData['description'] ?? '', fn($q) => $q->where('description', 'like', '%' . $requestData['email'] . '%'))
            ->when($requestData['check'] ?? '', fn($q) => $q->whereIn('id', $requestData['check']))
            ->when($requestData['uncheck'] ?? '', fn($q) => $q->whereNotIn('id', $requestData['uncheck']))
            ->latest();
    }
}
