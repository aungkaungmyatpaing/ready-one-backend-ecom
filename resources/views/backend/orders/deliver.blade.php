@extends('main')

@section('content')
<div class="row">

    <div class="col-12">
        <div class="card bg-white my_card">
            <div class="card-header bg-transparent">
                <div class="d-flex align-items-center justify-content-between">
                    <a href="{{ URL::previous() }}" class="card-title mb-0 d-inline-flex align-items-center create_title">
                        <i class=" ri-arrow-left-s-line mr-3 primary-icon"></i>
                        <span class="create_sub_title">Order Deliver လုပ်မည်</span>
                    </a>
                </div>
            </div>
            <div class="card-body ">
                <div class="row">

                    <div class="col-6">
                        <label for="">Order အချက်အလက်</label>
                        <div class="table-responsive">
                            <table class="table table-nowrap align-middle table-bordered mb-0">
                                  <tbody>
                                    <tr>
                                        <th width="20%">Name</th>
                                        <td>{{ $order['name'] }}</td>
                                    </tr>
                                    <tr>
                                        <th width="20%">Phone</th>
                                        <td>{{ $order['phone'] }}</td>
                                    </tr>
                                    <tr>
                                        <th width="20%">Address</th>
                                        <td>{{ $order['address'] }}</td>
                                    </tr>

                                    <tr>
                                        <th width="20%">Payment Type</th>
                                        <td>{{ $order['payment_method'] == 'payment' ? $order['payment']['payment_type'] : 'cod' }}</td>
                                    </tr>

                                    <tr>
                                        <th width="20%">Grand Total</th>
                                        <td>{{ $order['grand_total'] }} MMK</td>
                                    </tr>
                                    <tr>
                                        <th width="20%">Order Time</th>
                                        <td>{{ Carbon\Carbon::parse($order['created_at'])->diffForHumans() }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-6">
                        <form action="{{ route('order.saveDeliver',$order->id) }}" method="POST" id="order_deliver" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                  <label for="image">Image</label>
                                  <div class="mb-3">
                                      <img src="{{ asset(config('app.companyInfo.logo')) }}" class="" id="imgPreview" alt="" style="width: 150px;border-radius: 10px;border: 1px dotted #888">
                                  </div>
                                  <input type="file" class="form-control" name="image" id="photo" accept="image/*"></input>
                              </div>
                            <div class="mb-4">
                                <label for="">Message</label>
                                <textarea name="message" id="" cols="30" rows="5" class="form-control"></textarea>
                            </div>
                            <button type="submit" class="submit-btn float-end">Order Deliver လုပ်မည်</button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
{!! JsValidator::formRequest('App\Http\Requests\StoreOrderDeliveredRequest', '#order_deliver') !!}

    <script>
      
        $('#photo').change(function(){
        const file = this.files[0];
        console.log(file);
        if (file){
          let reader = new FileReader();
          reader.onload = function(event){
            console.log(event.target.result);
            $('#imgPreview').attr('src', event.target.result);
          }
          reader.readAsDataURL(file);
        }
      });

    </script>
@endsection

