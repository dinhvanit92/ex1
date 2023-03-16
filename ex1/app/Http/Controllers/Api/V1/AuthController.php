<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UserLoginRequest;
use App\Http\Responses\ResponseCode;
use App\Http\Responses\ResponseTrait;
use App\Models\User;
use App\Services\UserService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Request;
use Laravel\Passport\Client as OClient;

class AuthController extends Controller
{
    use ResponseTrait;

    private $userService;

    public $successStatus = 200;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function login(UserLoginRequest $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $oClient = OClient::where('password_client', 1)->first();
            return $this->getTokenAndRefreshToken($oClient, $request->email, $request->password);
        }
        else {
            return $this->error(ResponseCode::HTTP_UNAUTHORIZED, 'User or Password incorect');
        }
    }

    public function getTokenAndRefreshToken(OClient $oClient, $email, $password) {
        $oClient = OClient::where('password_client', 1)->first();

        $response = Http::asForm()->post(config('services.passport.login_endpoint'), [
                'grant_type' => 'password',
                'client_id' => $oClient->id,
                'client_secret' => $oClient->secret,
                'username' => $email,
                'password' => $password,
                'scope' => '*',
        ]);

        $result = json_decode((string) $response->getBody(), true);

        return $this->respondWithToken($result, auth()->user());
    }

    public function register(RegisterRequest $request)
    {
        return User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);
    }

    public function refreshToken(Request $request) {
        $refresh_token = $request->header('Refreshtoken');
        $oClient = OClient::where('password_client', 1)->first();

        try {
            $response = Http::asForm()->post(config('services.passport.login_endpoint'), [
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $refresh_token,
                    'client_id' => $oClient->id,
                    'client_secret' => $oClient->secret,
                    'scope' => '*',
            ]);
            return json_decode((string) $response->getBody(), true);
        } catch (Exception $e) {
            return response()->json("unauthorized", 401);
        }
    }
}
