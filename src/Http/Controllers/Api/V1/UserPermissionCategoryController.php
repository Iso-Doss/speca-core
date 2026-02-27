<?php

namespace Speca\SpecaCore\Http\Controllers\Api\V1;


use Maatwebsite\Excel\Facades\Excel;
use Speca\SpecaCore\Enums\GroupActionType;
use Speca\SpecaCore\Export\ExportModel;
use Speca\SpecaCore\Http\Controllers\Controller;
use Speca\SpecaCore\Http\Requests\BasePasswordConfirmationRequest;
use Speca\SpecaCore\Http\Requests\UserPermissionCategory\EnableDisableRequest;
use Speca\SpecaCore\Http\Requests\UserPermissionCategory\FilterRequest;
use Speca\SpecaCore\Http\Requests\UserPermissionCategory\FormRequest;
use Speca\SpecaCore\Http\Requests\UserPermissionCategory\GroupActionRequest;
use Speca\SpecaCore\Http\Resources\SendApiResponse;
use Speca\SpecaCore\Models\UserPermissionCategory;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Exception as WriterException;


class UserPermissionCategoryController extends Controller
{
    /**
     * The user permission category constructor.
     */
    public function __construct()
    {
        //$this->middleware('permission:list-user-permission-category|show-user-permission-category|create-user-permission-category|update-user-permission-category|enable-disable-user-permission-category|delete-user-permission-category|restore-user-permission-category|force-delete-user-permission-category', ['only' => ['index']]);
        //$this->middleware('permission:show-user-permission-category', ['only' => ['show']]);
        //$this->middleware('permission:create-user-permission-category', ['only' => ['create']]);
        //$this->middleware('permission:update-user-permission-category', ['only' => ['update']]);
        //$this->middleware('permission:enable-disable-user-permission-category', ['only' => ['enableOrDisable']]);
        //$this->middleware('permission:group-action-user-permission-category', ['only' => ['groupAction']]);
        //$this->middleware('permission:export-user-permission-category', ['only' => ['export']]);
        //$this->middleware('permission:delete-user-permission-category', ['only' => ['delete']]);
        //$this->middleware('permission:restore-user-permission-category', ['only' => ['restore']]);
        //$this->middleware('permission:force-delete-user-permission-category', ['only' => ['forceDelete']]);
    }

    /**
     * User permission category list.
     *
     * @param FilterRequest $request The request.
     * @return SendApiResponse The api response.
     */
    public static function index(FilterRequest $request): SendApiResponse
    {
        $requestData = $request->validated();
        $userPermissionCategories = self::userPermissionCategoryRequest($requestData)
            ->paginate(perPage: $requestData['limit'] ?? 10, page: $requestData['page'] ?? 1);

        $output = [
            'user_permission_categories' => $userPermissionCategories,
            'total_user_permission_categories_activated' => UserPermissionCategory::whereNotNull('activated_at')->count(),
            'total_user_permission_categories_disabled' => UserPermissionCategory::whereNull('activated_at')->count(),
            'total_user_permission_categories_archived' => UserPermissionCategory::onlyTrashed()->count(),
        ];

        addActivityLog(
            model: UserPermissionCategory::getModel(),
            event: 'user-permission-category-list',
            properties: ['input' => $requestData, 'output' => $output],
            logDescription: __('speca-core::activity-log.user-permission-category.list')
        );

        return new SendApiResponse(
            success: true,
            message: __('speca-core::messages.user-permission-category.list'),
            input: $requestData,
            data: $output,
            statusCode: 200,
        );
    }

    /**
     * User permission category details.
     *
     * @param string $userPermissionCategoryId The user permission category id.
     * @return SendApiResponse The api response.
     */
    public static function show(string $userPermissionCategoryId): SendApiResponse
    {
        $requestData = ['user_permission_category_id' => $userPermissionCategoryId];

        $userPermissionCategory = modelExist(UserPermissionCategory::getModel(), $userPermissionCategoryId, 'user-permission-category', 'show', $requestData);
        if ($userPermissionCategory instanceof SendApiResponse) {
            return $userPermissionCategory;
        }

        $output = getOutput($userPermissionCategory);

        addActivityLog(
            model: UserPermissionCategory::getModel(),
            event: 'user-permission-category-show',
            properties: ['input' => $requestData, 'output' => $output],
            logDescription: __('speca-core::activity-log.user-permission-category.show', ['user_permission_category' => $userPermissionCategory->label])
        );

        return new SendApiResponse(
            success: true,
            message: __('speca-core::messages.user-permission-category.show'),
            input: $requestData,
            data: $output,
            statusCode: 200,
        );
    }

    /**
     * Create the user permission category.
     *
     * @param FormRequest $request The request.
     * @return SendApiResponse The api response.
     */
    public static function create(FormRequest $request): SendApiResponse
    {
        $requestData = $request->validated();

        $userPermissionCategory = UserPermissionCategory::create($requestData)?->refresh();
        if (!empty($requestData['user_permissions'])) {
            $userPermissionCategory->userPermissions()->sync($requestData['user_permissions']);
        }

        $output = getOutput($userPermissionCategory);

        addActivityLog(
            model: UserPermissionCategory::getModel(),
            event: 'user-permission-category-created',
            properties: ['input' => $requestData, 'output' => $output],
            logDescription: __('speca-core::activity-log.user-permission-category.created', ['user_permission_category' => $userPermissionCategory->label])
        );

        return new SendApiResponse(
            success: true,
            message: __('speca-core::messages.user-permission-category.created'),
            input: $requestData,
            data: $output,
            statusCode: 201,
        );
    }

    /**
     * Update the user permission category.
     *
     * @param FormRequest $request The request.
     * @param string $userPermissionCategoryId The user permission category id.
     * @return SendApiResponse The api response.
     */
    public static function update(FormRequest $request, string $userPermissionCategoryId): SendApiResponse
    {
        $requestData = $request->validated();

        $userPermissionCategory = modelExist(UserPermissionCategory::getModel(), $userPermissionCategoryId, 'user-permission-category', 'updated', $requestData);
        if ($userPermissionCategory instanceof SendApiResponse) {
            return $userPermissionCategory;
        }

        $userPermissionCategory->update($requestData);
        if (!empty($requestData['user_permissions'])) {
            $userPermissionCategory->userPermissions()->sync($requestData['user_permissions']);
        }

        $output = getOutput($userPermissionCategory);

        addActivityLog(
            model: UserPermissionCategory::getModel(),
            event: 'user-permission-category-updated',
            properties: ['input' => $requestData, 'output' => $output],
            logDescription: __('speca-core::activity-log.user-permission-category.updated', ['user_permission_category' => $userPermissionCategory->label])
        );

        return new SendApiResponse(
            success: true,
            message: __('speca-core::messages.user-permission-category.updated'),
            input: $requestData,
            data: $output,
            statusCode: 200,
        );
    }

    /**
     * Enable or disable the user permission category.
     *
     * @param EnableDisableRequest $request The request.
     * @param string $userPermissionCategoryId The user permission category id.
     * @return SendApiResponse The api response.
     */
    public static function enableOrDisable(EnableDisableRequest $request, string $userPermissionCategoryId): SendApiResponse
    {
        $requestData = $request->validated();

        $userPermissionCategory = modelExist(UserPermissionCategory::getModel(), $userPermissionCategoryId, 'user-permission-category', ('enabled' == $requestData['new_status']) ? 'activated' : 'deactivated', $requestData);
        if ($userPermissionCategory instanceof SendApiResponse) {
            return $userPermissionCategory;
        }

        $oldStatus = (is_null($userPermissionCategory->activated_at)) ? 'disabled' : 'enabled';
        $activatedAt = ('enabled' == $requestData['new_status']) ? now() : null;
        $toDo = ('enabled' == $requestData['new_status']) ? __('speca-core::messages.user-permission-category.activated') : __('speca-core::messages.user-permission-category.deactivated');

        if ($requestData['new_status'] !== $oldStatus) {
            $userPermissionCategory->update(['activated_at' => $activatedAt]);
        }

        $output = getOutput($userPermissionCategory);

        addActivityLog(
            model: UserPermissionCategory::getModel(),
            event: 'user-permission-category-' . ($activatedAt ? 'activated' : 'deactivated'),
            properties: ['input' => $requestData, 'output' => $output],
            logDescription: __('speca-core::activity-log.user-permission-category.' . ($activatedAt ? 'activated' : 'deactivated'), ['user_permission_category' => $userPermissionCategory->label])
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
     * Apply a group action on the user permission categories.
     *
     * @param GroupActionRequest $request The request.
     * @return SendApiResponse The api response.
     */
    public static function groupAction(GroupActionRequest $request): SendApiResponse
    {
        $requestData = $request->validated();
        $userPermissionCategories = self::userPermissionCategoryRequest($requestData);

        if ($userPermissionCategories->count() > 0) {
            match ($requestData['action']) {
                GroupActionType::ACTIVATED->value => $userPermissionCategories->update(['activated_at' => now()]),
                GroupActionType::DEACTIVATED->value => $userPermissionCategories->update(['activated_at' => null]),
                GroupActionType::ARCHIVED->value => $userPermissionCategories->delete(),
                GroupActionType::DELETED->value => $userPermissionCategories->forceDelete(),
                GroupActionType::RESTORED->value => $userPermissionCategories->restore(),
                // Todo : A mettre a jour.
                GroupActionType::UPDATED->value => $userPermissionCategories->update(),
            };

            addActivityLog(
                model: UserPermissionCategory::getModel(),
                event: 'user-permission-category-group-action-' . strtolower($requestData['action']),
                properties: ['input' => $requestData, 'output' => $userPermissionCategories->get()->toArray()],
                logDescription: __('speca-core::activity-log.user-permission-category.group-action', ['action' => GroupActionType::from($requestData['action'])->label()])
            );

            return new SendApiResponse(
                success: true,
                message: __('speca-core::messages.user-permission-category.group-action', ['action' => GroupActionType::from($requestData['action'])->label()]),
                input: $requestData,
                data: $userPermissionCategories->get()->toArray(),
                statusCode: 200,
            );
        } else {
            addActivityLog(
                model: UserPermissionCategory::getModel(),
                event: 'user-permission-category-group-action-' . strtolower($requestData['action']) . '-attempt',
                properties: ['input' => $requestData, 'output' => $userPermissionCategories->get()->toArray()],
                logDescription: __('speca-core::activity-log.user-permission-category.group-action-attempt', ['action' => GroupActionType::from($requestData['action'])->label()])
            );

            return new SendApiResponse(
                success: false,
                message: __('speca-core::messages.user-permission-category.group-action-attempt', ['action' => GroupActionType::from($requestData['action'])->label()]),
                input: $requestData,
                data: $userPermissionCategories->get()->toArray(),
                statusCode: 200,
            );
        }
    }

    /**
     * Export the user permission categories.
     *
     * @param FilterRequest $request The request.
     * @return SendApiResponse The api response.
     * @throws Exception | WriterException The exception.
     */
    public static function export(FilterRequest $request): SendApiResponse
    {
        $requestData = $request->validated();
        $columnsName = ['label', 'created_at'];
        $columnsLabel = ['Nom', 'Date de création', 'Statut'];
        $userPermissionCategories = self::userPermissionCategoryRequest($requestData)->get($columnsName)->toArray();

        Excel::store(new ExportModel($userPermissionCategories, $columnsLabel), 'permission_categories/permission_categories_export' . now()->format('Y-m-d') . '.xlsx', 'public');
        // Todo : Mettre en place l'exportation sur le space de Digital Ocean (S3).
        // Excel::store(new ExportModel($userPermissionCategories, $columnsLabel), 'permission_categories/permission_categories_export' . now()->format('Y-m-d') . '.xlsx', 'public');

        addActivityLog(
            model: UserPermissionCategory::getModel(),
            event: 'user-permission-category-exported',
            properties: ['input' => $requestData, 'output' => $userPermissionCategories],
            logDescription: __('speca-core::activity-log.user-permission-category.exported')
        );

        return new SendApiResponse(
            success: true,
            message: __('speca-core::messages.user-permission-category.exported'),
            input: $requestData,
            data: $userPermissionCategories,
            statusCode: 200,
        );
    }

    /**
     * Delete the user permission category.
     *
     * @param BasePasswordConfirmationRequest $request The request.
     * @param string $userPermissionCategoryId The user permission category id.
     * @return SendApiResponse The api response.
     */
    public static function delete(BasePasswordConfirmationRequest $request, string $userPermissionCategoryId): SendApiResponse
    {
        $requestData = ['user_permission_category_id' => $userPermissionCategoryId];

        $confirmation = passwordConfirmation(UserPermissionCategory::getModel(), $request->get('password'), 'archived', $requestData);

        if ($confirmation instanceof SendApiResponse) {
            return $confirmation;
        }

        $userPermissionCategory = modelExist(UserPermissionCategory::getModel(), $userPermissionCategoryId, 'user-permission-category', 'archived', $requestData);
        if ($userPermissionCategory instanceof SendApiResponse) {
            return $userPermissionCategory;
        }

        $userPermissionCategory->delete();

        $output = getOutput($userPermissionCategory);

        addActivityLog(
            model: UserPermissionCategory::getModel(),
            event: 'user-permission-category-archived',
            properties: ['input' => $requestData, 'output' => $output],
            logDescription: __('speca-core::activity-log.user-permission-category.archived', ['user_permission_category' => $userPermissionCategory->label])
        );

        return new SendApiResponse(
            success: true,
            message: __('speca-core::messages.user-permission-category.archived'),
            input: $requestData,
            data: $output,
            statusCode: 200,
        );
    }

    /**
     * Restore the user permission category.
     *
     * @param string $userPermissionCategoryId The user permission category id.
     * @return SendApiResponse The api response.
     */
    public static function restore(string $userPermissionCategoryId): SendApiResponse
    {
        $requestData = ['user_permission_category_id' => $userPermissionCategoryId];

        $userPermissionCategory = modelExist(UserPermissionCategory::getModel(), $userPermissionCategoryId, 'user-permission-category', 'restored', $requestData, true);
        if ($userPermissionCategory instanceof SendApiResponse) {
            return $userPermissionCategory;
        }

        $userPermissionCategory->restore();

        $output = getOutput($userPermissionCategory);

        addActivityLog(
            model: UserPermissionCategory::getModel(),
            event: 'user-permission-category-restored',
            properties: ['input' => $requestData, 'output' => $output],
            logDescription: __('speca-core::activity-log.user-permission-category.restored', ['user_permission_category' => $userPermissionCategory->label])
        );

        return new SendApiResponse(
            success: true,
            message: __('speca-core::messages.user-permission-category.restored'),
            input: $requestData,
            data: $output,
            statusCode: 200,
        );
    }

    /**
     * Force delete the user permission category.
     *
     * @param BasePasswordConfirmationRequest $request The request.
     * @param string $userPermissionCategoryId The user permission category id.
     * @return SendApiResponse The api response.
     */
    public static function forceDelete(BasePasswordConfirmationRequest $request, string $userPermissionCategoryId): SendApiResponse
    {
        $requestData = ['user_permission_category_id' => $userPermissionCategoryId];

        $confirmation = passwordConfirmation(UserPermissionCategory::getModel(), $request->get('password'), 'deleted', $requestData);

        if ($confirmation instanceof SendApiResponse) {
            return $confirmation;
        }

        $userPermissionCategory = modelExist(UserPermissionCategory::getModel(), $userPermissionCategoryId, 'user-permission-category', 'deleted', $requestData, true);

        if ($userPermissionCategory instanceof SendApiResponse) {
            return $userPermissionCategory;
        }

        $userPermissionCategory->forceDelete();

        $output = getOutput($userPermissionCategory);

        addActivityLog(
            model: UserPermissionCategory::getModel(),
            event: 'user-permission-category-deleted',
            properties: ['input' => $requestData, 'output' => $output],
            logDescription: __('speca-core::activity-log.user-permission-category.deleted', ['user_permission_category' => $userPermissionCategory->label])
        );

        return new SendApiResponse(
            success: true,
            message: __('speca-core::messages.user-permission-category.deleted'),
            input: $requestData,
            data: $output,
            statusCode: 200,
        );
    }

    /**
     * User permission category request.
     *
     * @param array $requestData The request data.
     * @return mixed The user permission category request.
     */
    public static function userPermissionCategoryRequest(array $requestData = []): mixed
    {
        return UserPermissionCategory::with('userPermissions')
            ->when($requestData['user_permission_category_id'] ?? '', fn($q) => $q->where('id', $requestData['user_permission_category_id']))
            ->when($requestData['label'] ?? '', fn($q) => $q->where('label', 'like', '%' . $requestData['label'] . '%'))
            ->when($requestData['name'] ?? '', fn($q) => $q->where('name', 'like', '%' . $requestData['name'] . '%'))
            ->when($requestData['guard_name'] ?? '', fn($q) => $q->where('guard_name', $requestData['guard_name']))
            ->when($requestData['description'] ?? '', fn($q) => $q->where('description', 'like', '%' . $requestData['description'] . '%'))
            ->when(array_key_exists('activated', $requestData), fn($q) => $requestData['activated'] ? $q->whereNotNull('activated_at') : $q->whereNull('activated_at'))
            ->when(array_key_exists('archived', $requestData), fn($q) => $requestData['archived'] ? $q->onlyTrashed() : $q->withTrashed())
            ->when($requestData['search'] ?? '', function ($q) use ($requestData) {
                $q->where(function ($subQuery) use ($requestData) {
                    $subQuery->where('label', 'like', '%' . $requestData['search'] . '%')
                        ->orWhere('name', 'like', '%' . $requestData['search'] . '%')
                        ->orWhere('description', 'like', '%' . $requestData['search'] . '%');
                });
            })
            ->when($requestData['check'] ?? '', fn($q) => $q->whereIn('id', $requestData['check']))
            ->when($requestData['uncheck'] ?? '', fn($q) => $q->whereNotIn('id', $requestData['uncheck']))
            ->latest();
    }
}
