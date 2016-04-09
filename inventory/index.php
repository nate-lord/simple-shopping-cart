<?php
include '../functions.php';
echo get_head();
?>
		<section id="inventory-section">
			<h1>Inventory list</h1>
			<ul id="inventory-list">
<?php
$dbLogin = db_login();
$connection = new PDO( $dbLogin[ 'dsn' ], $dbLogin[ 'user' ], $dbLogin[ 'pass' ] );
$statement = $connection->query( 'SELECT * FROM inventory' );

while( $row = $statement->fetch( PDO::FETCH_ASSOC ) ) {
	$id = $row[ 'id' ];
	$name = $row[ 'name' ];
	$price = $row[ 'price' ];
	$quantity = $row[ 'quantity' ];
?>
				<li class="inventory-item" data-id="<?php echo $id; ?>">
					<h2 class="item-name"><?php echo $name; ?></h2>
					<p>Price: <span class="item-price">$<?php echo $price; ?></span></p>
					<label>
						Quantity: <input class="item-quantity-input"<?php echo( $quantity === '0' ? ' disabled '  : ' ' ); ?>type="number" min="1" max="<?php echo $quantity; ?>" autocomplete="off" />
<?php
		if ( $quantity !== '0' ) {
?>
						<span class="inventory-item-max">out of <?php echo $quantity; ?> in stock</span>
<?php
		} else {
?>
						<span class="inventory-item-max">sold out</span>
<?php
		}
?>
						<mark></mark>
					</label>
					<p><button type="button"<?php echo( $quantity === '0' ? ' disabled '  : ' ' ); ?>autocomplete="off"  class="add-item-to-cart-button">Add to cart</button></p>
				</li>
<?php } ?>
			</ul>
			<nav>
				<p id="add-inventory-items-link-wrap"><a href="<?php echo get_site_url() ?>">Add more items to inventory</a></p>
				<p id="go-to-checkout-link-wrap"><a href="<?php echo get_site_url() . 'shopping-cart/' ?>">Go to checkout</a></p>
			</nav>
		</section>
<?php
echo get_foot();
?>