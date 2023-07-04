<?php

namespace App\Http\Requests;

use App\Enums\ResponseEnum;
use App\Models\Member;
use App\Models\User;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class BaseRequest extends FormRequest
{
    /**
     * Get the route name.
     *
     * @return string
     */
    protected function routeName(): string
    {
        return $this->route()->getName();
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Authenticate the refresh token.
     * @param string $guard
     * @return string
     */
    protected function authenticateRefreshToken(string $guard): string
    {
        /** @var Member|User $user */
        $user = Auth::guard($guard)->user();

        // Update the user's last activity timestamp.
        $user->touch();

        $cache = $guard === Member::GUARD ? config('cache.member_refresh_token') : config('cache.user_refresh_token');

        $refreshToken = Cache::get($cache . $userId = $user->getJWTIdentifier());

        if (empty($refreshToken)) {
            $refreshToken = $user->tokens()->where('name', 'refreshToken')->first();
        }

        if ($refreshToken) {
            // Update the refresh token
            if ($refreshToken->expires_at->isPast()) {
                $refreshToken->forceFill([
                    'token' => hash('sha256', Str::random(40)),
                    'abilities' => ['refreshToken'],
                    'last_used_at' => now(),
                    'expires_at' => now()->addMinutes(config('jwt.refresh_ttl')),
                ])->save();

                Cache::put($cache . $userId, $refreshToken, now()->addWeeks());
            }

            $token = $refreshToken->token;
        } else {
            $refreshToken = $user->createToken('refreshToken', ['refreshToken'], now()->addMinutes(config('jwt.refresh_ttl')));
            $token = $refreshToken->accessToken->token;
        }
        return $token;
    }

    /**
     * Get the failed validation response for the request.
     *
     * @param Validator $validator
     * @return void
     */
    protected function failedValidation(Validator $validator): void
    {
        $msg = $validator->errors()->first() ?? '';
        $res = ResponseEnum::UNPROCESSABLE_ENTITY->toJson($msg);

        // If the error message contains the following string, return the corresponding response.
        switch (true) {
            case str_contains($msg, 'unique'):
                $key = $validator->errors()->keys()[0] ?? '';
                $res = ResponseEnum::CONFLICT->toJson($key . ' has already been taken');
                break;
            case str_contains($msg, 'already been taken.'):
                $res = ResponseEnum::CONFLICT->toJson($msg);
                break;
            case str_contains($msg, 'max.file'):
                $res = ResponseEnum::CONTENT_TOO_LONG->toJson('File size too large.');
                break;
        }
        throw new HttpResponseException($res);
    }
}
