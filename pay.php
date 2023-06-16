<?php
include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
   header('location:user_login.php');
}

?>

<?php include 'components/user_header.php'; ?>

<?php 
// include('components/user_header.php');
?> 
<title>Payment __shopie</title>
<?php include('components/container.php');?> 
	<div class="welcome-section">
		<h1>Welcome to Payment Page</h1>
	</div>
	<br><br><br>
<?php
require('config.php');
require('razorpay-php/Razorpay.php');
// session_start();
use Razorpay\Api\Api;

// Create an instance of the Razorpay Api
$api = new Api($keyId, $keySecret);

$grand_total = 0;
$cart_items[] = '';
$select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
$select_cart->execute([$user_id]);
if($select_cart->rowCount() > 0){
   while($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)){
      $cart_items[] = $fetch_cart['name'].' ('.$fetch_cart['price'].' x '. $fetch_cart['quantity'].') - ';
      $total_products = implode($cart_items);
      $grand_total += ($fetch_cart['price'] * $fetch_cart['quantity']);
?>
<p> <?= $fetch_cart['name']; ?> <span>(<?= '$'.$fetch_cart['price'].'/- x '. $fetch_cart['quantity']; ?>)</span> </p>
<?php
   }
}else{
   echo '<p class="empty">your cart is empty!</p>';
}

$orderData = [
    'receipt'         => 3456,
    'amount'          => $grand_total * 100,
    'currency'        => "INR",
    'payment_capture' => 1
];
$razorpayOrder = $api->order->create($orderData);
$razorpayOrderId = $razorpayOrder['id'];
$_SESSION['razorpay_order_id'] = $razorpayOrderId;
$displayAmount = $amount = $orderData['amount'];
if ($displayCurrency !== 'INR') {
    $url = "https://api.fixer.io/latest?symbols=$displayCurrency&base=INR";
    $exchange = json_decode(file_get_contents($url), true);

    $displayAmount = $exchange['rates'][$displayCurrency] * $amount / 100;
}
$data = [
    "key"               => $keyId,
    "amount"            => $grand_total,
    // "name"              => $_POST['item_name'],
    // "description"       => $_POST['item_description'],
    "image"             => "",
    "prefill"           => [
    // "name"              => $_POST['cust_name'],
    // "email"             => $_POST['email'],
    // "contact"           => $_POST['contact'],
    ],
    "notes"             => [
    // "address"           => $_POST['address'],
    "merchant_order_id" => "12312321",
    ],
    "theme"             => [
    "color"             => "#F37254"
    ],
    "order_id"          => $razorpayOrderId,
];

if ($displayCurrency !== 'INR')
{
    $data['display_currency']  = $displayCurrency;
    $data['display_amount']    = $displayAmount;
}

$json = json_encode($data);


require("manual.php");
?>

<?php include('components/footer.php'); ?>
