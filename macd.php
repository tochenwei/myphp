<?php
class Macd{

    /**
     * 计算ema的通用方法
     * @param $n
     * @param $end 当前收盘价
     * @param $ema 上一次的ema
     */
    private static function golEma($end,$ema_prev,$n){
        $a = bcdiv(2,$n+1,6);
        $b = bcsub($end,$ema_prev,6);
        $c = bcmul($a,$b,6);
        return bcadd($c,$ema_prev,4);
    }

    public static function ema5($end,$ema_prev){
        return self::golEma($end,$ema_prev,5);
    }

    public static function ema12($end,$ema_prev){
        return self::golEma($end,$ema_prev,12);
    }

    public static function ema26($end,$ema_prev){
        return self::golEma($end,$ema_prev,26);
    }

    public static function ema54($end,$ema_prev){
        return self::golEma($end,$ema_prev,54);
    }


    public static function dif_12_26($ema12,$ema26){
        return bcsub($ema12,$ema26,4);
    }

    private static function golDea($dif,$dea_prev,$n){
        $a = bcdiv(2,$n+1,6);
        $b = bcdiv($n-1,$n+1,6);
        $c = bcmul($a,$dif,6);
        $d = bcmul($b,$dea_prev,6);
        return bcadd($c,$d,4);
    }

    public static function dea9($dif,$dea_prev){
        return self::golDea($dif,$dea_prev,9);
    }

    public static function bar9($dif,$dea){
        $a = bcsub($dif,$dea,6);
        return bcmul(2,$a,4);
    }



}