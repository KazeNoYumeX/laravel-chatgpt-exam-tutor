<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class TutorRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, Rule|array|string>
     */
    public function rules(): array
    {
        return match ($this->routeName()) {
            'tutor.index' => [
                'question' => ['required', 'string', 'max:255'],
                'content' => ['required', 'string', 'max:255'],
            ],
            default => [],
        };
    }
}
