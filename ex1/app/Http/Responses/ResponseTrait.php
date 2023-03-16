<?php

declare(strict_types=1);

namespace App\Http\Responses;

trait ResponseTrait
{
    protected function success($data)
    {
        return response()->json($data);
    }

    protected function error($httpCode, $message = '')
    {
        return response()->json(
            [
                'message' => $this->translateMessage($httpCode, $message),
                'errors' => [
                    $httpCode => $this->translateMessage($httpCode),
                ],
            ],
            $httpCode
        );
    }

    protected function errorMessage($httpCode, $message = '')
    {
        return response()->json(
            [
                'message' => $this->translateMessage($httpCode, $message),
            ],
            $httpCode
        );
    }

    protected function notFound()
    {
        return $this->error(ResponseCode::HTTP_NOT_FOUND);
    }

    protected function notAuthorize()
    {
        return $this->error(ResponseCode::HTTP_UNAUTHORIZED);
    }

    protected function respondWithToken($token, $user = null)
    {
        $data = [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ];

        return $this->success($data);
    }

    private function translateMessage($httpCode, $message = '')
    {
        if (empty($message)) {
            $message = __('response_message.' . $httpCode);
        }

        return $message;
    }
}
