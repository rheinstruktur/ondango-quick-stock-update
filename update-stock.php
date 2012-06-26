<?php
/**
* PHP Quick-Stock-Update Tool for Ondango Shop
* written by Daniel Speckhardt
*
* http://github.com/rheinstruktur/
* http://rheinstruktur.de
*
* GNU General Public License
*/

require_once "libs/Ondango.php";
$api_key = "YOUR API KEY";
$api_secret = "YOUR API SECRET"; // optional
$shop_id = "YOUR SHOP ID";

$ondango = new Ondango($api_key, $api_secret);

if (isset($_POST['btnSubmit'])) {

    $product_id = $_POST['tbProductId'];

    $Result = $ondango->GET("products", array("product_id" => $product_id));
    $Product = $Result->data[0];
    $Variations = $Product->ProductVariation;
    $VariationCount = count($Variations);

    if ($VariationCount > 0) {

        for ($i = 0; $i < $VariationCount; $i++) {
            //get Variation_id
            $variation_id = $Variations[$i]->product_variation_id;
            $V = $i + 1;
            //Update Variation
            $valVariationStock = $_POST['tbStockV' . $V];
            $reslutVariationUpdate = $ondango->PUT("product/variation", array("product_variation_id" => $variation_id,
                "stock" => $valVariationStock
                    ));
        }
    } else {
        $valVariationStock = $_POST['tbStockV1'];
        $ondango->PUT("product", array("product_id" => $product_id, "stock" => $valVariationStock));
    }
}

$resultsAllProducts = $ondango->GET("products/all", array("shop_id" => $shop_id));

$product_data = $resultsAllProducts->data;
?>

<html>
    <head>
        <link rel="stylesheet" href="styles.css" type="text/css" media="all" />
        <script type="text/javascript">
            
            function updateProduct(productid){
            
                var maxVariationCount=document.getElementById("tbMaxVariationCount").value;
            
                var ColumsCountArray = document.getElementById('row'+productid).cells;
                var ColumsCount = ColumsCountArray.length;
                var fixColums = 3;
                var varColumns = (ColumsCount - 3)/2;
                var VarCount = (ColumsCount - fixColums) - varColumns;
            
            
                //Clean all hiddenBoxes
                for (var v = 1; v <= maxVariationCount; v++) { 
                    document.getElementById("tbStockV"+v).value = ""; 
                }
            
                for (var v = 1; v <= VarCount; v++) { 
                    document.getElementById("tbStockV"+v).value = 
                        document.getElementById("V"+v+"Stock-"+productid).value;
                }
            
                document.getElementById("tbProductId").value = productid;
                document.getElementById("tbProductVariationCount").value = VarCount;   
                document.getElementById("btnSubmit").click();  
            }
        </script>
    </head>
    <body>
        <h1>Ondango Shop - Quick Stock Update Tool</h1>
        <form name="formUpdate" method="POST" action="update-stock.php">

            <table id="tblProducts">

                <?php
                $maxVariationCount = 0;

                $rowsAsHTML = "";

                for ($i = 0; $i < count($product_data); $i++) {

                    $product = $product_data[$i]->Product;
                    $product->product_id;
                    $rowsAsHTML .= "<tr id='row" . $product->product_id . "'>" . "\n";
                    $rowsAsHTML .= "<td><a href='javascript:void(0)' onClick='updateProduct(" . $product->product_id . ");'><img src='update.png' alt='' /></a></td>" . "\n";
                    $rowsAsHTML .= "<td class='sku'>" . $product->sku . "</td>" . "\n";    //Erweiterung von GGG
                    $rowsAsHTML .= "<td>" . $product->title . "</td>" . "\n";
                    $variation = $product_data[$i]->ProductVariation;
                    $VariationCount = count($variation);

                    for ($j = 0; $j < $VariationCount; $j++) {

                        $varObj = $variation[$j];
                        $V = $j + 1;
                        $rowsAsHTML .= "<td class='vardesc'>" . $varObj->name . "</td>" . "\n";
                        $rowsAsHTML .= "<td class='varinput'><input type='text' id='V" . $V . "Stock-" . $product->product_id . "' value='" . $varObj->stock . "'/></td>" . "\n";
                    }
                    if ($VariationCount == 0) {
                        $rowsAsHTML .= "<td class='vardesc'>ALL:</td>" . "\n";
                        $rowsAsHTML .= "<td class='varinput'><input type='text' id='V1Stock-" . $product->product_id . "' value='" . $product->stock . "'/></td>" . "\n";
                    }
                    if ($VariationCount > $maxVariationCount) {
                        $maxVariationCount = $VariationCount;
                    }
                    $rowsAsHTML .= "</tr>" . "\n";
                }

                //Table Header
                echo "<tr>";
                echo "<th>Update</th>";
                echo "<th>SKU</th>";
                echo "<th>Name</th>";
                for ($i = 1; $i <= $maxVariationCount; $i++) {
                    echo "<th colspan='2'>Variation-" . $i . "</th>";
                }
                echo "</tr>";

                echo $rowsAsHTML;
                ?>

            </table> 

<?php
for ($i = 1; $i < $maxVariationCount + 1; $i++) {
    echo "<input id='tbStockV" . $i . "' name='tbStockV" . $i . "' type='hidden'/>" . "\n";
}
echo "<input id='tbProductId' name='tbProductId' type='hidden'/>" . "\n";
echo "<input id='tbProductVariationCount' name='tbProductVariationCount' type='hidden'/>" . "\n";
echo "<input id='tbMaxVariationCount' name='tbMaxVariationCount' type='hidden' value='" . $maxVariationCount . "' />" . "\n";
echo "<input id='btnSubmit' name='btnSubmit' type='submit' style='display:none' />" . "\n";
?>    

        </form>
    </body>
</html>



