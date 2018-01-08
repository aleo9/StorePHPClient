<!DOCTYPE html>

<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        
<?php
session_start();
include ("view/search.php");
include ("controller/helper.php");
include ("view/printer.php");

//make subcategory links
$categoryListUrl ="http://localhost:8080/Store/webresources/rest.categories";
$categories = refineData($categoryListUrl);

printCategoryLinks($categories);

//pretty long if-else statement that directs you to the correct "page" depending on actions.
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
//not in use currently, since a regular index.php call will have the same effect
}else if(isset($_GET["action"]) && $_GET["action"] == "get_all"){
    //function that prints out the name of all products, with links to the full details.
    //printAllProducts();

//move to the cart page    
}else if(isset($_GET["action"]) && $_GET["action"] == "get_cart"){
    
    //if a delete command was also given
    if(isset($_GET["delete"])){
        
        //the 2nd parameter is the id that is deleted.
        array_splice($_SESSION['cart'], $_GET["delete"], 1);
    }
    
    //if a order/buy command was also given
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
    showCart();
    //printCart();
    
  
//this gets all items under the wanted category
}else if(isset($_GET["action"]) && $_GET["action"] == "get_categories"){
    
    $categoryUrl ="http://localhost:8080/Store/webresources/rest.product/category/" . urldecode($_GET["name"]);
    $listByCategory = refineData($categoryUrl);
    
    printProductLinks($listByCategory);
    

//search based on keyword stored as $_POST["search"].
//Doesn't work with spaces, and you need to include the first character in the product name
}else if(isset($_POST["search"])){
    
    $searchUrl ="http://localhost:8080/Store/webresources/rest.product/name/" . urldecode($_POST["search"]);
    $searchResult = refineData($searchUrl);
    printProductLinks($searchResult);
    

//add item to shopping cart
}else if(isset($_POST["add"])){
    
    if(sizeof($_SESSION['cart']) ==0){
        //no cart created before
        $_SESSION['cart']=array();
    }
    
    addToCart($_POST["id"]);
        

        
}else{//nothing in particular was requested, just a clear index.php request, which prints out all products.
    $allItemsUrl ="http://localhost:8080/Store/webresources/rest.product";
    $items = refineData($allItemsUrl);
    printProductLinks($items);

}
?>
    </body>
</html>



