@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Products') }}</div>
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                            @if($formType == 'edit')
                                <form class="row g-3 needs-validation" novalidate action="{{route('product.update',$product->id)}}" method="post" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                            @elseif($formType === 'create')
                                <form class="row g-3 needs-validation" novalidate action="{{route('product.store')}}" method="post" enctype="multipart/form-data">
                                @csrf
                            @endif
                        
                            {{csrf_field()}}
                            <div class="col-mb-4">
                                <label for="validationCustom01" class="form-label">Product Name</label>
                                <input type="text" class="form-control" id="validationCustom01"  required name="product_name" value="{{old('product_name',$product->product_name ?? '') }}">
                            </div>
                            <div class="col-mb-4">
                                <label for="validationCustom02" class="form-label">Image</label>
                                <input type="file" class="form-control" aria-label="file example" @if($formType == "create") required @endif id="validationCustom02" name="product_image" onchange="readURL(this,'#imgPreview',true)">
                                
                                <div class="col-md-2"  style="padding-top: 10px;">
                                    <img class="img-thumbnail img-preview" alt="Photo" id="imgPreview" @if($formType == "edit") src="{{asset('storage/'.$product->image)}}" @else style="display: none" src="#" @endif>
                                </div>
                            </div>

                            <div class="col-mb-3">
                                <label for="validationCustom04" class="form-label">Category</label>
                                <select class="form-select" id="validationCustom04" required name="category_id">
                                    <option selected disabled value="">Choose...</option>
                                    @if(count($categories) > 0)
                                        @foreach($categories as $category)
                                            <option value="{{$category->id}}" @if(old('category_id') == $category->id) selected="selected" @elseif(isset($product) && $product->category_id == $category->id) selected="" @endif>{{$category->name}}</option>
                                        @endforeach
                                        @endif
                                </select>
                                <div class="invalid-feedback">
                                    Please select a valid Category.
                                </div>
                            </div>
                            <div class="col-mb-3">
                                <label for="validationCustom05" class="form-label">Price</label>
                                <input type="text" class="form-control" id="validationCustom05" required name="price" value="{{old('price',$product->price ?? '') }}">
                                <div class="invalid-feedback">
                                    Please provide a valid Price.
                                </div>
                            </div>

                            <div class="col-12">
                                <button class="btn btn-primary" type="submit">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('custom-scripts')
    <script>
        // Example starter JavaScript for disabling form submissions if there are invalid fields
        (function () {
            'use strict'

            // Fetch all the forms we want to apply custom Bootstrap validation styles to
            var forms = document.querySelectorAll('.needs-validation')

            // Loop over them and prevent submission
            Array.prototype.slice.call(forms)
                    .forEach(function (form) {
                        form.addEventListener('submit', function (event) {
                            if (!form.checkValidity()) {
                                event.preventDefault()
                                event.stopPropagation()
                            }

                            form.classList.add('was-validated')
                        }, false)
                    })
        })();

        function readURL(input,showEl) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $(showEl).attr('src', e.target.result);
                    $(showEl).show();
                };

                reader.readAsDataURL(input.files[0]);
            }else{
                $(showEl).attr('src', '');
                $(showEl).hide();
            }
        }

    </script>
@endsection