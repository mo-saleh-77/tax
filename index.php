<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>App Tax</title>
</head>
<body>

<?php
require_once("inc/functions.php");
require_once("inc/connect.php");

$requests = $_GET;
$hmac = $_GET['hmac'];
$serializeArray = serialize($requests);
$requests = array_diff_key($requests,array('hmac' => ''));
ksort($requests);

$sql = "SELECT * FROM stores WHERE store_url='".$requests['shop']."' LIMIT 1";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

$token = $row['access_token'];
$shop_full = $row['store_url'];
$shop = str_replace('.myshopify.com','', $row['store_url']);

$image = '';
$title = '';

$collectionList = shopify_call($token, $shop, "/admin/api/2020-01/custom_collections.json", array(), 'GET');
$collectionList = json_decode($collectionList['response'], JSON_PRETTY_PRINT);
$collection_Id = $collectionList['custom_collections'][0]['id'];


$collects = shopify_call($token, $shop, "/admin/api/2020-01/collects.json", array("collection_id" => $collection_Id), 'GET');
$collects = json_decode($collects['response'], JSON_PRETTY_PRINT);


foreach ($collects as $collect) {
    foreach ($collect as $key => $value) {
        $products = shopify_call($token, $shop, "/admin/api/2020-01/products/" . $value['product_id'] . ".json", array(), 'GET');
        $products = json_decode($products['response'], JSON_PRETTY_PRINT);

        $images =  shopify_call($token, $shop, "/admin/api/2020-01/products/" . $value['product_id'] . "/images.json", array(), 'GET');
        $images = json_decode($images['response'], JSON_PRETTY_PRINT);

        foreach ($images as $image) {
            foreach ($image as $key => $value) {
                $image = $value['src'];
                echo "<img src='$image' alt='' style='width:100px;margin:10px 20px;display:inline-block'>";
            }        
        }

        $title = $products['product']['title'];
        $url = $products['product']['handle'];
        
        echo "<a target=_blank href=https://$shop_full/products/$url style=display:block>$title</a>";
    }
}
$countries = shopify_call($token, $shop, "/admin/api/2020-01/countries.json", array(), 'GET');
$countries = json_decode($countries['response'], JSON_PRETTY_PRINT);

$array_update = array(
    'country' => array(
        "id" => 227467952180,
        "tax" => 100.5
    )
);

$country_update_tax = shopify_call($token, $shop, "/admin/api/2020-01/countries/227467952180.json", array($array_update), 'PUT');
$country_update_tax = json_decode($country_update_tax['response'], JSON_PRETTY_PRINT);


foreach ($countries as $country) {
    
    foreach ($country as $details) {
    echo 'Country ID: '. $details['id'] .'<br/>';
    echo 'Country Name: '. $details['name'] .'<br/>';
    echo 'Tax: '. $details['tax'] .'<br/>';
    echo 'Country Code: '. $details['code'] .'<br/>';
    echo 'Tax Name: '. $details['tax_name'] .'<br/>';
       foreach ($details['provinces'] as $province) {
            echo $province['name'] .'<br/>';
            echo $province['tax'] .'<br/>';
        }
        
    }
    echo '<br/>';
 }

?>
<input type="text" name="searchInput" id="searchInput" placeholder="Search ..." style="display:block;margin-top:15px">
<input type="hidden" name="subdomain" value="<?php echo $shop;?>" class="subdomain">

<div class="product_list"></div>
<script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
<script>
    $('#searchInput').keypress(function(e){
        if(e.which == 13) {
            var search = $(this).val();
            var shop = $('.subdomain').val();
            $.ajax({
                type: 'POST',
                data: {
                    term: search,
                    shop: shop
                },
                url: 'search.php',
                dataType: 'html',
                success: function(response){
                    $('.product_list').html(response);
                }
            });
            return false;
        }
    });
</script>

</body>
</html>