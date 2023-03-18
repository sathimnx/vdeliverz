<?php

use App\Order;

function cusCouponAlert($data){
    $body = config('constants.coupon_added.c_push');
    foreach ($data as $key => $value) {
        $body = str_replace('{#'.$key.'#}', $value, $body);
    }
    $result['title'] = config('constants.coupon_added.title');
    $result['body'] = $body;
    return $result;
}

function getContent($datas, $content){
    foreach ($datas as $key => $value) {
        $content = str_replace('{#'.$key.'#}', $value, $content);
    }
    return $content;
}

function totalDeliveryCharges($orders){
    $total = 0;
    foreach ($orders as $key => $order) {
        $total += $order->cart->delivery_charge;
    }
    return $total;
}

function totalOrderDiscounts($orders){
    $total = 0;
    foreach ($orders as $key => $order) {
        $total += $order->cart->coupon_amount;
    }
    return $total;
}

function totalOrderCommission($orders){
    $total = 0;
    foreach ($orders as $key => $order) {
        $total += $order->comission;
    }
    return $total;
}

function totalShopEarnings($orders){
    $total = 0;
    foreach ($orders as $key => $order) {
        $total += str_replace(",", "", $order->cart->total_amount) - str_replace(",", "", $order->comission);
    }
    return $total;
}

function currencyFormatter($value){
    switch ($value) {
        case $value > 1000:
            $value = number_format($value / 1000, 2) .' K';
            break;
        case $value > 1000000:
            $value = number_format($value / 1000000, 2) .' M';
            break;
        default:
            $value;
            break;
    }
    return $value;
}