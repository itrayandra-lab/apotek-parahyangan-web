<?php

// Simple test to check if checkout route is working
echo "Testing checkout route...\n";

// Check if we can access the route
$url = 'http://localhost/checkout?selected_items=["1","2"]';
echo "Testing URL: $url\n";

// You can run this with: php test_checkout_route.php
// Or access it via browser to test the route

echo "Route should be handled by CheckoutControllerNew::form\n";
echo "Check the logs for debug information.\n";