<?php

require_once 'vendor/autoload.php';
require_once 'lib/function/orderfunction.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$orderid = $_GET['orderid'] ?? '';

if ($orderid == '') {
    die("Invalid Order");
}

$orderObj = new Order();
$order = $orderObj->getOrderById($orderid);

if (!$order) {
    die("Invoice not found");
}

$options = new Options();
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);

$html = '
<!DOCTYPE html>
<html>
<head>

<style>

body{
    font-family: DejaVu Sans;
    font-size:13px;
    color:#333;
}

h1,h2,h3{
    margin:0;
}

.header{
    text-align:center;
    margin-bottom:25px;
}

.info{
    margin-bottom:20px;
}

.info table{
    width:100%;
}

table.items{
    width:100%;
    border-collapse:collapse;
}

table.items th{
    background:#222;
    color:#fff;
    border:1px solid #ddd;
    padding:8px;
}

table.items td{
    border:1px solid #ddd;
    padding:8px;
}

.total-table{
    width:40%;
    float:right;
    margin-top:20px;
    border-collapse:collapse;
}

.total-table td{
    border:1px solid #ddd;
    padding:8px;
}

.footer{
    margin-top:70px;
    text-align:center;
    font-size:12px;
    color:#777;
}

</style>

</head>

<body>

<div class="header">

<h1>Malee Dress Point</h1>

<p>
Boutique Store
</p>

<h2>ORDER INVOICE</h2>

</div>

<div class="info">

<table>

<tr>

<td>

<b>Invoice No :</b> '.$order['orderid'].'<br>

<b>Invoice Date :</b> '.date('d-m-Y',strtotime($order['created_at'])).'<br>

<b>Payment :</b> '.ucwords(str_replace('_',' ',$order['payment_method'])).'<br>

<b>Status :</b> '.ucwords(str_replace('_',' ',$order['order_status'])).'

</td>

<td>

<b>Customer :</b><br>

'.$order['customer_name'].'<br>

'.$order['phone'].'<br>

'.$order['email'].'<br>

'.$order['address'].'<br>

'.$order['city'].'<br>

'.$order['postal_code'].'

</td>

</tr>

</table>

</div>

<table class="items">

<tr>

<th>#</th>

<th>Product</th>

<th>Qty</th>

<th>Unit Price</th>

<th>Total</th>

</tr>

';

$count=1;

foreach($order['items'] as $item){

$html .= '

<tr>

<td>'.$count++.'</td>

<td>'.$item['product_name'].'</td>

<td>'.$item['qty'].'</td>

<td>Rs. '.number_format($item['price'],2).'</td>

<td>Rs. '.number_format($item['line_total'],2).'</td>

</tr>

';

}

$html .= '

</table>

<table class="total-table">

<tr>

<td><b>Subtotal</b></td>

<td>Rs. '.number_format($order['subtotal'],2).'</td>

</tr>

<tr>

<td><b>Delivery Fee</b></td>

<td>Rs. '.number_format($order['delivery_fee'],2).'</td>

</tr>

<tr>

<td><b>Grand Total</b></td>

<td><b>Rs. '.number_format($order['total'],2).'</b></td>

</tr>

</table>

<div style="clear:both"></div>

<div class="footer">

<h3>Thank You For Shopping With Malee Dress Point</h3>

<p>
This is a computer generated invoice and does not require a signature.
</p>

</div>

</body>

</html>

';

$dompdf->loadHtml($html);

$dompdf->setPaper('A4','portrait');

$dompdf->render();

$dompdf->stream(
    "Invoice_".$order['orderid'].".pdf",
    array("Attachment"=>true)
);

?>