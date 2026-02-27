<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Speca\SpecaCore\Http\Resources\SendApiResponse;
use Speca\SpecaCore\Models\User;

if (! function_exists('whoCanAccessDeveloperTools')) {
    /**
     * Who can access developer tools.
     *
     * @param  User  $user  The user.
     * @return bool Who can access developer tools.
     */
    function whoCanAccessDeveloperTools(User $user): bool
    {
        return ! is_null($user->email) && in_array($user->email, explode(',', config('speca-core.who-can-access-developers-tools', '')));
    }
}

if (! function_exists('addActivityLog')) {
    /**
     * Add activity log.
     *
     * @param  Model|null  $model  The model.
     * @param  string  $event  The event.
     * @param  Model|int|string|null  $user  The user.
     * @param  array  $properties  The properties.
     * @param  string  $logDescription  The log description.
     * @param  string  $logName  The log name.
     */
    function addActivityLog(?Model $model, string $event, Model|int|string|null $user = null, array $properties = [], string $logDescription = '', string $logName = 'speca-core'): void
    {
        $activity = activity()
            ->event($event)
            ->causedBy($user ?? auth()->user())
            ->withProperties($properties)
            ->inLog($logName);

        $model and $activity->performedOn($model);

        $activity->log($logDescription);
    }
}

if (! function_exists('isApiRequest')) {
    /**
     * Check if the request or url is an api request.
     *
     * @param  string  $url  The url.
     * @return bool Is api request.
     */
    function isApiRequest(string $url): bool
    {
        return count(explode('/api/', $url)) >= 2;
    }
}

if (! function_exists('getOutput')) {
    /**
     * Get the model output.
     *
     * @param  Model|SendApiResponse  $model  The model.
     * @return array The output.
     */
    function getOutput(Model|SendApiResponse $model): array
    {
        return ($model instanceof Model) ? $model->toArray() : [];
    }
}

if (! function_exists('modelExist')) {
    /**
     * Check if the model exists.
     *
     * @param  Model  $model  The model.
     * @param  string  $modelId  The model id.
     * @param  string  $modelSlug  The model slug.
     * @param  string  $eventType  The event type.
     * @param  array  $requestData  The request data.
     * @param  bool  $withTrashed  Is with trashed.
     * @return SendApiResponse|true The send api response or boolean.
     */
    function modelExist(Model $model, string $modelId, string $modelSlug, string $eventType, array $requestData = [], bool $withTrashed = false): SendApiResponse|Model
    {
        $model = $model::when($withTrashed ?? false, fn ($q) => $withTrashed ? $q->withTrashed() : $q)->find($modelId);

        if (! $model) {
            addActivityLog(
                model: $model,
                event: $modelSlug.'-'.$eventType.'-attempt',
                properties: ['input' => $requestData, 'output' => []],
                logDescription: __('speca-core::activity-log.'.$modelSlug.'.'.$eventType.'-attempt')
            );

            return new SendApiResponse(
                success: false,
                message: __('speca-core::messages.'.$modelSlug.'.not-found'),
                input: $requestData,
                statusCode: 404,
            );
        } else {
            return $model;
        }
    }
}

if (! function_exists('passwordConfirmation')) {

    /**
     * Check if the authenticated user password is valid.
     *
     * @param  string  $password  The user password.
     * @param  string  $event  The event considered.
     * @param  array  $requestData  The request Data.
     * @return SendApiResponse|bool The api response or boolean response.
     */
    function passwordConfirmation(Model $model, string $password, string $event, array $requestData = []): SendApiResponse|bool
    {
        $user = Auth::user();

        if (! $user || ! Hash::check($password, $user->password)) {

            addActivityLog(
                model: $model,
                event: $event,
                properties: ['input' => $requestData, 'output' => []],
                logDescription: __('speca-core::activity-log.user.unauthorized')
            );

            return new SendApiResponse(
                success: false,
                message: __('speca-core::messages.user.unauthorized'),
                input: $requestData,
                statusCode: 403,
            );
        }

        return true;
    }
}
