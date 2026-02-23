<?php

namespace Speca\SpecaCore\Http\Controllers\Api\V1;

use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Exception as WriterException;
use Speca\SpecaCore\Enums\GroupActionType;
use Speca\SpecaCore\Export\ExportModel;
use Speca\SpecaCore\Http\Controllers\Controller;
use Speca\SpecaCore\Http\Requests\BasePasswordConfirmationRequest;
use Speca\SpecaCore\Http\Requests\UserPermission\EnableDisableRequest;
use Speca\SpecaCore\Http\Requests\UserPermission\FilterRequest;
use Speca\SpecaCore\Http\Requests\UserPermission\FormRequest;
use Speca\SpecaCore\Http\Requests\UserPermission\GroupActionRequest;
use Speca\SpecaCore\Http\Resources\SendApiResponse;
use Speca\SpecaCore\Models\UserPermission;
use Speca\SpecaCore\Models\UserPermissionCategory;

class UserPermissionController extends Controller
{
    /**
     * The user permission constructor.
     */
    public function __construct()
    {
        // $this->middleware('permission:list-user-permission|show-user-permission|create-user-permission|update-user-permission|enable-disable-user-permission|delete-user-permission|restore-user-permission|force-delete-user-permission', ['only' => ['index']]);
        // $this->middleware('permission:show-user-permission', ['only' => ['show']]);
        // $this->middleware('permission:create-user-permission', ['only' => ['create']]);
        // $this->middleware('permission:update-user-permission', ['only' => ['update']]);
        // $this->middleware('permission:enable-disable-user-permission', ['only' => ['enableOrDisable']]);
        // $this->middleware('permission:group-action-user-permission', ['only' => ['groupAction']]);
        // $this->middleware('permission:export-user-permission', ['only' => ['export']]);
        // $this->middleware('permission:delete-user-permission', ['only' => ['delete']]);
        // $this->middleware('permission:restore-user-permission', ['only' => ['restore']]);
        // $this->middleware('permission:force-delete-user-permission', ['only' => ['forceDelete']]);
    }

    /**
     * User permission list.
     *
     * @param  FilterRequest  $request  The request.
     * @return SendApiResponse The api response.
     */
    public function index(FilterRequest $request): SendApiResponse
    {
        $requestData = $request->validated();
        $userPermissions = self::userPermissionRequest($requestData)
            ->paginate(perPage: $requestData['limit'] ?? 10, page: $requestData['page'] ?? 1);

        $output = [
            'user_permissions' => $userPermissions,
            'total_user_permissions_activated' => UserPermission::whereNotNull('activated_at')->count(),
            'total_user_permissions_disabled' => UserPermission::whereNull('activated_at')->count(),
            'total_user_permissions_archived' => UserPermission::onlyTrashed()->count(),
        ];

        addActivityLog(
            model: UserPermission::getModel(),
            event: 'user-permission-list',
            properties: ['input' => $requestData, 'output' => $output],
            logDescription: __('paydunya-core::activity-log.user-permission.list')
        );

        return new SendApiResponse(
            success: true,
            message: __('paydunya-core::messages.user-permission.list'),
            input: $requestData,
            data: $output,
            statusCode: 200,
        );

    }

    /**
     * User permission details.
     *
     * @param  string  $userPermissionId  The user permission id.
     * @return SendApiResponse The api response.
     */
    public function show(string $userPermissionId): SendApiResponse
    {
        $requestData = ['user_permission_id' => $userPermissionId];

        $userPermission = modelExist(UserPermission::getModel(), $userPermissionId, 'user-permission', 'show', $requestData);
        if ($userPermission instanceof SendApiResponse) {
            return $userPermission;
        }

        $output = getOutput($userPermission);

        addActivityLog(
            model: UserPermission::getModel(),
            event: 'user-permission-show',
            properties: ['input' => $requestData, 'output' => $output],
            logDescription: __('paydunya-core::activity-log.user-permission.show', ['user_permission' => $userPermission->label])
        );

        return new SendApiResponse(
            success: true,
            message: __('paydunya-core::messages.user-permission.show'),
            input: $requestData,
            data: $output,
            statusCode: 200,
        );
    }

    /**
     * Create the user permission.
     *
     * @param  FormRequest  $request  The request.
     * @return SendApiResponse The api response.
     */
    public function create(FormRequest $request): SendApiResponse
    {
        $requestData = $request->validated();
        $userPermission = UserPermission::create($requestData)?->refresh();

        if (! empty($requestData['user_permission_categories'])) {
            $userPermission->userPermissionCategories()->sync($requestData['user_permission_categories']);
        }

        if (! empty($requestData['user_roles'])) {
            $userPermission->roles()->sync($requestData['user_roles']);
        }

        $output = getOutput($userPermission);

        addActivityLog(
            model: UserPermission::getModel(),
            event: 'user-permission-created',
            properties: ['input' => $requestData, 'output' => $output],
            logDescription: __('paydunya-core::activity-log.user-permission.created', ['user_permission' => $userPermission->label])
        );

        return new SendApiResponse(
            success: true,
            message: __('paydunya-core::messages.user-permission.created'),
            input: $requestData,
            data: $output,
            statusCode: 201,
        );
    }

    /**
     * Update the user permission.
     *
     * @param  FormRequest  $request  The request.
     * @param  string  $userPermissionId  The user permission id.
     * @return SendApiResponse The api response.
     */
    public function update(FormRequest $request, string $userPermissionId): SendApiResponse
    {
        $requestData = $request->validated();

        $userPermission = modelExist(UserPermission::getModel(), $userPermissionId, 'user-permission', 'updated', $requestData);
        if ($userPermission instanceof SendApiResponse) {
            return $userPermission;
        }

        $userPermission->update($requestData);

        if (! empty($requestData['user_permission_categories'])) {
            $userPermission->userPermissionCategories()->sync($requestData['user_permission_categories']);
        }

        if (! empty($requestData['user_roles'])) {
            $userPermission->roles()->sync($requestData['user_roles']);
        }

        $output = getOutput($userPermission);

        addActivityLog(
            model: UserPermission::getModel(),
            event: 'user-permission-updated',
            properties: ['input' => $requestData, 'output' => $output],
            logDescription: __('paydunya-core::activity-log.user-permission.updated', ['user_permission' => $userPermission->label])
        );

        return new SendApiResponse(
            success: true,
            message: __('paydunya-core::messages.user-permission.updated'),
            input: $requestData,
            data: $output,
            statusCode: 200,
        );
    }

    /**
     * Enable or disable the user permission.
     *
     * @param  EnableDisableRequest  $request  The request.
     * @param  string  $userPermissionId  The user permission id.
     * @return SendApiResponse The api response.
     */
    public function enableOrDisable(EnableDisableRequest $request, string $userPermissionId): SendApiResponse
    {
        $requestData = $request->validated();

        $userPermission = modelExist(UserPermission::getModel(), $userPermissionId, 'user-permission', ($requestData['new_status'] == 'enabled') ? 'activated' : 'deactivated', $requestData);
        if ($userPermission instanceof SendApiResponse) {
            return $userPermission;
        }

        $oldStatus = (is_null($userPermission->activated_at)) ? 'disabled' : 'enabled';
        $activatedAt = ($requestData['new_status'] == 'enabled') ? now() : null;
        $toDo = ($requestData['new_status'] == 'enabled') ? __('paydunya-core::messages.user-permission.activated') : __('paydunya-core::messages.user-permission.deactivated');

        if ($requestData['new_status'] !== $oldStatus) {
            $userPermission->update(['activated_at' => $activatedAt]);
        }

        $output = getOutput($userPermission);

        addActivityLog(
            model: UserPermission::getModel(),
            event: 'user-permission-'.($activatedAt ? 'activated' : 'deactivated'),
            properties: ['input' => $requestData, 'output' => $output],
            logDescription: __('paydunya-core::activity-log.user-permission.'.($activatedAt ? 'activated' : 'deactivated'), ['user_permission' => $userPermission->label])
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
     * Apply a group action on the user permission.
     *
     * @param  GroupActionRequest  $request  The request.
     * @return SendApiResponse The api response.
     */
    public static function groupAction(GroupActionRequest $request): SendApiResponse
    {
        $requestData = $request->validated();
        $userPermissions = self::userPermissionRequest($requestData);

        if ($userPermissions->count() > 0) {
            match ($requestData['action']) {
                GroupActionType::ACTIVATED->value => $userPermissions->update(['activated_at' => now()]),
                GroupActionType::DEACTIVATED->value => $userPermissions->update(['activated_at' => null]),
                GroupActionType::ARCHIVED->value => $userPermissions->delete(),
                GroupActionType::DELETED->value => $userPermissions->forceDelete(),
                GroupActionType::RESTORED->value => $userPermissions->restore(),
                // Todo : A mettre a jour.
                GroupActionType::UPDATED->value => $userPermissions->update(),
            };

            addActivityLog(
                model: UserPermission::getModel(),
                event: 'user-permission-group-action-'.strtolower($requestData['action']),
                properties: ['input' => $requestData, 'output' => $userPermissions->get()->toArray()],
                logDescription: __('paydunya-core::activity-log.user-permission.group-action', ['action' => GroupActionType::from($requestData['action'])->label()])
            );

            return new SendApiResponse(
                success: true,
                message: __('paydunya-core::messages.user-permission.group-action', ['action' => GroupActionType::from($requestData['action'])->label()]),
                input: $requestData,
                data: $userPermissions->get()->toArray(),
                statusCode: 200,
            );
        } else {
            addActivityLog(
                model: UserPermission::getModel(),
                event: 'user-permission-group-action-'.strtolower($requestData['action']).'-attempt',
                properties: ['input' => $requestData, 'output' => $userPermissions->get()->toArray()],
                logDescription: __('paydunya-core::activity-log.user-permission.group-action-attempt', ['action' => GroupActionType::from($requestData['action'])->label()])
            );

            return new SendApiResponse(
                success: false,
                message: __('paydunya-core::messages.user-permission.group-action-attempt', ['action' => GroupActionType::from($requestData['action'])->label()]),
                input: $requestData,
                data: $userPermissions->get()->toArray(),
                statusCode: 200,
            );
        }
    }

    /**
     * Export the user permissions.
     *
     * @param  FilterRequest  $request  The request.
     * @return SendApiResponse The api response.
     *
     * @throws Exception | WriterException The exception.
     */
    public static function export(FilterRequest $request): SendApiResponse
    {
        $requestData = $request->validated();
        $columnsName = ['label', 'created_at'];
        $columnsLabel = ['Nom', 'Date de création', 'Statut'];
        $userPermissions = self::userPermissionRequest($requestData)->get($columnsName)->toArray();

        Excel::store(new ExportModel($userPermissions, $columnsLabel), 'permissions/permissions_export'.now()->format('Y-m-d').'.xlsx', 'public');
        // Todo : Mettre en place l'exportation sur le space de Digital Ocean (S3).
        // Excel::store(new ExportModel($userPermissions, $columnsLabel), 'permissions/permissions_export' . now()->format('Y-m-d') . '.xlsx', 'public');

        addActivityLog(
            model: UserPermissionCategory::getModel(),
            event: 'user-permission-exported',
            properties: ['input' => $requestData, 'output' => $userPermissions],
            logDescription: __('paydunya-core::activity-log.user-permission.exported')
        );

        return new SendApiResponse(
            success: true,
            message: __('paydunya-core::messages.user-permission.exported'),
            input: $requestData,
            data: $userPermissions,
            statusCode: 200,
        );
    }

    /**
     * Delete the user permission.
     *
     * @param  BasePasswordConfirmationRequest  $request  The request.
     * @param  string  $userPermissionId  The user permission id.
     * @return SendApiResponse The api response.
     */
    public function delete(BasePasswordConfirmationRequest $request, string $userPermissionId): SendApiResponse
    {
        $requestData = ['user_permission_id' => $userPermissionId];

        $confirmation = passwordConfirmation(UserPermissionCategory::getModel(), $request->get('password'), 'archived', $requestData);

        if ($confirmation instanceof SendApiResponse) {
            return $confirmation;
        }

        $userPermission = modelExist(UserPermission::getModel(), $userPermissionId, 'user-permission', 'archived', $requestData);
        if ($userPermission instanceof SendApiResponse) {
            return $userPermission;
        }

        $userPermission->delete();

        $output = getOutput($userPermission);

        addActivityLog(
            model: UserPermission::getModel(),
            event: 'user-permission-archived',
            properties: ['input' => $requestData, 'output' => $output],
            logDescription: __('paydunya-core::activity-log.user-permission.archived', ['user_permission' => $userPermission->label])
        );

        return new SendApiResponse(
            success: true,
            message: __('paydunya-core::messages.user-permission.archived'),
            input: $requestData,
            data: $output,
            statusCode: 200,
        );
    }

    /**
     * Restore the user permission.
     *
     * @param  string  $userPermissionId  The user permission id.
     * @return SendApiResponse The api response.
     */
    public function restore(string $userPermissionId): SendApiResponse
    {
        $requestData = ['user_permission_id' => $userPermissionId];

        $userPermission = modelExist(UserPermission::getModel(), $userPermissionId, 'user-permission', 'restored', $requestData, true);
        if ($userPermission instanceof SendApiResponse) {
            return $userPermission;
        }

        $userPermission->restore();

        $output = getOutput($userPermission);

        addActivityLog(
            model: UserPermission::getModel(),
            event: 'user-permission-restored',
            properties: ['input' => $requestData, 'output' => $output],
            logDescription: __('paydunya-core::activity-log.user-permission.restored', ['user_permission' => $userPermission->label])
        );

        return new SendApiResponse(
            success: true,
            message: __('paydunya-core::messages.user-permission.restored'),
            input: $requestData,
            data: $output,
            statusCode: 200,
        );
    }

    /**
     * Force delete the user permission.
     *
     * @param  BasePasswordConfirmationRequest  $request  The request.
     * @param  string  $userPermissionId  The user permission id.
     * @return SendApiResponse The api response.
     */
    public function forceDelete(BasePasswordConfirmationRequest $request, string $userPermissionId): SendApiResponse
    {
        $requestData = ['user_permission_id' => $userPermissionId];

        $confirmation = passwordConfirmation(UserPermissionCategory::getModel(), $request->get('password'), 'deleted', $requestData);

        if ($confirmation instanceof SendApiResponse) {
            return $confirmation;
        }

        $userPermission = modelExist(UserPermission::getModel(), $userPermissionId, 'user-permission', 'deleted', $requestData, true);
        if ($userPermission instanceof SendApiResponse) {
            return $userPermission;
        }

        $userPermission->forceDelete();

        $output = getOutput($userPermission);

        addActivityLog(
            model: UserPermission::getModel(),
            event: 'user-permission-deleted',
            properties: ['input' => $requestData, 'output' => $output],
            logDescription: __('paydunya-core::activity-log.user-permission.deleted', ['user_permission' => $userPermission->label])
        );

        return new SendApiResponse(
            success: true,
            message: __('paydunya-core::messages.user-permission.deleted'),
            input: $requestData,
            data: $output,
            statusCode: 200,
        );
    }

    /**
     * User permission request.
     *
     * @param  array  $requestData  The request data.
     * @return mixed The user permission request.
     */
    public static function userPermissionRequest(array $requestData = []): mixed
    {
        return UserPermission::with('userPermissionCategories')
            ->when($requestData['user_permission_id'] ?? '', fn ($q) => $q->where('id', $requestData['user_permission_id']))
            ->when($requestData['label'] ?? '', fn ($q) => $q->where('label', 'like', '%'.$requestData['label'].'%'))
            ->when($requestData['name'] ?? '', fn ($q) => $q->where('name', 'like', '%'.$requestData['name'].'%'))
            ->when($requestData['guard_name'] ?? '', fn ($q) => $q->where('guard_name', $requestData['guard_name']))
            ->when($requestData['description'] ?? '', fn ($q) => $q->where('description', 'like', '%'.$requestData['description'].'%'))
            ->when(array_key_exists('activated', $requestData), fn ($q) => $requestData['activated'] ? $q->whereNotNull('activated_at') : $q->whereNull('activated_at'))
            ->when(array_key_exists('archived', $requestData), fn ($q) => $requestData['archived'] ? $q->onlyTrashed() : $q->withTrashed())
            ->when($requestData['search'] ?? '', function ($q) use ($requestData) {
                $q->where(function ($subQuery) use ($requestData) {
                    $subQuery->where('label', 'like', '%'.$requestData['search'].'%')
                        ->orWhere('name', 'like', '%'.$requestData['search'].'%')
                        ->orWhere('description', 'like', '%'.$requestData['search'].'%');
                });
            })
            ->when($requestData['check'] ?? '', fn ($q) => $q->whereIn('id', $requestData['check']))
            ->when($requestData['uncheck'] ?? '', fn ($q) => $q->whereNotIn('id', $requestData['uncheck']))
            ->latest();
    }
}
