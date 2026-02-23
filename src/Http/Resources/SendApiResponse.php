<?php

namespace Speca\SpecaCore\Http\Resources;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\MessageBag;

class SendApiResponse extends JsonResource
{
    /**
     * The status.
     *
     * @var bool The status.
     */
    public bool $status = true;

    /**
     * The success.
     *
     * @var bool The success.
     */
    public bool $success = true;

    /**
     * The message.
     *
     * @var string The message.
     */
    public string $message = '';

    /**
     * The errors.
     *
     * @var array|MessageBag The errors.
     */
    public array|MessageBag $errors = [];

    /**
     * The warnings.
     *
     * @var array The warnings.
     */
    public array $warnings = [];

    /**
     * The input.
     *
     * @var array The input.
     */
    public array $input = [];

    /**
     * The output.
     *
     * @var array|LengthAwarePaginator|Collection The output.
     */
    public array|LengthAwarePaginator|Collection $output = [];

    /**
     * The data.
     *
     * @var array|LengthAwarePaginator|Collection The data.
     */
    public array|LengthAwarePaginator|Collection $data = [];

    /**
     * The status code.
     *
     * @var int The status code.
     */
    protected int $statusCode = 200;

    /**
     * Create a new resource instance.
     *
     * @param bool $success The success.
     * @param string $message The message
     * @param array|MessageBag $errors The errors.
     * @param array $warnings The warnings.
     * @param array $input The input.
     * @param array|LengthAwarePaginator|Collection $data The data.
     * @param int $statusCode The status code.
     */
    public function __construct(bool $success = true, string $message = '', array|MessageBag $errors = [], array $warnings = [], array $input = [], array|LengthAwarePaginator|Collection $data = [], int $statusCode = 200)
    {
        parent::__construct($data);
        $this->status = $success;
        $this->success = $success;
        $this->message = $message;
        $this->errors = $errors;
        $this->warnings = $warnings;
        $this->input = $input;
        $this->data = $data;
        //$this->output = $data;
        $this->statusCode = $statusCode;
        $this->getResponseArray();
    }

    /**
     * Get response as an array.
     *
     * @return array $response The response as an array.
     */
    private function getResponseArray(): array
    {
        $response = [
            'status' => $this->success,
            'success' => $this->success,
            'message' => $this->message,
            'errors' => $this->errors,
            'warnings' => $this->warnings,
            'input' => $this->input,
            'output' => $this->data,
            //'data' => $this->data,
        ];
        if ($this->success) {
            Log::info('Api response', $response);
        } else {
            Log::error('Api response', $response);
        }
        return $response;
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return $this->getResponseArray();
    }

    /**
     * Get the response.
     *
     * @param Request|null $request The request.
     * @return JsonResponse The JSON response.
     */
    public function response($request = null): JsonResponse
    {
        return response()->json($this->getResponseArray(), $this->statusCode);
    }

    /**
     * Customize the response for a request.
     *
     * @param Request $request The request.
     * @param JsonResponse $response The response.
     * @return void
     */
    public function withResponse(Request $request, JsonResponse $response): void
    {
        $response->setStatusCode($this->getStatusCode());
    }

    /**
     * Get status code.
     *
     * @return int The status code.
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
