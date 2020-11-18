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
		#Graph{
			width:60%;
			height:30%;
			margin:0 auto;
		}

		.insights-left{
			background: #ffffff;  /* fallback for old browsers */
            background: -webkit-linear-gradient(to right, #ffffff,#dcdcdc);  /* Chrome 10-25, Safari 5.1-6 */
            background: linear-gradient(to right, #ffffff,#dcdcdc); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
            padding:1%;
			/* width:60%;
			float:left; */
		}

		.insights-right{
			background: #ffffff;  /* fallback for old browsers */
            background: -webkit-linear-gradient(to left,#ffffff,#dcdcdc);  /* Chrome 10-25, Safari 5.1-6 */
            background: linear-gradient(to left,#ffffff,#dcdcdc); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
            padding:1%;
			/* width:60%;
			float:right; */
		}

	</style>

	<title>Sales Analysis</title>
</head>
<body>
	<header>
		<h1 class="text-center display-4 bg-dark text-light p-2 rounded">Sales Analysis</h1>
	</header>
	
	<br/>

	<section class="container text-center">
		@if($status == "PROFIT")
			<h3 class="bg-success text-light p-2 rounded-pill">{{$status}}</h3>
		@elseif($status =="LOSS")
			<h3 class="bg-danger text-light p-2 rounded-pill">{{$status}}</h3>
		@else
		<h3 class="bg-warning text-dark p-2 rounded-pill">{{$status}}</h3>
		@endif
	</section>
	
	<br/>
	
	<section id="Graph">
        {!! $chart->container() !!}
        {!! $chart->script() !!}
	</section>
	
	<div class="text-center">
		Since the last 30 days...
	</div>
	
	<div class="container text-center">
		<div class="row justify-content-around">
			<div class="col-6">
				<b>Amount Invested = &#8377;{{$investedAmount}} </b>
			</div>

			<div class="col-6">
				<b>Revenue = &#8377;{{$totalAmount}} </b>
			</div>
		</div>
	</div>
	
	<br/>

	<div class="container text-center bg-primary text-light rounded p-2">
		@if($ProfitPercentage == NULL && $BreakevenMessage == NULL)
			<h4>
				{{$Breakeven}}
				<br/>
				<br/>
				Don't worry. You got this!
			</h4>
		@elseif($ProfitPercentage == NULL)
		<h4>
				{{$BreakevenMessage}}
				<br/>
				<br/>
				The only way is up from here! You got this!
			</h4>
		@else
			<h4>
				{{$ProfitPercentage}}
				<br/>
				<br/>
				Your establishment is doing great!
			</h4>
		@endif
	</div>
	
	<br/>
	<div class="text-center"><h3>Do you want to view the <a href="/manufacturer_transaction_history">transaction history</a> ?</h3></div>
	</br>
	<!-- Insights section -->
	<section>
		<h2 class="text-center text-light bg-dark p-1 rounded ">Insights</h2>

		<div class="text-dark rounded mt-2 text-center insights-left font-weight-bold">
			<h3>Most Selling Product</h3>
			The most selling product in your store is {{$MostSellingProduct}}. It has sold {{$MSQuantity}} units since the last 30 days! <br/>
			Woah! You might want to invest more on that product! 
		</div>

		<div class="text-dark rounded text-center font-weight-bold insights-right mt-2">
			<h3>Least Selling Product</h3>
			The least selling product in your store is {{$LeastSellingProduct}}. It has sold {{$LSQuantity}} units since the last 30 days. <br/>
			Looks like your customers are not interested in that product. Invest less on that. 
		</div>

		<div class="text-dark rounded text-center font-weight-bold insights-left mt-2">
			<h3>Purchase Frequency</h3>
			On an average, your customers request {{$PurchaseFrequency}} products on every order.
		</div>
		
		<div class="text-dark rounded text-center font-weight-bold insights-right mt-2">
			<h3>Total Number Of Customers</h3>
			Since the last 30 days, {{$TotalCustomers}} customers have ordered from your store.
		</div>

		<div class="text-dark rounded text-center font-weight-bold insights-left mt-2">
			<h3>Highest Revenue Earned</h3>
			The highest revenue that your store has earned is on {{$RMEDate}}<br/>
			Your store made &#8377; {{$RMEAmount}} 
		</div>


		<div class="text-dark rounded p-2 text-center font-weight-bold insights-right mt-2 mb-2">
			<h3>Least Revenue Earned</h3>
			Least revenue that your store has earned is on {{$RLEDate}}<br/>
			Your store made &#8377; {{$RLEAmount}}
		</div>

	</section>
	<!-- End of Insights -->

	<br/>

	<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script> 
    <!--Chartjs CDN-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js" integrity="sha512-d9xgZrVZpmmQlfonhQUvTR7lMPtO7NkZMkA0ABN3PHCbKA5nqylQ/yWlFAyY6hYgdF1Qh6nYiuADWwKB4C2WSw==" crossorigin="anonymous"></script>
</body>
</html>