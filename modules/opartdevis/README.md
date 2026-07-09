# OpartDevis

Quotation module for Prestashop

### Troubleshooting :

#### Ecotax and discounts :

OpartDevis has to follow the Prestashop's way of product price calculation.

On the other hand, to stick with the law, discounts can't be applied on ecotax but Prestashop does so since the 1.6.1.0 version.

If your shop applies ecotax and discounts on products, you'll have to override a method from classes/Product.php in order to fix this bug.

How to fix the bug ? 

1. Create an override of classes/Product.php and copy in it the method `priceCalculation` from the original class of your Prestashop
2. Move the block that concerns ecotax after the condition block `if ($only_reduct ...)`

See this bug report for more information : https://github.com/PrestaShop/PrestaShop/issues/9600
