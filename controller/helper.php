<?php

//PHP helper functions


//takes a url and pulls out objects that you can easily read.
function refineData($url){
    
//client url in order to use data in code
$data = curl_init($url);
curl_setopt($data,CURLOPT_RETURNTRANSFER,1);
$json = curl_exec($data); 
$refined = json_decode($json, true);
return $refined;
}

function addToCart($id){
    array_push($_SESSION['cart'], $id);
    printProduct($_SESSION['item']);
    printAddedCart();
    
}

function showCart(){
    $cartSize = 0;
    $sum = 0;
    if(isset($_SESSION['cart']) && sizeof($_SESSION['cart'])>0){
        
        $cartSize = sizeof($_SESSION['cart']);
        while (list ($key, $val) = each ($_SESSION['cart'])) {
        
            $cartProductUrl ="http://localhost:8080/Store/webresources/rest.product/" . $val;
            $cartProduct = refineData($cartProductUrl);
            //keep adding up the prices to get the sum of all products.
            $sum = $sum + $cartProduct["price"];
            
            printProductSummary($cartProduct);
            printDeleteProductButton($key);
        }    
    }
    printCartSummary($cartSize, $sum);
}
