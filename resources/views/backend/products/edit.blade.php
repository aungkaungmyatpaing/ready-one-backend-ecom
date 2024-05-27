@extends('main')

@section('content')
<div class="row">
    <div class="col-xl-10 offset-xl-1">
        <div class="card my_card">
            <div class="card-header bg-transparent">
                <a href="{{route('product')}}" class="card-title mb-0 d-inline-flex align-items-center create_title">
                    <i class=" ri-arrow-left-s-line mr-3 primary-icon"></i>
                    <span class="create_sub_title">Product ကိုပြုပြင်မည်</span>
                </a>
            </div><!-- end card header -->
            <div class="card-body">
                <div class="row d-flex justify-content-center">
                    <div class="col-xl-12">
                        @if(Session::get('fail'))
                            <div class="alert alert-danger p-3 mb-3 text-center">
                                {{Session::get('fail')}}
                            </div>
                        @endif
                        <form method="POST" action="{{route('product.update', $product->id)}}" id="product_update" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-check form-switch form-switch-md form-switch-primary ms-2 mb-4 d-flex align-items-center">
                                        <input class="form-check-input mb-0" name="instock" type="checkbox" role="switch" id="SwitchCheck7" {{ old('instock',$product->instock) == 1 ? 'checked' : ''}} value="1">
                                        <label class="form-check-label mb-0" for="SwitchCheck7">Instock</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-6">
                                    <div class="mb-3">
                                        <label class="form-label mb-3">အမည်</label>
                                        <input type="text" class="form-control" name="name" autocomplete="off" value="{{$product->name}}">
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="mb-3">
                                        <label class="form-label mb-3">အလေးချိန်</label>
                                        <input type="text" class="form-control" name="weight" autocomplete="off" value="{{$product->weight}}">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xl-6">
                                    <div class="mb-3">
                                        <label for="category">အမျိူးအစား / Category</label>
                                        <select name="category_id" class="form-select mb-3" aria-label="Default select example" id='category'>
                                            <option selected disabled>အမျိူးအစား ရွေးပါ</option>
                                            @foreach ($categories as $category)
                                                <option value="{{$category->id}}" {{$category->id == $product->category_id ? 'selected' : ''}}>
                                                    {{$category->name}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="mb-3">
                                        <label for="brand">အမှတ်တံဆိပ် / Brand</label>
                                        <select name="subcategory" class="form-select mb-3" aria-label="Default select example" id='subCategory'>
                                            <option selected disabled>please select sub category</option>
                                            @foreach ($subCategories as $sc)
                                                <option value="{{$sc->id}}" {{$sc->id == $product->sub_category_id ? 'selected' : ''}}>
                                                    {{$sc->name}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-5 mt-3">
                                <label for="description" class="form-label">အကြောင်းအရာ / Description</label>
                                <textarea class="form-control" name="description" id="description" rows="8">{{$product->description}}</textarea>
                            </div>
                            <div class="form-group">
                                <label for="images">Images</label>
                                <div class="input-images" id="images"></div>
                            </div>

                            <div class="text-end submit-m-btn">
                                <button type="submit" class="submit-btn">ပြင်ဆင်မှုများကိုသိမ်းမည်</button>
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
    {!! JsValidator::formRequest('App\Http\Requests\UpdateProductRequest', '#product_update') !!}
    <script src="{{ asset('assets/js/image-uploader.min.js') }}"></script>
    <script>
        $.ajax({
            url: `/product-images/${`{{ $product->id }}`}`
            }).done(function(response) {
            if( response ){
                $('.input-images').imageUploader({
                    preloaded: response,
                    imagesInputName: 'images',
                    preloadedInputName: 'old',
                    maxSize: 2 * 1024 * 1024,
                    maxFiles: 10
                });
            }
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
