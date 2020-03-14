<?php

// Set variables for our request
$shop = $_GET['shop'];
$api_key = "15eff4170b2b9eef43e2f7b618f2a005";
$scopes = "read_orders,write_products,read_content,write_content,read_themes,write_themes,read_products,read_product_listings,read_locations,read_script_tags,write_script_tags,read_checkouts,write_checkouts,read_price_rules,write_price_rules";
$redirect_uri = "http://localhost/tax/generate_token.php";

// Build install/approval URL to redirect to
$install_url = "https://" . $shop . ".myshopify.com/admin/oauth/authorize?client_id=" . $api_key . "&scope=" . $scopes . "&redirect_uri=" . urlencode($redirect_uri);

// Redirect
header("Location: " . $install_url);
die();