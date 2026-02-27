<?php

namespace Speca\SpecaCore\Http\Controllers\Api\V1;

use Speca\SpecaCore\Http\Controllers\Controller;
use Speca\SpecaCore\Http\Requests\Auth\SignUpRequest;
use Speca\SpecaCore\Http\Resources\SendApiResponse;
use Speca\SpecaCore\Models\User;

class AuthController extends Controller
{
    /**
     * The auth constructor.
     */
    public function __construct()
    {
        //
    }

    /**
     * Sign up.
     *
     * @param  SignUpRequest  $request  The request.
     * @return SendApiResponse The api response.
     */
    public static function signUp(SignUpRequest $request): SendApiResponse
    {
        $input = $request->validated();
        $user = User::create($input)->refresh()->toArray();

        return new SendApiResponse(
            success: true,
            message: __(''),
            input: $input,
            data: $user
        );
    }

    public static function signIn(): SendApiResponse
    {
        return new SendApiResponse;
    }

    public static function signOut(): SendApiResponse
    {
        return new SendApiResponse;
    }

    public static function google(): SendApiResponse
    {
        return new SendApiResponse;

        // return Socialite::driver('google')->redirect();
    }

    public static function forgotPassword(): SendApiResponse
    {
        return new SendApiResponse;
    }

    public static function resetPassword(): SendApiResponse
    {
        return new SendApiResponse;
    }
}
