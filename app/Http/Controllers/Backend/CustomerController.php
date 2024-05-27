<?php

namespace App\Http\Controllers\Backend;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UpdateCustomerRequest;
use App\Http\Requests\UpdateCustomerPasswordRequest;
use Illuminate\Support\Facades\Http;

class CustomerController extends Controller
{
    //index
    public function index(){
        return view('backend.customers.index');
    }

    //edit
    public function edit(Customer $customer){
        return view('backend.customers.edit')->with(['customer' => $customer]);
    }

    //update
    public function update(UpdateCustomerRequest $request,Customer $customer){
        $customer->update([
            'name' => $request->name,
//            'email' => $request->email,
            'phone' => $request->phone,
        ]);
        return redirect()->route('customer')->with(['updated'=>'Customer updated successfully']);
    }

    //updatePassword
    public function updatePassword(UpdateCustomerPasswordRequest $request,Customer $customer){
        $customer->update([
            'password' => Hash::make($request->password)
        ]);
        return redirect()->route('customer')->with(['updated'=>'Customer updated successfully']);
    }

    //detail
    public function detail(Customer $customer,$notiId=null){
        if ($notiId) {
            auth()->user()->notifications->where('id', $notiId)->markAsRead();
        }
        return view('backend.customers.detail')->with(['customer'=> $customer]);
    }

    public function accept(Customer $customer,Request $request){
        $customer->is_admin_approve = true;
        $customer->update();
        $this->sendPushNotification("Accepted",$customer->id);
        return response()->json([
            'message' => 'Register accepted successfully',
        ]);
    }


    //ban customer
    public function banCustomer(Customer $customer,Request $request){
        $customer->update([
            'is_banned' => $request->is_banned,
        ]);
        if($customer->is_banned == '0'){
            $this->sendPushNotification('Un-Bun',$customer->id);
        }else{
            $this->sendPushNotification('Bun',$customer->id);
        }

        return response()->json([
            'customerName' => $customer->name,
        ]);
    }

    private function sendPushNotification($status, $customerId)
    {
        $url = 'https://fcm.googleapis.com/fcm/send';
        $serverKey = config('app.firebase.server_key');

        $customer = Customer::find($customerId);
        $notificaions = [
            'title' => 'Account ' . $status,
            'body' => 'Your account has been ' . $status  . " by " . config('app.companyInfo.name'),
        ];
        Http::withHeaders([
            'Authorization' => "key={$serverKey}",
            'Content-Type' => "application/json"
        ])->post($url, [
            'to' => $customer->fcm_token_key,
            'notification' => $notificaions,
        ]);

        return true;
    }

    //server side
    public function serverSide()
    {
        $customers = Customer::orderBy('id','desc');
        return datatables($customers)
//        ->addColumn('email',function($each){
//            return $each->email ?? '-----';
//        })
        ->addColumn('phone',function($each){
            return $each->phone ?? '-----';
        })
        ->addColumn('action', function ($each) {
            $show_icon = '<a href="'.route('customer.detail', $each->id).'" class="btn btn-sm btn-info detail_btn mr-3"><i class="ri-eye-fill btn_icon_size"></i></a>';
            $edit_icon = '<a href="'.route('customer.edit', $each->id).'" class="btn btn-sm btn-success mr-3 edit_btn"><i class="mdi mdi-square-edit-outline btn_icon_size"></i></a>';

            return '<div class="action_icon">'. $show_icon .$edit_icon.'</div>';
        })
        ->addColumn('is_ban', function ($each) {
            if($each->is_banned == '0'){
                $ban_btn = '<a href="#" class="btn btn-danger ban_btn"  data-id="'.$each->id  .'">Ban</a>';
            }else{
                $ban_btn = '<a href="#" class="btn btn-outline-danger unban_btn"  data-id="'.$each->id  .'">Unban</a>';
            }
            return '<div class="action_icon">'. $ban_btn .'</div>';
        })
        ->rawColumns(['action','is_ban'])
        ->toJson();
    }
}
