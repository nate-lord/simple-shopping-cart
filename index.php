<?php
include 'functions.php';
echo get_head();
?>
		<form id="add-product-form">
			<h1>Add a product to the database</h1>
			<label>
				Product name: <input id="product-name-input" type="text" placeholder="ex: widget" autocomplete="off" />
				<mark></mark>
			</label>
			<label>
				Price: <input id="product-price-input" type="number" placeholder="ex: 1000.00" min="0" autocomplete="off" /> $
				<mark></mark>
			</label>
			<label>
				Quantity: <input id="product-quantity-input" type="number" placeholder="ex: 4" min="0" autocomplete="off" />
				<mark></mark>
			</label>
			<div>
				<button id="add-product-to-database-button" type="button">Add product</button>
				<mark id="add-product-flag"></mark>
			</div>
			<div id="go-to-inventory-link-wrap">
				<a id="go-to-inventory-link" href="<?php echo get_site_url() . 'inventory/' ?>">Go to inventory</a>
			</div>
			<hr />
			<div>
				<button id="empty-database-button" type="button">Empty database</button>
				<mark id="reset-db-flag">resets the database. cannot be undone.</mark>
			</div>
		</form>
<?php
echo get_foot();
?>