<?php
//PHP printout FUNCTIONS

//prints out the details of a specific product. The form is a button for adding the product to a cart.
function printProduct($product){

?>
<p>Name : <?php echo $product["name"] ?></p>
<p>Category : <?php echo $product["category"] ?></p>
<p>Price : <?php echo $product["price"] ?></p>

<form method="POST" action="index.php">
    <input type="hidden" name="id" value="<?php echo $product["id"] ?>">
    <input type="submit" name="add"  value="Add to cart">
</form>
<?php
}

//prints out the details only, to be used on the shopping cart page, where I didn't want the add button.
function printProductSummary($product){
    
?>
<p>Name : <?php echo $product["name"] ?></p>
<p>Category : <?php echo $product["category"] ?></p>
<p>Price : <?php echo $product["price"] ?></p>

<?php
}

function printCategoryLinks($categories){
    
//link for all items "category", not taken from the db
?><a href=<?php echo "http://localhost/StorePhp/index.php" ?>>All</a>
   <?php

   
if(!empty($categories)){
    foreach ($categories as $cat) {?>
    <a href=<?php echo "http://localhost/StorePhp/index.php?action=get_categories&name=" . urlencode($cat["name"]) ?>><?php echo $cat["name"] ?></a>
   <?php
    }
}  
?>
    <a href="http://localhost/StorePhp/index.php?action=get_cart">See Shopping Cart</a>
</br></br>

<?php   

}

//prints out name contained in a json array, and links to their full details.
function printProductLinks($items){
    
    if(!empty($items)){ 
        foreach ($items as $i) {
        ?>
<a href=<?php echo "http://localhost/StorePhp/index.php?action=get_product&id=" . $i["id"] ?>><?php echo $i["name"] ?></a><br/>
   
        <?php

        }
    }else{
        echo "No products found";
    }
}

function printDeleteProductButton($id){
    
    ?>
    <a href=<?php echo "http://localhost/StorePhp/index.php?action=get_cart&delete=" . $id ?>>delete item</a><br>
    <?php
}

function printCartSummary($size, $price){
    ?>
    <p><?php echo "Number of products in the cart = ".$size."<br>"; ?></p>
    <p>Total price: <?php echo $price ?></p><br>
    <?php
    if($size > 0){
        ?>
        <a href="http://localhost/StorePhp/index.php?action=get_cart&order">make order</a>
        <?php
    }
    
}
function printAddedCart(){
    echo "<br>Product added to shopping cart!";
}

