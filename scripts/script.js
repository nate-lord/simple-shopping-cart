$( function() {
	var SHOPPING,
			S,
			F;
			
	SHOPPING = {
		
		settings: {
			baseUrl: 'http://shoppingcart.natelord.org/'
		},

		funcs: {
			
			addItemToCart: function( Item ) {
				/**
				* Sends an id and quantity to create or amend a row on the cartcontents database table.
				* On AJAX success, update the human readable string on the inventory list display quantity.
				* If quantity is now zero, disable the item.
				*
				* @param {object} Item has the id and quantity to be added or amended to the cartcontents database table
			  */
				
				$.ajax({
					type: 'POST',
					url: S.baseUrl + 'functions.php',
					data: {
						action: 'add_item_to_cart',
						id: Item.id,
						quantity: Item.quantity
					},
					success: function() {
						var $itemWrap = $( '.inventory-item[data-id="' + Item.id + '"]' ),
								$quantityInput = $itemWrap.find( '.item-quantity-input' ),
								$inventoryMaxText = $itemWrap.find( '.inventory-item-max' ),
								stock = parseInt( $quantityInput.attr( 'max' ), 10 ) - parseInt( Item.quantity, 10 );

						$quantityInput.val( '' );
						
						if ( stock > 0 ) {
							$quantityInput.attr( 'max', stock )
							$inventoryMaxText.text( 'out of ' + stock + ' in stock' );
						} else {
							$quantityInput.prop( 'disabled', true );
							$itemWrap
								.find( '.add-item-to-cart-button' )
									.prop( 'disabled', true );
							$inventoryMaxText.text( 'sold out' );
						}
					}
				});
			},
			
			addProductToDatabase: function( Product ) {
				/**
				* Sends a name, price, and quantity to create a new row in the inventory table on the database.
				* On AJAX success, if 'nope' is returned then the product name is already in use.
				* The product was not added and a human readable error is displayed.
				* Otherwise clear fields and temporarily display human readable message re success.
				*
				* @param {object} Product has the name, price, and quantity to be added to the inventory table
				* @see displayWarning
			  */
				
				$.ajax({
					type: 'POST',
					url: S.baseUrl + 'functions.php',
					data: {
						action: 'add_product_to_database',
						name: Product.name,
						price: Product.price,
						quantity: Product.quantity
					},
					success: function( data ) {
						var $flag;
						if ( data === 'nope' ) {
							F.displayWarning( $( '#product-name-input' ).parent(), 'Name already in use' );
							return;
						}
						
						$( '#product-name-input' ).val( '' );
						$( '#product-price-input' ).val( '' );
						$( '#product-quantity-input' ).val( '' );
						
						$flag = $( '#add-product-flag' );
						$flag.text( 'Successfully added product.' )
						
						window.setTimeout( function() {
							$flag.text( '' );
						}, 3500 );
					}
				});
			},
			
			bindUiActions: function() {
				/**
				* Binds all the UI actions in the program.
				*
				* @see init
			  */
				
				$( '.add-item-to-cart-button' ).on( 'click', function() {
					/**
				  * Click ev on the 'add to cart' btn on the invertory page.
					* The quantity value is tested via testQuantity.
					* If quantity is valid, the Ob Item with the item's quantity and database id is
					* passed to addItemToCart.
					* If quantity is invalid displayWarning shows an error.
					*
					* @see testQuantity
					* @see addItemToCart
					* @see displayWarning
				  */
					
					var $item = $( this ).parentsUntil( '.inventory-item' ).parent(),
							$itemQuantityInput = $item.find( '.item-quantity-input' ),
							Item = {},
							Quantity = F.testQuantity( $itemQuantityInput.val(), $itemQuantityInput.attr( 'max' ) );
							
					if ( Quantity.isValid ) {
						Item.id = $item.attr( 'data-id' );
						Item.quantity = Quantity.val;
						
						F.addItemToCart( Item );
					} else {
						F.displayWarning( $item, Quantity.error );
					}
				});
				
				$( 'body' ).on( 'focus', '.error input', function() {
					/**
				  * Delegated focus ev on <input>s that has an ancestor with the 'error' class.
					* Gets that ancestor and passes it to hideWarning.
					*
				  * @see displayWarning
					* @see hideWarning
				  */
					
					var $wrap = $( this ).parentsUntil( '.error' ).parent();
					
					if ( !$wrap.length ) {
						$wrap = $( this ).parent();
					}
					
				  F.hideWarning( $wrap );
				});
				
				$( '#add-product-to-database-button' ).on( 'click', function() {
					/**
				  * Click ev on the 'add product' btn on the 'add product to database' page.
					*
					* @see testProductVals
				  */
					
					F.testProductVals();
				});
				
				$( '#empty-database-button' ).on( 'click', function() {
					/**
				  * Click ev on the 'empty database' btn on the 'add product to database' page.
					*
					* @see emptyDatabases
				  */
					
					F.emptyDatabases();
				});
				
				$( '.remove-item-from-cart' ).on( 'click', function() {
					/**
				  * Click ev on the 'remove' btn on the 'shopping cart' page.
					* Passes it's ancestor <li> to removeLineItem to be destroyed.
					*
					* @see emptyDatabases
				  */
					
					var $line = $( this ).parentsUntil( '.shopping-cart-item' ).parent();
					
					F.removeLineItem( $line );
				});
			},
			
			displayWarning: function( $wrap, warning ) {
				/**
				* Displays human readable warning for an <input> error
				*
				* @param {jquery object} $wrap An ancestor of the invalid <input>
				* @param {string} warning Human readable warning
			  */
				
				$wrap
					.addClass( 'error' )
					.find( 'mark' )
						.text( warning );
			},
			
			emptyDatabases: function() {
				/**
				* Sends trigger to remove all rows from cartcontents and inventory tables on the database.
				* Temporarily display human readable message re success.
			  */
				
				$.ajax({
					type: 'POST',
					url: S.baseUrl + 'functions.php',
					data: {
						action: 'empty_databases'
					},
					success: function( data ) {
						var $resetDbFlag = $( '#reset-db-flag' ),
								oldText = $resetDbFlag.text();
								
						$resetDbFlag.text( 'Databases have been reset!' );
						
						window.setTimeout( function() {
							$resetDbFlag.text( oldText );
						}, 3500 );
					}
				});
			},
			
			hideWarning: function( $wrap ) {
				/**
				* Removes human readable warning for an <input> error
				*
				* @param {jquery object} $wrap An ancestor of the invalid <input>
			  */
				
				$wrap
					.removeClass( 'error' )
					.find( 'mark' )
						.text( '' );
			},
			
			removeLineItem: function( $line ) {
				/**
				* Removes <li> from the shopping cart.
				* Updates the total price <li> in the shopping cart.
				* Sends id to database to remove row from cartcontents table and update inventory table.
				*
				* @param {jquery object} $line The <li> from the shopping cart to be removed.
			  */
				
				var id = $line.attr( 'data-id' ),
						price = parseFloat( $line.find( '.line-total' ).text().substring( 1 ), 10 ),
						total = parseFloat( $( '#shopping-cart-total' ).text().substring( 1 ), 10 );
				
				$line.remove();
				
				$( '#shopping-cart-total' ).text( '$' + ( total - price ) );

				$.ajax({
					type: 'POST',
					url: S.baseUrl + 'functions.php',
					data: {
						action: 'amend_shopping_cart',
						id: id
					},
					success: function( data ) {
						console.log( data );
					}
				});
				
			},
			
			testName: function( name ) {
				/**
				* Tests if the inputed name for a new product in valid and returns result.
				*
				* @param {string} name The name to be tested
				* @returns {object} Result containing isValid bool and either error string (on fail) or valid name string (on success)
			  */
				
				var Result = {
					isValid: false
				};
				
				if ( !name.replace(/\s+/g, '').length ) {
					Result.error = 'cannot be blank';
					return Result;
				}

				Result.isValid = true;
				Result.val = name;
				
				return Result;
			},
			
			testPrice: function( price ) {
				/**
				* Tests if the inputed price for a new product in valid and returns result.
				*
				* @param {string} price The price to be tested
				* @returns {object} Result containing isValid bool and either error string (on fail) or valid price number (on success)
			  */
				var Result = {
							isValid: false
						},
						decimals;
				
				if ( !price.length ) {
					Result.error = 'price not formatted correctly';
					return Result;
				}
				
				if ( price.indexOf( '.' ) !== -1 ) {
					decimals = price.substring( price.indexOf( '.' ) + 1 );
					
					if ( decimals.length > 2 ) {
						Result.error = 'price can only have 2 decimal places';
						return Result;
					}
				}
				
				price = parseFloat( price );

				if ( price <= 0 ) {
					Result.error = 'price must be .01 and above';
					return Result;
				}
				
				Result.isValid = true;
				Result.val = price;
				
				return( Result );
			},
			
			testProductVals: function() {
				/**
			  * Passes all <input> vals on the form page to test for validity.
				* If all are valid, put them in Ob Product and pass them to addProductToDatabase.
				* Otherwise, pass each invalid <input>'s parent and human readable error string to displayWarning.
				*
				* @see testName
				* @see testPrice
				* @see testQuantity
				* @see addProductToDatabase
				* @see displayWarning
			  */
				
				var Name = F.testName( $( '#product-name-input' ).val() ),
						Price = F.testPrice( $( '#product-price-input' ).val() ),
						Product = {},
						Quantity = F.testQuantity( $( '#product-quantity-input' ).val() );
				
				if ( Name.isValid && Price.isValid && Quantity.isValid ) {
					Product = {
						name: Name.val,
						price: Price.val,
						quantity: Quantity.val
					};
					
					F.addProductToDatabase( Product );
					return;
				}
				
				if ( !Name.isValid ) {
					F.displayWarning( $( '#product-name-input' ).parent(), Name.error );
				}
				
				if ( !Price.isValid ) {
					F.displayWarning( $( '#product-price-input' ).parent(), Price.error );
				}
				
				if ( !Quantity.isValid ) {
					F.displayWarning( $( '#product-quantity-input' ).parent(), Quantity.error );
				}
			},
			
			testQuantity: function( quantity, max ) {
				/**
				* Tests if the inputed quantity for a product in valid and returns result.
				*
				* @param {string} quantity The quantity to be tested
				* @param {string} max Optional used to test if quantity is in range
				* @returns {object} Result containing isValid bool and either error string (on fail) or valid quantity number (on success)
			  */
				
				var Result = {
					isValid: false
				};
				
				if ( !quantity.length ) {
					Result.error = 'quantity not formatted correctly';
					return Result;
				}
				
				if ( quantity.indexOf( '.' ) !== -1 ) {
					Result.error = 'quantity must be a whole number';
					return Result;
				}
				
				quantity = parseInt( quantity, 10 );
				
				if ( quantity <= 0 ) {
					Result.error = 'quantity must be at least 1';
					return Result;
				}
				
				if ( max !== undefined && quantity > max ) {
					Result.error = 'quantity cannot exceed ' + max;
					return Result;
				}
				
				Result.isValid = true;
				Result.val = quantity;
				
				return Result;
			}
			
		},
		
		init: function() {
			/**
			* Starts program.
			*/
			
			F = this.funcs;
			S = this.settings;
			
			F.bindUiActions();
		}
	}
	SHOPPING.init();
});