#[A simple shopping cart](http://shoppingcart.natelord.org)

*A coding assignment from a potential employer*

##Instructions:

Create an MV* app split into three parts

1. A form to add new products to a "shop"
2. A list that represents products in the shop
3. A subset list of selected items from the shop

###Add an Item Form

The form can be very simple - the title of a product, the price (in $) and a numeric value representing quantity of stock to add. When submitted, the product appears in a list of all products added (we'll call this the Inventory List). Bonus points for validation if all fields are not complete (or invalid format).

###Inventory List

The Inventory List will list all products added through the form, with the ability to add an item to the Shopping Basket with an Add button next to each product. Multiple quantities of each item can be added to the shopping basket, and when no more stock is available for a particular item, that item will be displayed in a disabled state.

###Shopping Basket

The Shopping Basket will display a subset of products added from the Inventory List. Each row will include the product title, the quantity and a button to remove all items of that product from the basket. When removed, the product is removed from the shopping basket and the quantity of the Inventory List is updated accordingly.
