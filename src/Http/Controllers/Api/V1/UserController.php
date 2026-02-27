<?php

namespace Speca\SpecaCore\Http\Controllers\Api\V1;

use Maatwebsite\Excel\Facades\Excel;
use Speca\SpecaCore\Enums\GroupActionType;
use Speca\SpecaCore\Export\ExportModel;
use Speca\SpecaCore\Http\Controllers\Controller;
use Speca\SpecaCore\Http\Requests\BasePasswordConfirmationRequest;
use Speca\SpecaCore\Http\Requests\User\EnableDisableRequest;
use Speca\SpecaCore\Http\Requests\User\FilterRequest;
use Speca\SpecaCore\Http\Requests\User\FormRequest;
use Speca\SpecaCore\Http\Requests\User\GroupActionRequest;
use Speca\SpecaCore\Http\Resources\SendApiResponse;
use Speca\SpecaCore\Jobs\UserConfirmationMail;
use Speca\SpecaCore\Models\User;

class UserController extends Controller
{
    /**
     * The user constructor.
     */
    public function __construct()
    {
        // $this->middleware('permission:list-user|show-user|create-user|update-user|enable-disable-user|delete-user|restore-user|force-delete-user', ['only' => ['index']]);
        // $this->middleware('permission:show-user', ['only' => ['show']]);
        // $this->middleware('permission:create-user', ['only' => ['create']]);
        // $this->middleware('permission:update-user', ['only' => ['update']]);
        // $this->middleware('permission:enable-disable-user', ['only' => ['enableOrDisable']]);
        // $this->middleware('permission:group-action-user', ['only' => ['groupAction']]);
        // $this->middleware('permission:export-user', ['only' => ['export']]);
        // $this->middleware('permission:delete-user', ['only' => ['delete']]);
        // $this->middleware('permission:restore-user', ['only' => ['restore']]);
        // $this->middleware('permission:force-delete-user', ['only' => ['forceDelete']]);
    }

    /**
     * User list.
     *
     * @param  FilterRequest  $request  The request.
     * @return SendApiResponse The api response.
     */
    public function index(FilterRequest $request): SendApiResponse
    {
        $requestData = $request->validated();

        $users = self::userRequest($requestData)
            ->paginate(perPage: $requestData['limit'] ?? 10, page: $requestData['page'] ?? 1);

        $output = [
            'users' => $users,
            'total_users_activated' => User::whereNotNull('activated_at')->count(),
            'total_users_disabled' => User::whereNull('activated_at')->count(),
            'total_users_archived' => User::onlyTrashed()->count(),
        ];

        addActivityLog(
            model: User::getModel(),
            event: 'user-list',
            properties: ['input' => $requestData, 'output' => $output],
            logDescription: __('speca-core::activity-log.user.list')
        );

        return new SendApiResponse(
            success: true,
            message: __('speca-core::messages.user.list'),
            input: $requestData,
            data: $output,
            statusCode: 200,
        );
    }

    /**
     * User details.
     *
     * @param  string  $userId  The user id.
     * @return SendApiResponse The api response.
     */
    public function show(string $userId): SendApiResponse
    {
        $requestData = ['user_id' => $userId];

        $user = modelExist(User::getModel(), $userId, 'user', 'show', $requestData);
        if ($user instanceof SendApiResponse) {
            return $user;
        }

        $output = getOutput($user);

        addActivityLog(
            model: User::getModel(),
            event: 'user-show',
            properties: ['input' => $requestData, 'output' => $output],
            logDescription: __('speca-core::activity-log.user.show', ['user' => $user->full_name])
        );

        return new SendApiResponse(
            success: true,
            message: __('speca-core::messages.user.show'),
            input: $requestData,
            data: $output,
            statusCode: 200,
        );
    }

    /**
     * Create the user.
     *
     * @param  FormRequest  $request  The request.
     * @return SendApiResponse The api response.
     */
    public function create(FormRequest $request): SendApiResponse
    {
        $requestData = $request->validated();
        $user = User::create($requestData)?->refresh();

        if (! empty($requestData['user_roles'])) {
            $user->syncRoles($requestData['user_roles']);
        }

        $output = getOutput($user);

        $frontendUrl = config('paydunya.front_end_url');
        $mailData = array_merge($user->toArray(), ['link' => $frontendUrl.'/user/'.$user->id]);
        UserConfirmationMail::dispatch($user->email, __('Confirmation d\'inscription'), 'speca-core::email.user-confirmation', $mailData);

        addActivityLog(
            model: User::getModel(),
            event: 'user-created',
            properties: ['input' => $requestData, 'output' => $output],
            logDescription: __('speca-core::activity-log.user.created', ['email' => $user->email, 'role' => $user->roles->first()?->label])
        );

        return new SendApiResponse(
            success: true,
            message: __('speca-core::messages.user.created'),
            input: $requestData,
            data: $output,
            statusCode: 201,
        );
    }

    /**
     * Re-send the email confirmation to the user.
     *
     * @param  string  $email  The user email.
     * @return SendApiResponse The api response.
     */
    public function resendConfirmationEmail(string $email): SendApiResponse
    {
        $requestData = ['email' => $email];

        $user = User::where('email', '=', $email)->first();

        if (! $user) {
            addActivityLog(
                model: User::getModel(),
                event: 'user-resend-email-confirmation-attempt',
                properties: ['input' => $requestData, 'output' => []],
                logDescription: __('speca-core::activity-log.user.resend-email-confirmation-attempt')
            );

            return new SendApiResponse(
                success: false,
                message: __('speca-core::messages.user.not-found'),
                input: $requestData,
                statusCode: 404,
            );
        }

        $frontendUrl = config('paydunya.front_end_url');
        $mailData = array_merge($user->toArray(), ['link' => $frontendUrl.'/user/'.$user->id]);
        UserConfirmationMail::dispatch($user->email, __('speca-core::messages.user.send-confirmation-email'), 'speca-core::email.user-confirmation', $mailData);

        $output = getOutput($user);

        addActivityLog(
            model: User::getModel(),
            event: 'user-resend-email-confirmation',
            properties: ['input' => $requestData, 'output' => $output],
            logDescription: __('speca-core::activity-log.user.resend-email-confirmation', ['user' => $user->full_name])
        );

        return new SendApiResponse(
            success: true,
            message: __('speca-core::messages.user.resend-email-confirmation'),
            input: $requestData,
            data: $output,
            statusCode: 200,
        );
    }

    /**
     * Update the user.
     *
     * @param  FormRequest  $request  The request.
     * @param  string  $userId  The user id.
     * @return SendApiResponse The api response.
     */
    public function update(FormRequest $request, string $userId): SendApiResponse
    {
        $requestData = $request->validated();

        $user = modelExist(User::getModel(), $userId, 'user', 'updated', $requestData);
        if ($user instanceof SendApiResponse) {
            return $user;
        }

        $user->update($requestData);

        if (! empty($requestData['user_roles'])) {
            $user->syncRoles($requestData['user_roles']);
        }

        $output = getOutput($user);

        addActivityLog(
            model: User::getModel(),
            event: 'user-updated',
            properties: ['input' => $requestData, 'output' => $output],
            logDescription: __('speca-core::activity-log.user.updated', ['user' => $user->full_name])
        );

        return new SendApiResponse(
            success: true,
            message: __('speca-core::messages.user.updated'),
            input: $requestData,
            data: $output,
            statusCode: 200,
        );
    }

    /**
     * Enable or disable the user.
     *
     * @param  EnableDisableRequest  $request  The request.
     * @param  string  $userId  The user id.
     * @return SendApiResponse The api response.
     */
    public function enableOrDisable(EnableDisableRequest $request, string $userId): SendApiResponse
    {
        $requestData = $request->validated();

        $user = modelExist(User::getModel(), $userId, 'user', ($requestData['new_status'] == 'enabled') ? 'activated' : 'deactivated', $requestData);
        if ($user instanceof SendApiResponse) {
            return $user;
        }

        $oldStatus = (is_null($user->activated_at)) ? 'disabled' : 'enabled';
        $activatedAt = ($requestData['new_status'] == 'enabled') ? now() : null;
        $toDo = ($requestData['new_status'] == 'enabled') ? __('speca-core::messages.user.activated') : __('speca-core::messages.user.deactivated');

        if ($requestData['new_status'] !== $oldStatus) {
            $user->update(['activated_at' => $activatedAt]);
        }

        $output = getOutput($user);

        addActivityLog(
            model: User::getModel(),
            event: 'user-'.($activatedAt ? 'activated' : 'deactivated'),
            properties: ['input' => $requestData, 'output' => $output],
            logDescription: __('speca-core::activity-log.user.'.($activatedAt ? 'activated' : 'deactivated'), ['email' => $user->email, 'role' => $user->roles->first()?->label])
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
     * Apply a group action on the user.
     *
     * @param  GroupActionRequest  $request  The request.
     * @return SendApiResponse The api response.
     */
    public static function groupAction(GroupActionRequest $request): SendApiResponse
    {
        $requestData = $request->validated();
        $users = self::userRequest($requestData);

        if ($users->count() > 0) {
            match ($requestData['action']) {
                GroupActionType::ACTIVATED->value => $users->update(['activated_at' => now()]),
                GroupActionType::DEACTIVATED->value => $users->update(['activated_at' => null]),
                GroupActionType::ARCHIVED->value => $users->delete(),
                GroupActionType::DELETED->value => $users->forceDelete(),
                GroupActionType::RESTORED->value => $users->restore(),
                // Todo : A mettre a jour.
                GroupActionType::UPDATED->value => $users->update(),
            };

            addActivityLog(
                model: User::getModel(),
                event: 'user-group-action-'.strtolower($requestData['action']),
                properties: ['input' => $requestData, 'output' => $users->get()->toArray()],
                logDescription: __('speca-core::activity-log.user.group-action', ['action' => GroupActionType::from($requestData['action'])->label()])
            );

            return new SendApiResponse(
                success: true,
                message: __('speca-core::messages.user.group-action', ['action' => GroupActionType::from($requestData['action'])->label()]),
                input: $requestData,
                data: $users->get()->toArray(),
                statusCode: 200,
            );
        } else {
            addActivityLog(
                model: User::getModel(),
                event: 'user-group-action-'.strtolower($requestData['action']).'-attempt',
                properties: ['input' => $requestData, 'output' => $users->get()->toArray()],
                logDescription: __('speca-core::activity-log.user.group-action-attempt', ['action' => GroupActionType::from($requestData['action'])->label()])
            );

            return new SendApiResponse(
                success: false,
                message: __('speca-core::messages.user.group-action-attempt', ['action' => GroupActionType::from($requestData['action'])->label()]),
                input: $requestData,
                data: $users->get()->toArray(),
                statusCode: 200,
            );
        }
    }

    /**
     * Export the users.
     *
     * @param  FilterRequest  $request  The request.
     * @return SendApiResponse The api response.
     */
    public static function export(FilterRequest $request): SendApiResponse
    {
        $requestData = $request->validated();
        $columnsName = ['full_name', 'phone_with_indicative', 'created_at'];
        $columnsLabel = ['Nom complet', 'Numéro de téléphone', 'Date de création'];
        $users = self::userRequest($requestData)->get($columnsName)->toArray();

        Excel::store(new ExportModel($users, $columnsLabel), 'users/users_export'.now()->format('Y-m-d').'.xlsx', 'public');
        // Todo : Mettre en place l'exportation sur le space de Digital Ocean (S3).
        // Excel::store(new ExportModel($users, $columnsLabel), 'users/users' . now()->format('Y-m-d') . '.xlsx', 'public');

        addActivityLog(
            model: User::getModel(),
            event: 'user-exported',
            properties: ['input' => $requestData, 'output' => $users],
            logDescription: __('speca-core::activity-log.user.exported')
        );

        return new SendApiResponse(
            success: true,
            message: __('speca-core::messages.user.exported'),
            input: $requestData,
            data: $users,
            statusCode: 200,
        );
    }

    /**
     * Delete the user.
     *
     * @param  BasePasswordConfirmationRequest  $request  The request.
     * @param  string  $userId  The user id.
     * @return SendApiResponse The api response.
     */
    public function delete(BasePasswordConfirmationRequest $request, string $userId): SendApiResponse
    {
        $requestData = ['user_id' => $userId];

        $confirmation = passwordConfirmation(User::getModel(), $request->get('password'), 'archived', $requestData);

        if ($confirmation instanceof SendApiResponse) {
            return $confirmation;
        }

        $user = modelExist(User::getModel(), $userId, 'user', 'archived', $requestData);
        if ($user instanceof SendApiResponse) {
            return $user;
        }

        $user->delete();

        $output = getOutput($user);

        addActivityLog(
            model: User::getModel(),
            event: 'user-archived',
            properties: ['input' => $requestData, 'output' => $output],
            logDescription: __('speca-core::activity-log.user.archived', ['user' => $user->full_name])
        );

        return new SendApiResponse(
            success: true,
            message: __('speca-core::messages.user.archived'),
            input: $requestData,
            data: $output,
            statusCode: 200,
        );
    }

    /**
     * Restore the user.
     *
     * @param  string  $userId  The user id.
     * @return SendApiResponse The api response.
     */
    public function restore(string $userId): SendApiResponse
    {
        $requestData = ['user_id' => $userId];

        $user = modelExist(User::getModel(), $userId, 'user', 'restored', $requestData, true);
        if ($user instanceof SendApiResponse) {
            return $user;
        }

        $user->restore();

        $output = getOutput($user);

        addActivityLog(
            model: User::getModel(),
            event: 'user-restored',
            properties: ['input' => $requestData, 'output' => $output],
            logDescription: __('speca-core::activity-log.user.restored', ['email' => $user->email])
        );

        return new SendApiResponse(
            success: true,
            message: __('speca-core::messages.user.restored'),
            input: $requestData,
            data: $output,
            statusCode: 200,
        );
    }

    /**
     * Force delete the user.
     *
     * @param  BasePasswordConfirmationRequest  $request  The request.
     * @param  string  $userId  The user id.
     * @return SendApiResponse The api response.
     */
    public function forceDelete(BasePasswordConfirmationRequest $request, string $userId): SendApiResponse
    {
        $requestData = ['user_id' => $userId];

        $confirmation = passwordConfirmation(User::getModel(), $request->get('password'), 'deleted', $requestData);

        if ($confirmation instanceof SendApiResponse) {
            return $confirmation;
        }

        $user = modelExist(User::getModel(), $userId, 'user', 'deleted', $requestData, true);
        if ($user instanceof SendApiResponse) {
            return $user;
        }

        $user->forceDelete();

        $output = getOutput($user);

        addActivityLog(
            model: User::getModel(),
            event: 'user-deleted',
            properties: ['input' => $requestData, 'output' => $output],
            logDescription: __('speca-core::activity-log.user.deleted', ['user' => $user->full_name])
        );

        return new SendApiResponse(
            success: true,
            message: __('speca-core::messages.user.deleted'),
            input: $requestData,
            data: $output,
            statusCode: 200,
        );
    }

    /**
     * User request.
     *
     * @param  array  $requestData  The request data.
     * @return mixed The user request.
     */
    public static function userRequest(array $requestData = []): mixed
    {
        return User::with(['roles', 'permissions', 'userProfiles'])->when($requestData['user_id'] ?? '', fn ($q) => $q->where('id', $requestData['user_id']))
            ->when($requestData['email'] ?? '', fn ($q) => $q->where('email', 'like', '%'.$requestData['email'].'%'))
            ->when($requestData['phone_with_indicative'] ?? '', fn ($q) => $q->where('phone_with_indicative', 'like', '%'.$requestData['phone_with_indicative'].'%'))
            ->when($requestData['type'] ?? '', fn ($q) => $q->where('type', 'like', '%'.$requestData['type'].'%'))
            ->when($requestData['gender'] ?? '', fn ($q) => $q->where('gender', 'like', '%'.$requestData['gender'].'%'))
            ->when($requestData['address'] ?? '', fn ($q) => $q->where('address', 'like', '%'.$requestData['address'].'%'))
            ->when($requestData['birthday'] ?? '', fn ($q) => $q->where('birthday', $requestData['birthday']))
            ->when($requestData['full_name'] ?? '', fn ($q) => $q->where('full_name', 'like', '%'.$requestData['full_name'].'%'))
            ->when($requestData['legal_name'] ?? '', fn ($q) => $q->where('legal_name', 'like', '%'.$requestData['legal_name'].'%'))
            ->when($requestData['commercial_name'] ?? '', fn ($q) => $q->where('commercial_name', 'like', '%'.$requestData['commercial_name'].'%'))
            ->when($requestData['profession_title'] ?? '', fn ($q) => $q->where('profession_title', 'like', '%'.$requestData['profession_title'].'%'))
            ->when($requestData['country_id'] ?? '', fn ($q) => $q->where('country_id', '='.$requestData['country_id']))
            ->when($requestData['residence_country_id'] ?? '', fn ($q) => $q->where('residence_country_id', '='.$requestData['residence_country_id']))
            ->when($requestData['nationality_country_id'] ?? '', fn ($q) => $q->where('nationality_country_id', '='.$requestData['nationality_country_id']))
            ->when($requestData['role_id'] ?? '', function ($q) use ($requestData) {
                $q->whereHas('roles', function ($query) use ($requestData) {
                    $query->where('id', '=', $requestData['role_id']);
                });
            })
            ->when($requestData['user_profile_id'] ?? '', function ($q) use ($requestData) {
                $q->whereHas('userProfiles', function ($query) use ($requestData) {
                    $query->where('id', '=', $requestData['user_profile_id']);
                });
            })
            ->when($requestData['permission_id'] ?? '', function ($q) use ($requestData) {
                $q->whereHas('permissions', function ($query) use ($requestData) {
                    $query->where('id', '=', $requestData['permission_id']);
                });
            })
            ->when(array_key_exists('activated', $requestData), fn ($q) => $requestData['activated'] ? $q->whereNotNull('activated_at') : $q->whereNull('activated_at'))
            ->when(array_key_exists('archived', $requestData), fn ($q) => $requestData['archived'] ? $q->onlyTrashed() : $q->withTrashed())
            ->when($requestData['search'] ?? '', function ($q) use ($requestData) {
                $q->where(function ($subQuery) use ($requestData) {
                    $subQuery->where('email', 'like', '%'.$requestData['email'].'%')
                        ->orWhere('phone_with_indicative', 'like', '%'.$requestData['phone_with_indicative'].'%')
                        ->orWhere('full_name', 'like', '%'.$requestData['full_name'].'%');
                });
            })
            ->when($requestData['check'] ?? '', fn ($q) => $q->whereIn('id', $requestData['check']))
            ->when($requestData['uncheck'] ?? '', fn ($q) => $q->whereNotIn('id', $requestData['uncheck']))
            ->latest();
    }
}
