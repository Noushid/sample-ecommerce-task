<table class="table">
    <tr>
        <td>Order Id</td>
        <td>{{$data->orderid}}</td>
    </tr>
    <tr>
        <td>Products</td>
        <td id="products">
            <ul class="list-group">
                @if(isset($data->products))
                    @foreach($data->products as $key => $product)
                        <li class="list-group-item">{{$key+1}} - {{$product->product->product_name}} x {{$product->quantity}} = {{$product->product->price*$product->quantity}}</li>
                    @endforeach
                @endif
            </ul>
        </td>
    </tr>
    <tr>
        <td>Total</td>
        <td>{{$data->net_amount}}</td>
    </tr>
</table>