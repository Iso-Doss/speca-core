<?php

namespace Speca\SpecaCore\Http\Controllers\Api\V1;

use Maatwebsite\Excel\Facades\Excel;
use Speca\SpecaCore\Export\ExportModel;
use Speca\SpecaCore\Http\Controllers\Controller;
use Speca\SpecaCore\Http\Requests\ActivityLog\FilterRequest;
use Speca\SpecaCore\Http\Resources\SendApiResponse;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    /**
     * The activity constructor.
     */
    public function __construct()
    {
        //$this->middleware('permission:list-activity-log', ['only' => ['index']]);
    }

    /**
     * Activity Log list.
     *
     * @param FilterRequest $request The request.
     * @return SendApiResponse The api response.
     */
    public function index(FilterRequest $request): SendApiResponse
    {
        $requestData = $request->validated();

        $activityLog = self::activityLogRequest($requestData)
            ->paginate(perPage: $requestData['limit'] ?? 10, page: $requestData['page'] ?? 1);

        $output = ['activities-log' => $activityLog];

        addActivityLog(
            model: Activity::getModel(),
            event: 'activity-list',
            properties: ['input' => $requestData, 'output' => $output],
            logDescription: __('speca-core::activity-log.activities-log.list')
        );

        return new SendApiResponse(
            success: true,
            message: __('speca-core::messages.activities-log.list'),
            input: $requestData,
            data: $output,
            statusCode: 200,
        );
    }

    /**
     * Export the activity.
     *
     * @param FilterRequest $request The request.
     * @return SendApiResponse The api response.
     */
    public static function export(FilterRequest $request): SendApiResponse
    {
        $requestData = $request->validated();
        $columnsName = ['created_at', 'description'];
        $columnsLabel = ['Date de création', 'Détail de l\'action'];
        $userActivities = self::activityLogRequest($requestData)->get($columnsName)->toArray();

        Excel::store(new ExportModel($userActivities, $columnsLabel), 'activities/activities_export' . now()->format('Y-m-d') . '.xlsx', 'public');
        // Todo : Mettre en place l'exportation sur le space de Digital Ocean (S3).
        //Excel::store(new ExportModel($userPermissionCategories, $columnsLabel), '', 'public');

        addActivityLog(
            model: Activity::getModel(),
            event: 'activity-exported',
            properties: ['input' => $requestData, 'output' => $userActivities],
            logDescription: __('speca-core::activity-log.activities-log.exported')
        );

        return new SendApiResponse(
            success: true,
            message: __('speca-core::messages.activities-log.exported'),
            input: $requestData,
            data: $userActivities,
            statusCode: 200,
        );
    }

    /**
     * Activity request.
     *
     * @param array $requestData The request data.
     * @return mixed The activity request.
     */
    public static function activityLogRequest(array $requestData = []): mixed
    {
        return Activity::with('causer')->when($requestData['activity_log_id'] ?? '', fn($q) => $q->where('id', $requestData['activity_log_id']))
            ->when($requestData['description'] ?? '', fn($q) => $q->where('description', 'like', '%' . $requestData['description'] . '%'))
            ->when($requestData['event'] ?? '', fn($q) => $q->where('event', 'like', '%' . $requestData['event'] . '%'))
            ->when($requestData['period_start'] ?? '', fn($q) => $q->where('created_at', '>=', $requestData['period_start']))
            ->when($requestData['period_end'] ?? '', fn($q) => $q->where('created_at', '<=', $requestData['period_end']))
            ->when($requestData['full_name'] ?? '', function ($q) use ($requestData) {
                $q->whereHas('causer', function ($subQuery) use ($requestData) {
                    $subQuery->where('full_name', 'like', '%' . $requestData['full_name'] . '%');
                });
            })
            ->when($requestData['email'] ?? '', function ($q) use ($requestData) {
                $q->whereHas('causer', function ($subQuery) use ($requestData) {
                    $subQuery->where('email', 'like', '%' . $requestData['email'] . '%');
                });
            })
            ->when($requestData['search'] ?? '', function ($q) use ($requestData) {
                $q->where(function ($subQuery) use ($requestData) {
                    $subQuery->where('description', 'like', '%' . $requestData['search'] . '%')
                        ->orWhereHas('causer', function ($userQuery) use ($requestData) {
                            $userQuery->where('full_name', 'like', '%' . $requestData['search'] . '%')
                                ->orWhere('email', 'like', '%' . $requestData['search'] . '%');
                        });
                });
            })
            ->when($requestData['check'] ?? '', fn($q) => $q->whereIn('id', $requestData['check']))
            ->when($requestData['uncheck'] ?? '', fn($q) => $q->whereNotIn('id', $requestData['uncheck']))
            ->where('log_name', '=', 'speca-core')
            ->latest();
    }
}

