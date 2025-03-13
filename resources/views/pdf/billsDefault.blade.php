<!DOCTYPE html>
<html>
<head>
    @include('pdf.css')
</head>
<body>
<div style="width: 100%">
        <div >

            <div>
                <h2 class="heading">Invoice</h2>
                <table class="top-table-invoice">
                    <thead>
                        <tr>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>

                            <td>
                                <p class="address"> <strong>
                                {{ $bill->client->client_name }}</strong><br>



                                {{ $bill->client->address }} <br>



                                {{ $bill->client->city }}<br>



                                {{ $bill->client->country }} <br>



                                {{ $bill->client->phone }}
                                </p>
                            </td>

                            <td>
                                <p>
                                    {{ $bill->invoice_number }}<br>
                                    {{ $bill->bill_status }}<br>
                                    Status: <br>

                                     {{-- @if("balance === 0")
                                        Paid
                                    </div>

                                    @elseif("totalPayments > 0 && totalPayments < totalPayable")
                                        Partially paid
                                    </div>
                                    @else("totalPayments === 0 && totalPayable > 0")
                                        Not paid
                                    @endif
                                    </td>
                                     </div>
                                     --}}




                                Created:
                                {{ $bill->billing_date }}<br>


                                Due:
                                {{ $bill->billing_date, $bill->paymentTerms, $bill->termsUnits }}<br>

                            </td>




                        </tr>
                    </tbody>
                </table>




                <div class="table-invoice">
                    <div >
                        <h5>Product/Service</h5>
                        <table class="table">
                            <thead class="bg-primary">
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    {{-- <th>Discount</th>
                                    <th>Tax</th> --}}
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bill->products as $product)
                                    <tr>
                                        <td>
                                            <strong>{{ $product->name }}</strong><br>
                                            {{ $product->description }}
                                        </td>
                                        <td>{{ $product->pivot->quantity }}</td>
                                        <td>K{{ number_format($product->pivot->price, 2) }}</td>
                                        {{-- <td>K{{ number_format($product->pivot->item_discount, 2) }}</td>
                                        <td>K{{ number_format($product->pivot->tax, 2) }}</td> --}}
                                        <td>K{{ number_format($product->pivot->total, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{-- <div >
                        @if($bill->payments->isNotEmpty())
                            <h5>Payment information</h5>
                            <table class="table">
                                <thead class="bg-primary">
                                    <tr>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Payment Method</th>
                                        <th>Reference</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($bill->payments as $payment)
                                        <tr>
                                            <td>{{ $payment->payment_date }}</td>
                                            <td>{{ number_format($payment->payment_amount, 2) }}</td>
                                            <td>{{ $payment->payment_method }}</td>
                                            <td>{{ $payment->payment_reference }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif

                    </div> --}}
                    <div >
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td><strong>Total:</strong></td>
                                    <td>{{ number_format($bill->products->sum('pivot.total'), 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Tax:</strong></td>
                                    <td>{{ number_format($bill->products->sum('pivot.tax'), 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Discount:</strong></td>
                                    <td>{{ number_format($bill->products->sum('pivot.item_discount'), 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Total Payable:</strong></td>
                                    <td>{{ number_format($bill->products->sum('pivot.total') - $bill->payments->sum('payment_amount'), 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Paid:</strong></td>
                                    <td>{{ number_format($bill->payments->sum('payment_amount'), 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Balance:</strong></td>
                                    <td>{{ number_format($bill->products->sum('pivot.total') - $bill->payments->sum('payment_amount'), 2) }}</td>
                                </tr>
                            </tbody>

                        </table>
            </div>

        </div>

</div>

<script>
    window.onload = function() {
        window.print(); // Automatically trigger the print dialog
    }
</script>
</body>
</html>
