@extends('main')

@section('content')
<div class="row">
    <div class="col-xl-10 offset-xl-1">
        <div class="card my_card">
            <div class="card-header bg-transparent">
                <a href="{{route('product')}}" class="card-title mb-0 d-inline-flex align-items-center create_title">
                    <i class=" ri-arrow-left-s-line mr-3 primary-icon"></i>
                    <span class="create_sub_title">Product အသစ်ဖန်တီးမည်</span>
                </a>
            </div><!-- end card header -->
            <div class="card-body">
                <div class="row d-flex justify-content-center">
                    <div class="col-xl-12">
                        <form method="POST" action="{{route('product.store')}}" id="product_create" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-check form-switch form-switch-md form-switch-primary ms-2 mb-4 d-flex align-items-center">
                                        <input class="form-check-input mb-0" name="instock" type="checkbox" role="switch" id="SwitchCheck7" checked value="1">
                                        <label class="form-check-label mb-0" for="SwitchCheck7">Instock</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-6">
                                    <div class="mb-4">
                                        <label class="form-label">အမည်</label>
                                        <input type="text" class="form-control" name="name" autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="mb-4">
                                        <label class="form-label">အလေးချိန်</label>
                                        <input type="text" class="form-control" name="weight" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-6">
                                    <div class="mb-4">
                                        <label for="category">အမျိူးအစား / Category</label>
                                        <select name="category_id" class="form-control" aria-label="Default select example" id='category'>
                                            <option selected disabled>Category ရွေးပါ</option>
                                            @foreach ($categories as $category)
                                                <option value="{{$category->id}}">
                                                    {{$category->name}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="mb-4">
                                        <label for="brand"> Sub Category</label>
                                        <div class="sub-category">
                                            <div class="pb-2 pt-2 border border-2 border-secondary text-black-50 text-center border-dashed">
                                                Please Select Main Category First
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label for="description" class="form-label">အကြောင်းအရာ / Description</label>
                                <textarea class="form-control" name="description" id="description" rows="8"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="images">Images</label>
                                <div class="input-images" id="images"></div>
                            </div>

                            <div class="text-end submit-m-btn">
                                <button type="submit" class="submit-btn">Product အသစ်ပြုလုပ်မည်</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
    {!! JsValidator::formRequest('App\Http\Requests\StoreProductRequest', '#product_create') !!}
    <script src="{{ asset('assets/js/image-uploader.min.js') }}"></script>
    <script>
        $(".input-images").imageUploader({
            maxSize: 2 * 1024 * 1024,
            maxFiles: 10,
        });
        $(document).ready(function() {
             $('.js-example-basic-multiple').select2(
                {
                    width: '100%',
                    placeholder: "Select an Option",
                    allowClear: true
                }
             );
        });
        $sub_category = document.querySelector('.sub-category');
        document.getElementById('category').addEventListener('change',function (){
            // console.log(this.value);
            const catId = $(this).val();
            if (catId) {
                $.ajax({
                    url: '/sub/categories/server/' + catId,
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        // console.log(data)
                        $sub_category.innerHTML = `<select name = "subcategory" class="form-control"></select>`;
                        $('select[name="subcategory"]').empty();
                        console.log(data.length)
                        if(data.length >= 1){
                            data.map(el=>{
                                $('select[name="subcategory"]').append('<option value=" ' + el.id + '">' + el.name + '</option>');
                            });
                        }else {
                            $('select[name="subcategory"]').append('<option  disabled >There is no Sub Category in selected main Category </option>');

                        }

                    }

                })
            } else {
                $sub_category.innerHTML=`
                <div class="pb-4 pt-3 border border-2 border-secondary text-center border-dashed">
                                    Please Select Main Category First
                                   </div>
                `;
            }
        });

    </script>
@endsection
