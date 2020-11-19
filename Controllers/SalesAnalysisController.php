<?php

namespace App\Http\Controllers;
use DB;
use Illuminate\Http\Request;
use App\Charts\SampleChart;
use Illuminate\Support\Arr;
use Carbon\Carbon;

class SalesAnalysisController extends Controller
{
    public function sellerSalesAnalysis()
    {   
        // Variable Declaration to use with compact function //
        $ProfitPercentage = NULL; $Breakeven = NULL; $BreakevenMessage = NULL;

        //Invested Amount (30 Days)
        $investedAmountTemp = DB::table('items_sold_manufacturer')
            ->select(DB::raw('sum(selling_price * quantity) as amount')) 
            ->where('sellers_email', '=', session('seller'))
            ->where('date_of_transaction', '>', Carbon::today()->subDays(30))
            ->get();
 
        $investedAmountArr = Arr::pluck($investedAmountTemp, 'amount');
        $investedAmount = array_pop($investedAmountArr); 


        // Revenue (30 Days) //
        $amountTemp = DB::table('items_sold_seller')
            ->select(DB::raw('date_of_transaction, sum(selling_price * quantity_sold) as amount'))
            ->join('customer_details', function ($join) {
                $join->on('items_sold_seller.customer_mobile', '=', 'customer_details.customer_mobile'); 
            })->groupBy('date_of_transaction')
            ->where('items_sold_seller.sold', '=', 1)
            ->where('customer_details.sold', '=', 1)
            ->where('items_sold_seller.sellers_email', '=', session('seller'))
            ->where('date_of_transaction', '>', Carbon::today()->subDays(30))
            ->get();
        
        $amount = Arr::pluck($amountTemp,'amount'); 
        $totalAmount = array_sum($amount);
        
        // Profit/Loss Calculations //
        if($totalAmount == $investedAmount)
        {
            $status = "BREAKEVEN";
            $BreakevenMessage = "You made the amount you have invested!";
        }
        
        else if($totalAmount > $investedAmount)
        {
            $status = "PROFIT";
            $NetProfitMargin = round((($totalAmount - $investedAmount) / $totalAmount) * 100, 1);
            $ProfitPercentage = "Net Profit Margin: $NetProfitMargin %";   
        }
        
        else
        {
            $status = "LOSS";
            $LossMargin = $investedAmount - $totalAmount;
            $Breakeven = "You need to earn more $LossMargin rupees to breakeven";
        }

        
        // INSIGHTS //
        // Most Selling Product and Least Selling Product (30 Days) //
        $productsTemp = DB::table('items_sold_seller')
            ->select(DB::raw('product_name, sum(quantity_sold) as quantity'))
            ->join('customer_details', function ($join) {
                $join->on('items_sold_seller.customer_mobile', '=', 'customer_details.customer_mobile'); 
            })
            ->where('items_sold_seller.sellers_email', '=', session('seller'))
            ->where('items_sold_seller.sold', '=', 1)
            ->where('customer_details.sold', '=', 1)
            ->where('customer_details.date_of_transaction', '>', Carbon::today()->subDays(30))
            ->groupBy('product_name')
            ->get(); 
        
        $productsArr = Arr::pluck($productsTemp, 'quantity', 'product_name');
        $products =  Arr::sort($productsArr);

        $LeastSellingProduct = key($products);
        $LSQuantity = $products[$LeastSellingProduct];
        
        end($products);
        $MostSellingProduct = key($products);
        $MSQuantity = $products[$MostSellingProduct]; 
        reset($products);

        
        // Average number of products customers buy //
        $TotProdTemp = DB::table('items_sold_seller')
            ->select(DB::raw('sum(quantity_sold) as quantity'))
            ->join('customer_details', function ($join) {
                $join->on('items_sold_seller.customer_mobile', '=', 'customer_details.customer_mobile'); 
            })
            ->where('items_sold_seller.sellers_email', '=', session('seller'))
            ->where('items_sold_seller.sold', '=', 1)
            ->where('customer_details.sold', '=', 1)
            ->where('customer_details.date_of_transaction', '>', Carbon::today()->subDays(30))
            ->get(); 

        $TotalProdArr = Arr::pluck($TotProdTemp, 'quantity');
        $TotalProductsSold = array_pop($TotalProdArr); 

    
        $TotCust = DB::table('customer_details')
            ->select('customer_details.customer_name')
            ->join('items_sold_seller', function ($join) {
                $join->on('customer_details.customer_mobile', '=', 'items_sold_seller.customer_mobile'); 
            })
            ->where('customer_details.sellers_email', '=', session('seller'))
            ->where('items_sold_seller.sold', '=', 1)
            ->where('customer_details.sold', '=', 1)
            ->where('customer_details.date_of_transaction', '>', Carbon::today()->subDays(30))
            ->groupBy('customer_details.created_at')
            ->get(); 

        
        $TotalCustomers = count($TotCust);

        $PurchaseFrequency = round($TotalProductsSold / $TotalCustomers);
    
        
        // Most and Least Amount of Revenue Earned //
        $revenueArr = Arr::pluck($amountTemp, 'amount', 'date_of_transaction');
        $revenuesrt = Arr::sort($revenueArr);

        $RLEDate = key($revenuesrt);
        $RLEAmount = $revenuesrt[$RLEDate];

        end($revenuesrt);
        $RMEDate = key($revenuesrt);
        $RMEAmount = $revenuesrt[$RMEDate];
        reset($revenuesrt);

        
        // Transaction Dates (30 Days) //
        $tDatesTemp = DB::table('items_sold_seller')
        ->select('date_of_transaction')
        ->join('customer_details', function ($join) {
            $join->on('items_sold_seller.customer_mobile', '=', 'customer_details.customer_mobile'); 
        })
        ->where('items_sold_seller.sold', '=', 1)
        ->where('customer_details.sold', '=', 1)
        ->where('items_sold_seller.sellers_email', '=', session('seller'))
        ->where('date_of_transaction', '>', Carbon::today()->subDays(30))
        ->distinct()
        ->get();
        
        return $transactionDates = Arr::pluck($tDatesTemp,'date_of_transaction');
        
        // Graph //
        $chart = new SampleChart;
        $chart -> labels($transactionDates);
        $chart -> dataset('Revenue', 'bar', $amount);
        return view('Seller Sales Analysis',
            compact('chart', 'status', 'ProfitPercentage', 'investedAmount', 'Breakeven', 
            'totalAmount', 'MostSellingProduct', 'MSQuantity','LeastSellingProduct', 'LSQuantity', 'PurchaseFrequency', 
            'RMEDate', 'RMEAmount', 'RLEDate', 'RLEAmount','TotalCustomers','BreakevenMessage'));

    }

    public function manufacturerSalesAnalysis()
    {
        // Variable Declaration to use with compact function //
        $ProfitPercentage = NULL; $Breakeven = NULL; $BreakevenMessage = NULL;

        //Invested Amount (30 Days)
        $investedAmountTemp = DB::table('items_sold_manufacturer')
            ->select(DB::raw('sum(price * quantity) as amount'))
            ->where('manufacturers_email', '=', session('manufacturer'))
            ->where('date_of_transaction', '>', Carbon::today()->subDays(30))
            ->get();
        
        $investedAmountArr = Arr::pluck($investedAmountTemp, 'amount');
        $investedAmount = array_pop($investedAmountArr); 

        // Revenue (30 Days) //
        $amountTemp = DB::table('items_sold_manufacturer')
            ->select(DB::raw('date_of_transaction, sum(selling_price * quantity) as amount'))
            ->groupBy('date_of_transaction')
            ->where('manufacturers_email', '=', session('manufacturer'))
            ->where('date_of_transaction', '>', Carbon::today()->subDays(30))
            ->get();
        
        $amount = Arr::pluck($amountTemp,'amount'); 
        $totalAmount = array_sum($amount);

        // Profit/Loss Calculations //
        if($totalAmount == $investedAmount)
        {
            $status = "BREAKEVEN";
            $BreakevenMessage = "You made the amount you have invested!";
        }
        
        else if($totalAmount > $investedAmount)
        {
            $status = "PROFIT";
            $NetProfitMargin = round((($totalAmount - $investedAmount) / $totalAmount) * 100, 1);
            $ProfitPercentage = "Net Profit Margin: $NetProfitMargin %";   
        }
        
        else
        {
            $status = "LOSS";
            $LossMargin = $investedAmount - $totalAmount;
            $Breakeven = "You need to earn more $LossMargin rupees to breakeven";
        }
        
        // INSIGHTS //
        // Most Selling Product and Least Selling Product (30 Days) //
        $productsTemp = DB::table('items_sold_manufacturer')
            ->select(DB::raw('product_name, sum(quantity) as quantity'))
            ->where('manufacturers_email', '=', session('manufacturer'))
            ->where('date_of_transaction', '>', Carbon::today()->subDays(30))
            ->groupBy('product_name')
            ->get(); 
        
        $productsArr = Arr::pluck($productsTemp, 'quantity', 'product_name');
        $products =  Arr::sort($productsArr);

        $LeastSellingProduct = key($products);
        $LSQuantity = $products[$LeastSellingProduct];
        
        end($products);
        $MostSellingProduct = key($products);
        $MSQuantity = $products[$MostSellingProduct]; 
        reset($products);

        // Average number of products customers buy //
        $TotProdTemp = DB::table('items_sold_manufacturer')
            ->select(DB::raw('sum(quantity) as quantity'))
            ->where('manufacturers_email', '=', session('manufacturer'))
            ->where('date_of_transaction', '>', Carbon::today()->subDays(30))
            ->get(); 

        $TotalProdArr = Arr::pluck($TotProdTemp, 'quantity');
        $TotalProductsSold = array_pop($TotalProdArr); 

    
        $TotCust = DB::table('items_sold_manufacturer')
            ->select('sellers_email')
            ->where('manufacturers_email', '=', session('manufacturer'))
            ->where('date_of_transaction', '>', Carbon::today()->subDays(30))
            ->groupBy('created_at')
            ->get(); 

        
        $TotalCustomers = count($TotCust);

        $PurchaseFrequency = round($TotalProductsSold / $TotalCustomers);

        // Most and Least Amount of Revenue Earned //
        $revenueArr = Arr::pluck($amountTemp, 'amount', 'date_of_transaction');
        $revenuesrt = Arr::sort($revenueArr);

        $RLEDate = key($revenuesrt);
        $RLEAmount = $revenuesrt[$RLEDate];

        end($revenuesrt);
        $RMEDate = key($revenuesrt);
        $RMEAmount = $revenuesrt[$RMEDate];
        reset($revenuesrt);

        // Transaction Dates (30 Days) //
        $tDatesTemp = DB::table('items_sold_manufacturer')
        ->select('date_of_transaction')
        ->where('manufacturers_email', '=', session('manufacturer'))
        ->where('date_of_transaction', '>', Carbon::today()->subDays(30))
        ->distinct()
        ->get();
        
        $transactionDates = Arr::pluck($tDatesTemp,'date_of_transaction');

        // Graph //
        $chart = new SampleChart;
        $chart -> labels($transactionDates);
        $chart -> dataset('Revenue', 'bar', $amount);

        return view('Manufacturer Sales Analysis',
            compact('chart', 'status', 'ProfitPercentage', 'investedAmount', 'Breakeven', 
            'totalAmount', 'MostSellingProduct', 'MSQuantity','LeastSellingProduct', 'LSQuantity', 'PurchaseFrequency', 
            'RMEDate', 'RMEAmount', 'RLEDate', 'RLEAmount','TotalCustomers','BreakevenMessage'));
    }

    public function sellerTransactionHistory()
    {   
        return view('Seller Transaction History');
    }

    public function displaySellerTransactionHistory(Request $request)
    {
        $fromdate = $request -> input("fromdate");
        $todate = $request -> input("todate");

        $data = DB::table('items_sold_seller')
            ->select('seller_product_id','product_name','quantity_sold','selling_price','date_of_manufacture','expiry_date',
            'customer_details.customer_mobile', 'customer_details.date_of_transaction')
            ->join('customer_details', function ($join) {
                $join->on('items_sold_seller.customer_mobile', '=', 'customer_details.customer_mobile'); 
            })
            ->where('items_sold_seller.sold', '=', 1)
            ->where('customer_details.sold', '=', 1)
            ->where('items_sold_seller.sellers_email', '=', session('seller'))
            ->whereBetween('date_of_transaction',[$fromdate,$todate])
            ->get();

        return view('Seller Transaction History',compact('data'));

    }

    public function manufacturerTransactionHistory()
    {   
        return view('Manufacturer Transaction History');
    }

    public function displayManufacturerTransactionHistory(Request $request)
    {
        $fromdate = $request -> input("fromdate");
        $todate = $request -> input("todate");

        $data = DB::table('items_sold_manufacturer')
            ->select('product_name','quantity','selling_price','date_of_manufacture','expiry_date',
            'sellers_email', 'date_of_transaction')
            ->where('manufacturers_email', '=', session('manufacturer'))
            ->whereBetween('date_of_transaction',[$fromdate,$todate])
            ->get();

        return view('Manufacturer Transaction History',compact('data'));

    }
}
