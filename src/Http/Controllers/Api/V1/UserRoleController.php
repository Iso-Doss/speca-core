<?php

namespace Speca\SpecaCore\Http\Controllers\Api\V1;

use Maatwebsite\Excel\Facades\Excel;
use Speca\SpecaCore\Enums\GroupActionType;
use Speca\SpecaCore\Export\ExportModel;
use Speca\SpecaCore\Http\Controllers\Controller;
use Speca\SpecaCore\Http\Requests\BasePasswordConfirmationRequest;
use Speca\SpecaCore\Http\Requests\UserRole\EnableDisableRequest;
use Speca\SpecaCore\Http\Requests\UserRole\FilterRequest;
use Speca\SpecaCore\Http\Requests\UserRole\FormRequest;
use Speca\SpecaCore\Http\Requests\UserRole\GroupActionRequest;
use Speca\SpecaCore\Http\Resources\SendApiResponse;
use Speca\SpecaCore\Models\UserRole;

class UserRoleController extends Controller
{
    /**
     * The user role constructor.
     */
    public function __construct()
    {
        // $this->middleware('permission:list-user-role|show-user-role|create-user-role|update-user-role|enable-disable-user-role|delete-user-role|restore-user-role|force-delete-user-role', ['only' => ['index']]);
        // $this->middleware('permission:show-user-role', ['only' => ['show']]);
        // $this->middleware('permission:create-user-role', ['only' => ['create']]);
        // $this->middleware('permission:update-user-role', ['only' => ['update']]);
        // $this->middleware('permission:enable-disable-user-role', ['only' => ['enableOrDisable']]);
        // $this->middleware('permission:group-action-user-role', ['only' => ['groupAction']]);
        // $this->middleware('permission:export-user-role', ['only' => ['export']]);
        // $this->middleware('permission:delete-user-role', ['only' => ['delete']]);
        // $this->middleware('permission:restore-user-role', ['only' => ['restore']]);
        // $this->middleware('permission:force-delete-user-role', ['only' => ['forceDelete']]);
    }

    /**
     * User role list.
     *
     * @param  FilterRequest  $request  The request.
     * @return SendApiResponse The api response.
     */
    public function index(FilterRequest $request): SendApiResponse
    {
        $requestData = $request->validated();

        $userRoles = self::userRoleRequest($requestData)
            ->paginate(perPage: $requestData['limit'] ?? 10, page: $requestData['page'] ?? 1);

        $output = [
            'user_roles' => $userRoles,
            'total_user_roles_activated' => UserRole::whereNotNull('activated_at')->count(),
            'total_user_user_roles_disabled' => UserRole::whereNull('activated_at')->count(),
            'total_user_user_roles_archived' => UserRole::onlyTrashed()->count(),
        ];

        addActivityLog(
            model: UserRole::getModel(),
            event: 'user-role-list',
            properties: ['input' => $requestData, 'output' => $output],
            logDescription: __('paydunya-core::activity-log.user-role.list')
        );

        return new SendApiResponse(
            success: true,
            message: __('paydunya-core::messages.user-role.list'),
            input: $requestData,
            data: $output,
            statusCode: 200,
        );
    }

    /**
     * User role details.
     *
     * @param  string  $userRoleId  The user role id.
     * @return SendApiResponse The api response.
     */
    public function show(string $userRoleId): SendApiResponse
    {
        $requestData = ['user_role_id' => $userRoleId];

        $userRole = modelExist(UserRole::getModel(), $userRoleId, 'user-role', 'show', $requestData);
        if ($userRole instanceof SendApiResponse) {
            return $userRole;
        }

        $output = getOutput($userRole);

        addActivityLog(
            model: UserRole::getModel(),
            event: 'user-role-show',
            properties: ['input' => $requestData, 'output' => $output],
            logDescription: __('paydunya-core::activity-log.user-role.show', ['user_role' => $userRole->label])
        );

        return new SendApiResponse(
            success: true,
            message: __('paydunya-core::messages.user-role.show'),
            input: $requestData,
            data: $output,
            statusCode: 200,
        );
    }

    /**
     * Create the user role.
     *
     * @param  FormRequest  $request  The request.
     * @return SendApiResponse The api response.
     */
    public function create(FormRequest $request): SendApiResponse
    {
        $requestData = $request->validated();
        $userRole = UserRole::create($requestData)?->refresh();

        if (! empty($requestData['user_permissions'])) {
            $userRole->syncPermissions($requestData['user_permissions']);
        }

        $output = getOutput($userRole);

        addActivityLog(
            model: UserRole::getModel(),
            event: 'user-role-created',
            properties: ['input' => $requestData, 'output' => $output],
            logDescription: __('paydunya-core::activity-log.user-role.created', ['user_role' => $userRole->label])
        );

        return new SendApiResponse(
            success: true,
            message: __('paydunya-core::messages.user-role.created'),
            input: $requestData,
            data: $output,
            statusCode: 201,
        );
    }

    /**
     * Update the user role.
     *
     * @param  FormRequest  $request  The request.
     * @param  string  $userRoleId  The user role id.
     * @return SendApiResponse The api response.
     */
    public function update(FormRequest $request, string $userRoleId): SendApiResponse
    {
        $requestData = $request->validated();

        $userRole = modelExist(UserRole::getModel(), $userRoleId, 'user-role', 'updated', $requestData);
        if ($userRole instanceof SendApiResponse) {
            return $userRole;
        }

        $userRole->update($requestData);

        if (! empty($requestData['user_permissions'])) {
            $userRole->syncPermissions($requestData['user_permissions']);
        }

        $output = getOutput($userRole);

        addActivityLog(
            model: UserRole::getModel(),
            event: 'user-role-updated',
            properties: ['input' => $requestData, 'output' => $output],
            logDescription: __('paydunya-core::activity-log.user-role.updated', ['user_role' => $userRole->label])
        );

        return new SendApiResponse(
            success: true,
            message: __('paydunya-core::messages.user-role.updated'),
            input: $requestData,
            data: $output,
            statusCode: 200,
        );
    }

    /**
     * Enable or disable the user role.
     *
     * @param  EnableDisableRequest  $request  The request.
     * @param  string  $userRoleId  The user role id.
     * @return SendApiResponse The api response.
     */
    public function enableOrDisable(EnableDisableRequest $request, string $userRoleId): SendApiResponse
    {
        $requestData = $request->validated();

        $userRole = modelExist(UserRole::getModel(), $userRoleId, 'user-role', ($requestData['new_status'] == 'enabled') ? 'activated' : 'deactivated', $requestData);
        if ($userRole instanceof SendApiResponse) {
            return $userRole;
        }

        $oldStatus = (is_null($userRole->activated_at)) ? 'disabled' : 'enabled';
        $activatedAt = ($requestData['new_status'] == 'enabled') ? now() : null;
        $toDo = ($requestData['new_status'] == 'enabled') ? __('paydunya-core::messages.user-role.activated') : __('paydunya-core::messages.user-role.deactivated');

        if ($requestData['new_status'] !== $oldStatus) {
            $userRole->update(['activated_at' => $activatedAt]);
        }

        $output = getOutput($userRole);

        addActivityLog(
            model: UserRole::getModel(),
            event: 'user-role-'.($activatedAt ? 'activated' : 'deactivated'),
            properties: ['input' => $requestData, 'output' => $output],
            logDescription: __('paydunya-core::activity-log.user-role.'.($activatedAt ? 'activated' : 'deactivated'), ['user_role' => $userRole->label])
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
     * Apply a group action on the role.
     *
     * @param  GroupActionRequest  $request  The request.
     * @return SendApiResponse The api response.
     */
    public static function groupAction(GroupActionRequest $request): SendApiResponse
    {
        $requestData = $request->validated();
        $userRoles = self::userRoleRequest($requestData);

        if ($userRoles->count() > 0) {
            match ($requestData['action']) {
                GroupActionType::ACTIVATED->value => $userRoles->update(['activated_at' => now()]),
                GroupActionType::DEACTIVATED->value => $userRoles->update(['activated_at' => null]),
                GroupActionType::ARCHIVED->value => $userRoles->delete(),
                GroupActionType::DELETED->value => $userRoles->forceDelete(),
                GroupActionType::RESTORED->value => $userRoles->restore(),
                // Todo : A mettre a jour.
                GroupActionType::UPDATED->value => $userRoles->update(),
            };

            addActivityLog(
                model: UserRole::getModel(),
                event: 'user-group-action-'.strtolower($requestData['action']),
                properties: ['input' => $requestData, 'output' => $userRoles->get()->toArray()],
                logDescription: __('paydunya-core::activity-log.user-role.group-action', ['action' => GroupActionType::from($requestData['action'])->label()])
            );

            return new SendApiResponse(
                success: true,
                message: __('paydunya-core::messages.user-role.group-action', ['action' => GroupActionType::from($requestData['action'])->label()]),
                input: $requestData,
                data: $userRoles->get()->toArray(),
                statusCode: 200,
            );
        } else {
            addActivityLog(
                model: UserRole::getModel(),
                event: 'user-group-action-'.strtolower($requestData['action']).'-attempt',
                properties: ['input' => $requestData, 'output' => $userRoles->get()->toArray()],
                logDescription: __('paydunya-core::activity-log.user-role.group-action-attempt', ['action' => GroupActionType::from($requestData['action'])->label()])
            );

            return new SendApiResponse(
                success: false,
                message: __('paydunya-core::messages.user-role.group-action-attempt', ['action' => GroupActionType::from($requestData['action'])->label()]),
                input: $requestData,
                data: $userRoles->get()->toArray(),
                statusCode: 200,
            );
        }
    }

    /**
     * Export the user roles.
     *
     * @param  FilterRequest  $request  The request.
     * @return SendApiResponse The api response.
     */
    public static function export(FilterRequest $request): SendApiResponse
    {
        $requestData = $request->validated();
        $columnsName = ['label', 'created_at'];
        $columnsLabel = ['Nom', 'Date de création', 'Statut', 'Nombre de collaborateurs'];
        $userRoles = self::userRoleRequest($requestData)->get($columnsName)->toArray();

        Excel::store(new ExportModel($userRoles, $columnsLabel), 'roles/roles_export'.now()->format('Y-m-d').'.xlsx', 'public');
        // Todo : Mettre en place l'exportation sur le space de Digital Ocean (S3).
        // Excel::store(new ExportModel($userRoles, $columnsLabel), 'roles/roles_export' . now()->format('Y-m-d') . '.xlsx', 'public');

        addActivityLog(
            model: UserRole::getModel(),
            event: 'user-role-exported',
            properties: ['input' => $requestData, 'output' => $userRoles],
            logDescription: __('paydunya-core::activity-log.user-role.exported')
        );

        return new SendApiResponse(
            success: true,
            message: __('paydunya-core::messages.user-role.exported'),
            input: $requestData,
            data: $userRoles,
            statusCode: 200,
        );
    }

    /**
     * Delete the user role.
     *
     * @param  BasePasswordConfirmationRequest  $request  The request.
     * @param  string  $userRoleId  The user role id.
     * @return SendApiResponse The api response.
     */
    public function delete(BasePasswordConfirmationRequest $request, string $userRoleId): SendApiResponse
    {
        $requestData = ['user_role_id' => $userRoleId];

        $confirmation = passwordConfirmation(UserRole::getModel(), $request->get('password'), 'archived', $requestData);

        if ($confirmation instanceof SendApiResponse) {
            return $confirmation;
        }

        $userRole = modelExist(UserRole::getModel(), $userRoleId, 'user-role', 'archived', $requestData);
        if ($userRole instanceof SendApiResponse) {
            return $userRole;
        }

        $userRole->delete();

        $output = getOutput($userRole);

        addActivityLog(
            model: UserRole::getModel(),
            event: 'user-role-archived',
            properties: ['input' => $requestData, 'output' => $output],
            logDescription: __('paydunya-core::activity-log.user-role.archived', ['user_role' => $userRole->label])
        );

        return new SendApiResponse(
            success: true,
            message: __('paydunya-core::messages.user-role.archived'),
            input: $requestData,
            data: $output,
            statusCode: 200,
        );
    }

    /**
     * Restore the user role.
     *
     * @param  string  $userRoleId  The user role id.
     * @return SendApiResponse The api response.
     */
    public function restore(string $userRoleId): SendApiResponse
    {
        $requestData = ['user_role_id' => $userRoleId];

        $userRole = modelExist(UserRole::getModel(), $userRoleId, 'user-role', 'restored', $requestData, true);
        if ($userRole instanceof SendApiResponse) {
            return $userRole;
        }

        $userRole->restore();

        $output = getOutput($userRole);

        addActivityLog(
            model: UserRole::getModel(),
            event: 'user-role-restored',
            properties: ['input' => $requestData, 'output' => $output],
            logDescription: __('paydunya-core::activity-log.user-role.restored', ['user_role' => $userRole->label])
        );

        return new SendApiResponse(
            success: true,
            message: __('paydunya-core::messages.user-role.restored'),
            input: $requestData,
            data: $output,
            statusCode: 200,
        );
    }

    /**
     * Force delete the user role.
     *
     * @param  BasePasswordConfirmationRequest  $request  The request.
     * @param  string  $userRoleId  The user role id.
     * @return SendApiResponse The api response.
     */
    public function forceDelete(BasePasswordConfirmationRequest $request, string $userRoleId): SendApiResponse
    {
        $requestData = ['user_role_id' => $userRoleId];

        $confirmation = passwordConfirmation(UserRole::getModel(), $request->get('password'), 'deleted', $requestData);

        if ($confirmation instanceof SendApiResponse) {
            return $confirmation;
        }

        $userRole = modelExist(UserRole::getModel(), $userRoleId, 'user-role', 'deleted', $requestData, true);
        if ($userRole instanceof SendApiResponse) {
            return $userRole;
        }

        $userRole->forceDelete();

        $output = getOutput($userRole);

        addActivityLog(
            model: UserRole::getModel(),
            event: 'user-role-deleted',
            properties: ['input' => $requestData, 'output' => $output],
            logDescription: __('paydunya-core::activity-log.user-role.deleted', ['user_role' => $userRole->label])
        );

        return new SendApiResponse(
            success: true,
            message: __('paydunya-core::messages.user-role.deleted'),
            input: $requestData,
            data: $output,
            statusCode: 200,
        );
    }

    /**
     * User role request.
     *
     * @param  array  $requestData  The request data.
     * @return mixed The user role request.
     */
    public static function userRoleRequest(array $requestData = []): mixed
    {
        return UserRole::when($requestData['user_role_id'] ?? '', fn ($q) => $q->where('id', $requestData['user_role_id']))
            ->when($requestData['label'] ?? '', fn ($q) => $q->where('label', 'like', '%'.$requestData['label'].'%'))
            ->when($requestData['name'] ?? '', fn ($q) => $q->where('name', 'like', '%'.$requestData['name'].'%'))
            ->when($requestData['description'] ?? '', fn ($q) => $q->where('description', 'like', '%'.$requestData['description'].'%'))
            ->when($requestData['guard_name'] ?? '', fn ($q) => $q->where('guard_name', $requestData['guard_name']))
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
