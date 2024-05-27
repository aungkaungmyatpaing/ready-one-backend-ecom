<?php

namespace App\Http\Controllers\Backend;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use App\Models\FcmTokenKey;
use Cassandra\Custom;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use App\Http\Requests\OrderCancelRequest;
use App\Http\Requests\OrderRefundRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Requests\API\StoreOrderRequest;
use App\Http\Requests\StoreOrderDeliveredRequest;

class OrderController extends Controller
{
    //index
    public function index()
    {
        return view('backend.orders.index');
    }


    public function refundOrderList()
    {
        return view('backend.orders.refund-order');
    }

    public function getRefundList()
    {
        return $this->serverSide(null, true);
    }

    public function orderByStatus()
    {
        $orderStatus = [
            'pending',
            'confirm',
            'processing',
            'delivered',
            'complete',
            'cancel'
        ];
        if (!in_array(request()->status, $orderStatus)) {
            return abort(404);
        }
        return view('backend.orders.pending-order')->with(['status' => request()->status]);
    }

    //detail
    public function detail(Order $order, $notiId = null)
    {
        if ($notiId) {
            if ($notiId) {
                auth()->user()->notifications->where('id', $notiId)->markAsRead();
            }
        }
        $orderDetail = Order::with('orderItem', 'orderItem.product', 'orderItem.product.image', 'payment', 'customer', 'deliveryFeeRelation', 'deliveryFeeRelation.region')->where('id', $order->id)->first()->toArray();
//        return $orderDetail;
        // dd($orderDetail);
        return view('backend.orders.detail')->with(['order' => $orderDetail]);
    }

    //update order status
    public function updateStatus(Order $order, UpdateOrderRequest $request)
    {
        $order->update([
            'status' => $request->status,
        ]);

        $this->sendPushNotification($request->status, $order->customer_id);

        return response()->json([
            'message' => 'Order updated successfully',
        ]);
    }

    public function cancelOrder(Order $order)
    {
        return view('backend.orders.cancel')->with(['order' => $order]);
    }

    public function refundOrder(Order $order)
    {
        return view('backend.orders.refund')->with(['order' => $order]);
    }


    public function saveCancelOrder(Order $order, OrderCancelRequest $request)
    {
        $order->update(['cancel_message' => $request->message, 'status' => 'cancel']);

        $this->sendPushNotification('cancel', $order->customer_id);

        return redirect()->route('order')->with(['updated', 'Order cancel လုပ်ခြင်း အောင်မြင်ပါသည်']);
    }

     public function deliverOrder(Order $order)
     {
         return view('backend.orders.deliver')->with(['order' => $order]);
     }


    public function saveRefundOrder(Order $order, OrderRefundRequest $request)
    {
        $order->update([
            'refund_date'       => Carbon::now(),
            'refund_message'    => $request->message,
            'refund_screenshot' => $request->file('image')->store('orders')
        ]);

        $this->sendPushNotification('refund', $order->customer_id);

        return redirect()->route('order')->with(['updated', 'Order refund လုပ်ခြင်း အောင်မြင်ပါသည်']);
    }

     public function saveDeliverOrder(Order $order, StoreOrderDeliveredRequest $request)
     {
         $order->update([
             'status' => 'delivered',
             'delivered_date'    => Carbon::now(),
             'delivered_message' => $request->message,
             'delivered_image'   => $request->file('image')->store('orders')
         ]);

         $this->sendPushNotification('delivered', $order->customer_id);

         return redirect()->route('order')->with(['updated', 'Order Deliver လုပ်ခြင်း အောင်မြင်ပါသည်']);
     }


    //all order datatable
    public function getAllOrder()
    {
        return $this->serverSide();
    }

    public function getOrderByStatus($status)
    {
        return $this->serverSide($status);
    }

    //data table
    public function serverSide($status = null, $refund = false)
    {
        $order = Order::query();
        if (isset($status)) {
            $order->where('status', $status)->orderBy('id', 'desc');
        } elseif ($refund) {
            $order->where('refund_date', '!=', null)->orderBy('id', 'desc');
        } else {
            $order->orderBy('id', 'desc');
        }
        return datatables($order)
            ->editColumn('created_at', function ($each) {
                return $each->created_at->diffForHumans() ?? '-';
            })
            ->editColumn('status', function ($each) {
                if ($each->status == "pending") {
                    $status = 'bg-danger';
                } elseif ($each->status == "finish") {
                    $status = 'bg-success';
                } elseif ($each->status == "cancel") {
                    $status = 'bg-dark';
                } else {
                    $status = 'bg-info';
                }
                $status = '<div class="badge ' . $status . '">' . $each->status . '</div>';
                $refund = '<div class="badge bg-primary my-2">refunded</div>';
                if ($each->refund_date) {
                    return '<div class="d-flex flex-column justify-content-center align-items-center">' . $status . $refund . '</div>';
                }
                return '<div class="">' . $status . '</div>';
            })
            ->addColumn('action', function ($each) {
                $show_icon = '<a href="' . route('order.detail', $each->id) . '" class="show_btn btn btn-sm btn-info mr-3"><i class="ri-eye-fill btn_icon_size"></i></a>';
                $cancel_btn = '<a href="' . route('order.cancel', $each->id) . '" class="btn btn-dark cancelBtn">Cancel</a>';
                $refund_btn = '<a href="' . route('order.refund', $each->id) . '" class="btn btn-primary " data-id="' . $each->id . '">Refund</a>';
                if ($each->refund_date) {
                    return '<div class="action_icon d-flex align-items-center">' . $show_icon . '</div>';
                }
                if ($each->status == 'cancel') {
                    return '<div class="action_icon d-flex align-items-center">' . $show_icon . $refund_btn . '</div>';
                }
                return '<div class="action_icon d-flex align-items-center">' . $show_icon . $cancel_btn . '</div>';
            })
            ->rawColumns(['status', 'action'])
            ->toJson();
    }

    private function sendPushNotification($status, $customerId)
    {
        $url = 'https://fcm.googleapis.com/fcm/send';
        $serverKey = config('app.firebase.server_key');

        $customer = Customer::find($customerId);
        $notificaions = [
            'title' => 'Order ' . $status,
            'body' => 'Your order has been ' . $status  . " by " . config('app.companyInfo.name'),
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
}
