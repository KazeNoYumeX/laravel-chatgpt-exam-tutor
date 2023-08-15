<?php

namespace App\Http\Controllers;

use App\Http\Requests\TutorRequest;
use Arr;
use ArrayAccess;
use OpenAI;

/**
 * @group Tutor
 */
class TutorController extends Controller
{
    /**
     * Tutor - Ask and Answer
     *
     * This is a tutor api for students to ask questions and get answers.
     *
     * @param TutorRequest $request
     * @return array|ArrayAccess
     */
    public function index(TutorRequest $request): array|ArrayAccess
    {
        //TODO: add history to database or cache
        $history = '';

        $messages = [
            [
                'role' => 'system',
                'content' => 'You are now an experienced elementary school teacher who knows how to guide students, summarize their problems, and provide them with answers, and answer them using Traditional Chinese, Taiwan throughout.'
            ],
            [
                'role' => 'user',
                'content' => "{$history} \n\n The exam questions is as follows: {$request->get('question')}, \n\n And now, the student's question here: {$request->get('content')} \n\n please answer to him with specific and actionable advice. Please reply to him in Traditional Chinese directly, without the need for formalities or repeating his question. at most 200 words."
            ],
        ];

        $result = OpenAI::client(config('openai.api_key'))->chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => $messages,
        ]);

        return Arr::get($result, 'choices.0.message');
    }
}
