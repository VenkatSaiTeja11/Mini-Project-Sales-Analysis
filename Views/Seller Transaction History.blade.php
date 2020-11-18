<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">

	<style>
		body{
			margin:1%;
		}

		input{
			border-radius:5px;
			text-align:center;
		}
	
	</style>

	

	<title>Transaction History</title>
</head>

<body>
	<header>
		<h1 class="text-center display-4 bg-dark text-light p-1 rounded text-wrap">Transaction History</h1>
	</header>
	<br/>
	<br/>
	
	<section class="container text-center">
		<form action="/seller_transaction_history" method="post">
			@csrf
			<div class="row justify-content-around">
				<div class='col-6'>
					<label class="font-weight-bold" for="From date">From Date</label><br/>
					<input type="date" name="fromdate"/>
				</div>
				<div class='col-6'>
					<label class="font-weight-bold" for="To date">To Date</label><br/>
					<input type="date" name="todate"/>
				</div>
			</div>
			<input type="submit" value="Submit" class="text-center btn btn-primary" /> 
		</form>
	</section>

	<br/>
	<section class="table-responsive">
		<table class="table table-sm table-hover table-bordered">
			<thead class="thead-light">
			<tr>
				<th>Product ID</th>
				<th>Product Name</th>
				<th>Quantity Sold</th>
				<th>Price</th>
				<th>Date of Manufacture</th>
				<th>Expiry Date</th>
				<th>Customer Mobile Number</th>
				<th>Date of Transaction</th>
			</tr>
			</thead>

			<tbody>
			@if(isset($data))
			@foreach($data as $d)
			<tr>
				<td>{{$d -> seller_product_id}}</td>
				<td>{{$d -> product_name}}</td>
				<td>{{$d -> quantity_sold}}</td>
				<td>{{$d -> selling_price}}</td>
				<td>{{$d -> date_of_manufacture}}</td>
				<td>{{$d -> expiry_date}}</td>
				<td>{{$d -> customer_mobile}}</td>
				<td>{{$d -> date_of_transaction}}</td>
			</tr>
			@endforeach
			@endif
			</tbody>
			
		</table>
	</section>
	<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
</body>
</html>