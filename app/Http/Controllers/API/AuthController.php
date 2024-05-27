<?php

namespace App\Http\Controllers\API;

use App\Events\NewCustomerRegisterEvent;
use App\Http\Requests\API\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use App\Models\FcmTokenKey;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends BaseController
{
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    //login
    public function login(LoginRequest $request)
    {
        $this->middleware('auth:api');

        if (Auth::guard('api')->user()) {
            return $this->sendError(401, 'Already login!');
        }

        $customer = Customer::Where('phone', $request->emailOrPhone)->first();
        $customer->fcm_token_key = $request->fcm_token_key;
//        if (!$customer->is_admin_approve){
//            return response()->json([
//                'success' => false,
//                'message' => "Oops! Your request has been sent, but it's currently awaiting approval from our admin. Thank you for your patience.",
//            ], 401);
//        }

        $hashPassword = $customer->password;
        if (Hash::check($request->password, $hashPassword)) {
            return response()->json([
                'success' => true,
                'token' => $customer->createToken(config('app.companyInfo.name'))->accessToken,
                'data' => new CustomerResource($customer),
            ], 200);
//            if ($customer->device_id == $request->device_id){
//                return response()->json([
//                    'success' => true,
//                    'token' => $customer->createToken(config('app.companyInfo.name'))->accessToken,
//                    'data' => new CustomerResource($customer),
//                ], 200);
//            }else{
//                return $this->sendError(401, 'Sorry, you can only log in on the device where you registered. Please use the registered device for login.');
//            }

        } else {
            return $this->sendError(401, 'Credentials do not match');
        }
    }

    //register
    public function register(RegisterRequest $request)
    {
        if (Auth::guard('api')->user()) {
            return $this->sendError(401, 'Already login!');
        }

        $data = $this->getCustomerRequestData($request);
        $customer = Customer::create($data);
        event(new NewCustomerRegisterEvent($this->getNotificationData($customer->id)));
        if ($customer) {
            return response()->json([
                'success' => true,
                'token' => $customer->createToken(config('app.companyInfo.name'))->accessToken,
                'data' => new CustomerResource($customer),
            ], 200);
//            return response()->json([
//                'success' => true,
//                'data' => "Your subscription request has been sent to the admin. We will notify you when it is approved.",
//            ], 200);
        }

        return $this->sendError(401, 'Register Fail! Try Again.');
    }
    private function getNotificationData($id)
    {
        $data = Customer::find($id);
        $data->message = 'new customer';
        return $data;
    }
    //logout
    public function logout()
    {
        $customer = Auth::guard('api')->user()->token();
        $customer->revoke();

        return $this->sendResponse('Logout successfully');
    }


    private function getCustomerRequestData($request)
    {
        $data = [
            'name' => $request->name,
            'password' => Hash::make($request->password),
            'created_at' => Carbon::now(),
        ];
//        if ($request->email) {
//            $data['email'] = $request->email;
//        }
        if ($request->phone) {
            $data['phone'] = $request->phone;
        }
        if ($request->fcm_token_key) {
            $data['fcm_token_key'] = $request->fcm_token_key;
        }

        return $data;
    }
}
