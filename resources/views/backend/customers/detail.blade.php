@extends('main')

@section('content')
<div class="row">
    <div class="col-xl-10 offset-xl-1">
        <div class="card my_card">
            <div class="card-header d-flex justify-content-between align-items-center bg-transparent">
                <a href="{{route('customer')}}" class="card-title mb-0 d-inline-flex align-items-center create_title">
                    <i class=" ri-arrow-left-s-line mr-3 primary-icon"></i>
                    <span class="create_sub_title">Customer အချက်အလက်</span>
                </a>
                <div class="d-flex">
{{--                    @if($customer->is_admin_approve != true)--}}

{{--                    <button class="primary_button me-2 accept" >--}}
{{--                        <i class="ri-checkbox-circle-line btn_icon_size primary-icon mr-2"></i>--}}
{{--                        <span class="button_content">Register လက်ခံမည်</span>--}}
{{--                    </button>--}}
{{--                    @endif--}}
                    <a class="primary_button" href="{{ route('customer.edit',$customer->id) }}">
                        <div class="d-flex align-items-center">
                            <i class="mdi mdi-square-edit-outline btn_icon_size primary-icon mr-2"></i>
                            <span class="button_content">Customer အချက်အလက်ကိုပြုပြင်မည်</span>
                        </div>
                    </a>
                </div>

            </div><!-- end card header -->
            <div class="card-body">
                <div class="row d-flex justify-content-center">
                    <div class="col-xl-12">
                        <table class="table table-bordered" style="width: 100%">
                            <tbody>
                                <tr>
                                    <th width="30%">အမည်</th>
                                    <td>{{ $customer->name }}</td>
                                </tr>
{{--                                <tr>--}}
{{--                                    <th width="30%">အီးမေးလ် / Email</th>--}}
{{--                                    <td>{{ $customer->email ?? '-----'}}</td>--}}
{{--                                </tr>--}}
                                <tr>
                                    <th width="30%">အီးမေးလ် / Phone</th>
                                    <td>{{ $customer->phone ?? '-----'}}</td>
                                </tr>
                                @if ($customer->is_banned == '1')
                                    <tr>
                                        <th width="30%">Is Banned</th>
                                        <td>
                                            <div class="badge bg-danger">Banned</div>
                                        </td>
                                    </tr>
                                @endif
                                <tr>
                                    <th width="30%">အကောင့်စဖွင့်ချိန် / Created At</th>
                                    <td>{{ $customer->created_at->diffForHumans()}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
    <script>
      $(document).ready(function() {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 1800,
                width : '18em',
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            })
          $(document).on('click', '.accept', function(e) {
              e.preventDefault();
              swal({
                  text: "Are you sure to accept {{$customer['name']}} ?",
                  icon: "info",
                  buttons: true,
                  dangerMode: true,
              })
                  .then((confirm) => {
                      if (confirm) {
                          let id = '{{ $customer['id'] }}';
                          $.ajax({
                              url : `/customers/accept/${id}`,
                              method : 'POST',
                              dataType: 'json',
                              data: {
                                  _token: '{{ csrf_token() }}',
                              },
                          }).done(function(res) {
                              Swal.fire({
                                  icon: 'success',
                                  title: 'Accepted successfully',
                                  showConfirmButton: false,
                                  timer: 1500
                              });
                              setTimeout(function() {
                                  window.location.href="{{route('customer')}}";
                              }, 1500);
                          })
                      }
                  });
          })
        })


    </script>
@endsection

