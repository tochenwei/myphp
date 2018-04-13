<?php
function post($url, array $data,$json=false,$userAgend='')
{
    $ch = curl_init();
    
    if($json){
        $headers = [
            'Authorization: Bearer 111111111111-22222222222222'
        ];
        $req_data = json_encode($data);
        $headers[] = 'Content-Type: application/json; charset=utf-8';
        $headers[] = 'Content-Length:' . strlen($req_data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }else{
        $req_data = http_build_query($data);
    }
    print_r($req_data);
    curl_setopt($ch, CURLOPT_URL, $url);
    if (strpos($url, 'https') === 0) {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    }
    if($userAgend) {
        curl_setopt($ch, CURLOPT_USERAGENT,$userAgend);
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3); // 连接超时（秒）
    curl_setopt($ch, CURLOPT_TIMEOUT, 4); // 执行超时（秒）
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $req_data);
    $outPut = curl_exec($ch);
     
    curl_close($ch);

    return $outPut;
}

$url="https://api-fxpractice.oanda.com/v3/accounts/101-011-8115984-001/orders";
$arr = [
    /**
    public class OrderState
   {
      public const string Pending = "PENDING";
      public const string Filled = "FILLED";
      public const string Triggered = "TRIGGERED";
      public const string Cancelled = "CANCELLED";
   }
   public class OrderType
   {
      public const string Market = "MARKET";
      public const string Limit = "LIMIT";
      public const string Stop = "STOP";
      public const string MarketIfTouched = "MARKET_IF_TOUCHED";
      public const string TakeProfit = "TAKE_PROFIT";
      public const string StopLoss = "STOP_LOSS";
      public const string TrailingStopLoss = "TRAILING_STOP_LOSS";
   }
    */
    'order'=>[
    'instrument'=>'EUR_USD',
    'units'=>2,
    // 'side'=>'sell',
    'type'=>'MARKET',
    ]
];
$ret = post($url,$arr,1);
var_dump($ret);