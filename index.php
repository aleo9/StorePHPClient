<!DOCTYPE html>

<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
    <form action="index.php" method="post">
    <input type="text" name="search">
    <button type="submit">Search</button>
    </form> 
        
<?php
session_start();

//make subcategory links
$categoryListUrl ="http://localhost:8080/Store/webresources/rest.categories";
$categories = refineData($categoryListUrl);

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

//pretty long if statement that directs you to the correct "page" depending on actions.
//first checks if you want to see a specific product
if(isset($_GET["action"]) && $_GET["action"] == "get_product"){
   
    //gets the correct product via the id variable sent along with the get_product action.
    $productUrl ="http://localhost:8080/Store/webresources/rest.product/" . $_GET["id"];
    //refineData is a function to make the data readable.
    $product = refineData($productUrl);
    //when looking at a product, a copy of it is stored in a session variable for convenience.
    $_SESSION['item'] = $product;
    //function that prints out the product info to the screen.
    printProduct($product);

//if you want to see all products
}else if(isset($_GET["action"]) && $_GET["action"] == "get_all"){
    //function that prints out the name of all products, with links to the full details.
    printAllProducts();

//move to the cart page    
}else if(isset($_GET["action"]) && $_GET["action"] == "get_cart"){
    
    //if a delete command was also given
    if(isset($_GET["delete"])){
        
        //the 2nd parameter is the id that is deleted.
        array_splice($_SESSION['cart'], $_GET["delete"], 1);
    }
    
    //if a order command was also given
    if(isset($_GET["order"])){
        
        //Ideally a method would be used to execute a order more thoroughly,
        //make sure that a confirmation mail gets sent, that the actual order information gets stored somewhere, etc,
        //but under the currrent scope, I think another method was unneccessary.
        //placeOrder();
        
        //create empty array
        $_SESSION['cart']=array();
        echo "Your order was placed!<br><br>";
    }
    
    //finally, the cart is printed.
    printCart();
    
  
//this gets all items under the wanted category
}else if(isset($_GET["action"]) && $_GET["action"] == "get_categories"){
    
    $categoryUrl ="http://localhost:8080/Store/webresources/rest.product/category/" . urldecode($_GET["name"]);
    $listByCategory = refineData($categoryUrl);
    
    if(!empty($listByCategory))
    { 
        foreach ($listByCategory as $i) {
        ?>
<a href=<?php echo "http://localhost/StorePhp/index.php?action=get_product&id=" . $i["id"] ?>><?php echo $i["name"] ?></a><br/>
   
        <?php
        }
    }

//search based on keyword stored as $_POST["search"].
//Doesn't work with spaces, and you need to include the first character in the product name
}else if(isset($_POST["search"])){
    
    $searchUrl ="http://localhost:8080/Store/webresources/rest.product/name/" . urldecode($_POST["search"]);
    $searchResult = refineData($searchUrl);
    
    if(!empty($searchResult)){ 
        foreach ($searchResult as $s) {
        ?>
<a href=<?php echo "http://localhost/StorePhp/index.php?action=get_product&id=" . $s["id"] ?>><?php echo $s["name"] ?></a><br/>
    
        <?php
        }
    }

//add item to shopping cart
}else if(isset($_POST["add"])){
    
    $data = $_POST["id"];
    if(sizeof($_SESSION['cart']) >0){
        
    }else{
        //no cart created before
        $_SESSION['cart']=array();
    }
        array_push($_SESSION['cart'], $data);
        printProduct($_SESSION['item']);

        
}else{//nothing in particular was requested, just a clear index.php request, which prints out all products.
    printAllProducts();

}
?>
    </body>
</html>



<?php
//PHP FUNCTIONS

//prints out the details of a specific product. The form is a button for adding the product to a cart.
function printProduct($product){
    
?>
Name : <?php echo $product["name"] ?><br/>
Category : <?php echo $product["category"] ?><br/>
Price : <?php echo $product["price"] ?><br/>

<form method="POST" action="index.php">
    <input type="hidden" name="id" value="<?php echo $product["id"] ?>">
    <input type="submit" name="add"  value="Add to cart">
</form>

<?php
}

//prints out the details only, to be used on the shopping cart page, where I didn't want the add button.
function printProductSummary($product){
    
?>
Name : <?php echo $product["name"] ?><br/>
Category : <?php echo $product["category"] ?><br/>
Price : <?php echo $product["price"] ?><br/>

<?php
}

//takes a url and pulls out objects that you can easily read.
function refineData($url){
    
//client url in order to use data in code
$data = curl_init($url);
curl_setopt($data,CURLOPT_RETURNTRANSFER,1);
$json = curl_exec($data); 
$refined = json_decode($json, true);
return $refined;
}

//prints out all products, showing the name and a link to the full details.
function printAllProducts(){
    
$allItemsUrl ="http://localhost:8080/Store/webresources/rest.product";
$items = refineData($allItemsUrl);
    
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

//prints the contents of the shopping cart
function printCart(){
    if(sizeof($_SESSION['cart']) >0){
        echo "Number of products in the cart = ".sizeof($_SESSION['cart'])."<br><br> ";
        $sum = 0;
        
        
        while (list ($key, $val) = each ($_SESSION['cart'])) {
        
            $cartProductUrl ="http://localhost:8080/Store/webresources/rest.product/" . $val;
            $cartProduct = refineData($cartProductUrl);
            //keep adding up the prices to get the sum of all products.
            $sum = $sum + $cartProduct["price"];
            
            printProductSummary($cartProduct);
        ?>
<a href=<?php echo "http://localhost/StorePhp/index.php?action=get_cart&delete=" . $key ?>>delete item</a>
        <br><br>
            <?php
        }
        ?>
        <p>Total price: <?php echo $sum ?></p><br>
        <a href="http://localhost/StorePhp/index.php?action=get_cart&order">make order</a><?php
    
    }else{
        echo "Number of products in the cart = 0";
    }
}
