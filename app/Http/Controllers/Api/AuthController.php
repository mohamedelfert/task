<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Twilio\Rest\Client;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
//    public function __construct()
//    {
//        $this->middleware('auth:api', ['except' => ['login', 'register']]);
//    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|between:2,35',
            'phone' => 'required|unique:users',
            'password' => 'required|string|confirmed|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(array(
                "status" => false,
                "errors" => $validator->errors()
            ), 400);
        }

        $data = $request->except(['password']);
        $data['password'] = bcrypt($request->password);

        $pin_code = random_int(1111, 9999);
        $data['pin_code'] = $pin_code;

        // for send sms pin code
        $receiverNumber = $request->phone;
        $message = "Verification code is : ( " . $pin_code . " )";

        try {

            $account_sid = getenv("TWILIO_SID");
            $auth_token = getenv("TWILIO_TOKEN");
            $twilio_number = getenv("TWILIO_FROM");

            $client = new Client($account_sid, $auth_token);
            $client->messages->create($receiverNumber,
                [
                    'from' => $twilio_number,
                    'body' => $message
                ]);

        } catch (Exception $e) {
            info("Error: " . $e->getMessage());
            return response()->json(array(
                "status" => false,
//                "errors" => $e->getMessage(),
                "errors" => 'There was an error in your Phone Number Please try again later'
            ), 400);
        }

        $user = User::create($data);

        return response()->json(
            [
                'status' => true,
                'message' => 'User successfully registered, and send code to your phone number for verification',
                'user' => $user,
                'pin_code' => $pin_code,
            ], 201);
    }

    public function userVerify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pin_code' => 'required',
            'phone' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(array(
                "status" => false,
                "errors" => $validator->errors()
            ), 400);
        }

        $user = User::where('pin_code', $request->pin_code)
            ->where('pin_code', '!=', 0)
            ->where('phone', $request->phone)
            ->first();

        if ($user) {
            $user->pin_code = null;
            $user->verified = 'yes';
            if ($user->save()) {
                return response()->json(
                    [
                        'status' => true,
                        'message' => 'User successfully Verified.',
                    ], 201);
            } else {
                return response()->json(array(
                    "status" => false,
                    "errors" => 'Error while verifying user, please try again later.'
                ), 400);
            }
        } else {
            return response()->json(array(
                "status" => false,
                "errors" => 'Error your pin code is not true.'
            ), 400);
        }
    }

    public function login(Request $request)
    {
        $credentials = $request->only('phone', 'password');

        //valid credential
        $validator = Validator::make($credentials, [
            'phone' => 'required',
            'password' => 'required|string|min:6|max:50'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(array(
                "status" => false,
                "errors" => $validator->messages()
            ), 400);
        }

        $data = User::where('phone', $request->phone)->first();
        $verified = $data['verified'];

        if ($verified === 'no') {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'This user is not verified , please verify your phone.',
                ], 400);
        }

        //Request is validated
        //Create token
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Login credentials are invalid.',
                    ], 400);
            }
        } catch (JWTException $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Could not create token.',
                ], 500);
        }

        //Token created, return with success response and jwt token
        return response()->json([
            'success' => true,
            'token' => $token,
        ], 200);
    }


    public function logout()
    {
        auth()->logout();

        return response()->json(array(
            "status" => true,
            "message" => 'User Logout successfully'
        ), 200);
    }

    public function profile(Request $request)
    {
        $response = [
            'status' => true,
            'message' => 'profile',
            'user' => auth()->user(),
        ];
        return response()->json($response, 200);
    }
}
