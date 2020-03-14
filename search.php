<?php
require_once("inc/connect.php");
include_once("inc/functions.php");

$search_results = '';
$search = $_POST['term'];
$shop = $_POST['shop'];

$sql = "SELECT * FROM stores WHERE store_url='".$shop.".myshopify.com' LIMIT 1";
$results = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($results);

$token = $row['access_token'];

$products = shopify_call($token, $shop, '/admin/api/2020-01/products.json',array('fields' => 'id,title,variants'),'GET');

$products = json_decode($products['response'], JSON_PRETTY_PRINT);

foreach ($products as $product) {
    foreach ($product as $key => $value) {
        if (stripos($value['title'],$search) !== false) {
            $search_results .= '<p> Product ID: '. $value['id'] .' <br/>';
            $search_results .= 'Product title: '. $value['title'] . '<br/>'; 
            $variants = $value['variants'];
            foreach ($variants as $variant) {
                $search_results .= '<table>';

                foreach ($variant as $key_var => $value_var) {
                    $search_results .= '<tr>';
                    $search_results .= '<td style="border: 1px solid;padding:2px;">' . $key_var . '</td>'; 
                    $search_results .= '<td style="border: 1px solid;padding:2px;">' . $value_var . '</td>';
                    $search_results .= '</tr>';
                }
                $search_results .= '</table>';
                $search_results .= '<br/>';
            }
            $search_results .= '</p><br/><br/>';
        }        
    }
}


echo $search_results;