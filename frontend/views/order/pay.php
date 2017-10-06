<h1>订单编号:<?=$order->out_trade_no?></h1>
<p>订单价格:<?=$order->total_fee/100?></p>
<p>微信扫一扫 <img src="order/qr.html?content=<?=$code_url?>"></p>