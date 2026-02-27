<?php

namespace Speca\SpecaCore\Http\Controllers\Api\V1\UrlShortener;

use Speca\SpecaCore\Http\Controllers\Controller;
use Speca\SpecaCore\Http\Requests\UrlShortener\UrlHistory\EnableDisableRequest;
use Speca\SpecaCore\Http\Requests\UrlShortener\UrlHistory\FormRequest;
use Speca\SpecaCore\Http\Resources\SendApiResponse;

class UrlHistoryController extends Controller
{
    /**
     * The constructor.
     */
    public function __construct() {}

    /**
     * Url history list.
     *
     * @return SendApiResponse The api response.
     */
    public function index(): SendApiResponse
    {
        return new SendApiResponse;
    }

    /**
     * Url history details.
     *
     * @return SendApiResponse The api response.
     */
    public function show(): SendApiResponse
    {
        return new SendApiResponse;
    }

    /**
     * Create the url history.
     *
     * @return SendApiResponse The api response.
     */
    public function create(): SendApiResponse
    {
        return new SendApiResponse;
    }

    /**
     * Update the url history.
     *
     * @param  FormRequest  $request  The request.
     * @param  int  $urlHistoryId  The url history id.
     * @return SendApiResponse The api response.
     */
    public function update(FormRequest $request, int $urlHistoryId): SendApiResponse
    {
        return new SendApiResponse;
    }

    /**
     * Enable or disable the url history.
     *
     * @param  EnableDisableRequest  $request  The request.
     * @param  int  $urlHistoryId  The url history id.
     * @return SendApiResponse The api response.
     */
    public function enableOrDisable(EnableDisableRequest $request, int $urlHistoryId): SendApiResponse
    {
        return new SendApiResponse;
    }

    /**
     * Delete the url history.
     *
     * @param  int  $urlHistoryId  The url history id.
     * @return SendApiResponse The api response.
     */
    public function delete(int $urlHistoryId): SendApiResponse
    {
        return new SendApiResponse;
    }

    /**
     * Restore the url history.
     *
     * @param  int  $urlHistoryId  The url history id.
     * @return SendApiResponse The api response.
     */
    public function restore(int $urlHistoryId): SendApiResponse
    {
        return new SendApiResponse;
    }

    /**
     * Force delete the url history.
     *
     * @param  int  $urlHistoryId  The url history id.
     * @return SendApiResponse The api response.
     */
    public function forceDelete(int $urlHistoryId): SendApiResponse
    {
        return new SendApiResponse;
    }
}
