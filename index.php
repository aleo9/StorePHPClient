<!DOCTYPE html>

<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <form>
        
        
    </form>
        <ul id="products">
        </ul>


<?php

function refineData($url){
    
//client url in order to use data in code
$data = curl_init($url);
curl_setopt($data,CURLOPT_RETURNTRANSFER,1);
$json = curl_exec($data); 
$refined = json_decode($json, true);
return $refined;
}

    function printAllItems(){
        $allItemsUrl ="http://localhost:8080/Store/webresources/rest.product";
$items = refineData($allItemsUrl);
    
if(!empty($items))
{ 
    foreach ($items as $i) {?>
    <a href=<?php echo "http://localhost/StorePhp/index.php?action=get_product&id=" . $i["id"] ?>><?php echo $i["name"] ?></a><br />
   
   <?php

}
    }else{
    echo "No data found"; 
    }
}
   
//make subcategory links
$categoryListUrl ="http://localhost:8080/Store/webresources/rest.categories";
$categories = refineData($categoryListUrl);

?>

    
    <form action="index.php" method="post">
    <input type='text' name="search">
    <button type="submit">Search</button>
    </form>
    

    
<?php
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
</br></br>
<?php


if(isset($_GET["action"]) && $_GET["action"] == "get_product"){
    
    
$productUrl ="http://localhost:8080/Store/webresources/rest.product/" . $_GET["id"];
$product = refineData($productUrl);
?>

Name : <?php echo $product["name"] ?><br/>
Category : <?php echo $product["category"] ?><br/>
Price : <?php echo $product["price"] ?><br/>

<?php
}else if(isset($_GET["action"]) && $_GET["action"] == "get_all"){
    printAllItems();
    
}else if(isset($_GET["action"]) && $_GET["action"] == "get_categories"){
    
    
    $categoryUrl ="http://localhost:8080/Store/webresources/rest.product/category/" . urldecode($_GET["name"]);
    $listByCategory = refineData($categoryUrl);
    
if(!empty($listByCategory))
{ 
    foreach ($listByCategory as $i) {?>
    <a href=<?php echo "http://localhost/StorePhp/index.php?action=get_product&id=" . $i["id"] ?>><?php echo $i["name"] ?></a><br />
   
   <?php
    }
}


}

else if(isset($_POST["search"])){
    
    $searchUrl ="http://localhost:8080/Store/webresources/rest.product/name/" . urldecode($_POST["search"]);
    $searchResult = refineData($searchUrl);
    
    if(!empty($searchResult))
{ 
    foreach ($searchResult as $s) {?>
    <a href=<?php echo "http://localhost/StorePhp/index.php?action=get_product&id=" . $s["id"] ?>><?php echo $s["name"] ?></a><br />
   
   <?php
    }
}

 

}else{
    printAllItems();

 

}
?>



    </body>
</html>


