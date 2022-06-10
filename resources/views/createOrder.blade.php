@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Create Order') }}</div>
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
                                <form class="row g-3 needs-validation" novalidate action="{{route('orders.update',$order->id)}}" method="post">
                                @csrf
                                @method('PUT')
                            @elseif($formType === 'create')
                                <form class="row g-3 needs-validation" novalidate action="{{route('orders.store')}}" method="post">
                                @csrf
                            @endif
                        
                            {{csrf_field()}}
                            <div class="col-mb-4">
                                <label for="validationCustom01" class="form-label">Customer Name</label>
                                <input type="text" class="form-control" id="validationCustom01"  required name="customer_name" value="{{old('customer_name',$order->customer_name ?? '') }}">
                            </div>
                            <div class="col-mb-4">
                                <label for="validationCustom02" class="form-label">Phone Number</label>
                                <input type="text" class="form-control" id="validationCustom02"  required name="phone" value="{{old('phone',$order->phone ?? '') }}">
                            </div>
                            <div>
                                <table>

                                @foreach ($products as $k => $prd)
                                <tr>

                                    <td>
                                        <div class="card mb-4 rounded-3 shadow-sm">
                                            <div class="card-body">
                                                <h2 class="card-title pricing-card-title">{{ $prd->product_name }}</h2>
                                                <ul class="list-unstyled mt-3 mb-4">
                                                    <li><img class="img-thumbnail img-preview" alt="Photo" src="{{asset('storage/'.$prd->image)}}" width="50" height="50"></li>
                                                    <li>INR {{$prd->price }}</li>
                                                </ul>
                                            </div>
                                        </div></td>
                                    <td>
                                        <input type="checkbox" name="products[]" value="{{$prd->id}}" class="form-check-input" id="productSel" @if(isset($order->products) && isset($order->products[$prd->id]) && ($order->products[$prd->id]->product_id == $prd->id)) checked="" @endif/>
                                    </td>
                                    <td id="quantityEl" @if(isset($order->products) && isset($order->products[$prd->id]) && ($order->products[$prd->id]->product_id == $prd->id)) @else style="display: none" @endif>
                                        <input type="number" name="item[{{$prd->id}}][qty]" @if(isset($order->products[$prd->id]) && ($order->products[$prd->id]->product_id == $prd->id)) value="{{$order->products[$prd->id]->quantity}}" @else value="1" @endif class="form-control">
                                        <input type="hidden" name="item[{{$prd->id}}][id]" value="{{$prd->id}}">
                                    </td>
                                </tr>
                                @endforeach
                                </table>
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


        $("input[type='checkbox']").on('change', function () {
            console.log(this);
            if(this.checked){
                $(this).closest('td').next('td').show();
            }else{
                $(this).closest('td').next('td').hide();
            }
        });

    </script>
@endsection