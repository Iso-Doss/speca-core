<?php

namespace Speca\SpecaCore\Http\Controllers\Api\V1\UrlShortener;

use Speca\SpecaCore\Http\Controllers\Controller;
use Speca\SpecaCore\Http\Requests\UrlShortener\Url\EnableDisableRequest;
use Speca\SpecaCore\Http\Requests\UrlShortener\Url\FormRequest;
use Speca\SpecaCore\Http\Resources\SendApiResponse;

class UrlController extends Controller
{
    /**
     * The constructor.
     */
    public function __construct() {}

    /**
     * Url list.
     *
     * @return SendApiResponse The api response.
     */
    public function index(): SendApiResponse
    {
        return new SendApiResponse;
    }

    /**
     * Url details.
     *
     * @return SendApiResponse The api response.
     */
    public function show(): SendApiResponse
    {
        return new SendApiResponse;
    }

    /**
     * Create the url.
     *
     * @return SendApiResponse The api response.
     */
    public function create(): SendApiResponse
    {
        return new SendApiResponse;
    }

    /**
     * Update the url.
     *
     * @param  FormRequest  $request  The request.
     * @param  int  $urlId  The url id.
     * @return SendApiResponse The api response.
     */
    public function update(FormRequest $request, int $urlId): SendApiResponse
    {
        return new SendApiResponse;
    }

    /**
     * Enable or disable the url.
     *
     * @param  EnableDisableRequest  $request  The request.
     * @param  int  $urlId  The url id.
     * @return SendApiResponse The api response.
     */
    public function enableOrDisable(EnableDisableRequest $request, int $urlId): SendApiResponse
    {
        return new SendApiResponse;
    }

    /**
     * Delete the url.
     *
     * @param  int  $urlId  The url id.
     * @return SendApiResponse The api response.
     */
    public function delete(int $urlId): SendApiResponse
    {
        return new SendApiResponse;
    }

    /**
     * Restore the url.
     *
     * @param  int  $urlId  The url id.
     * @return SendApiResponse The api response.
     */
    public function restore(int $urlId): SendApiResponse
    {
        return new SendApiResponse;
    }

    /**
     * Force delete the url.
     *
     * @param  int  $urlId  The url id.
     * @return SendApiResponse The api response.
     */
    public function forceDelete(int $urlId): SendApiResponse
    {
        return new SendApiResponse;
    }
}
