<?php
include '../functions.php';
echo get_head();
?>
		<section id="shopping-cart-section">
			<h1>Shopping cart</h1>
			<ul id="shopping-cart-list">
<?php
$dbLogin = db_login();
$connection = new PDO( $dbLogin[ 'dsn' ], $dbLogin[ 'user' ], $dbLogin[ 'pass' ] );
$statement = $connection->query( 'SELECT * FROM cart_contents' );
$line_total = 0;
$cart_total = 0;

while( $row = $statement->fetch( PDO::FETCH_ASSOC ) ) {
	$id = $row[ 'id' ];
	$name = $row[ 'name' ];
	$price = $row[ 'price' ];
	$quantity = $row[ 'quantity' ];
	$line_total = $price * $quantity;
	$cart_total = $cart_total + $line_total;
?>

				<li class="shopping-cart-item" data-id="<?php echo $id; ?>">
					<div class="line-summary">
						<strong class="item-name"><?php echo $name; ?></strong> :
						<span class="item-price">$<?php echo $price; ?></span> x
						<samp class="item-quantity"><?php echo $quantity; ?></samp>
					</div>
					<div class="line-total-and-remove">
						<samp class="line-total">$<?php echo $line_total; ?></samp> 
						<button class="remove-item-from-cart" type="button">Remove</button>
					</div>
				</li>
<?php
}
?>
<!--
				<li class="shopping-cart-item" data-product-name="widget">
					<div class="line-summary">
						<strong class="item-name">widget</strong> :
						<span class="item-price">$1,000.00</span> x
						<samp class="item-quantity">9</samp>
					</div>
					<div class="line-total-and-remove">
						<samp class="line-total">$9,000.00</samp> 
						<button class="remove-item-from-cart" type="button">Remove</button>
					</div>
				</li>
-->
				<li id="sum-of-lines-line">total: <output id="shopping-cart-total">$<?php echo $cart_total; ?></output></li>
			</ul>
			<p id="keep-shopping-link-wrap"><a href="<?php echo get_site_url() . 'inventory/' ?>">Keep shopping</a></p>
		</section>
<?php
echo get_foot();
?>