<?php 
/*
 * Plugin Name:       MB Product Sync
 * Description:       This plugin synchronizes all products from a database
 * Version:           0.0.1
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            CanSoft
 * Author URI:        https://cansoft.com/
 */

require_once( plugin_dir_path( __FILE__ ) . '/all-functions/functions.php');
require_once( plugin_dir_path( __FILE__ ) . '/api/fetch-all-menual-products-data-from-icitem-table.php');
require_once( plugin_dir_path( __FILE__ ) . '/api/fetch-all-menual-products-data-from-icpricp-table.php');
require_once( plugin_dir_path( __FILE__ ) . '/api/fetch-all-menual-products-data-from-iciloc-table.php');
require_once( plugin_dir_path( __FILE__ ) . '/api/fetch-all-menual-products-data-from-j3-mijoshop-product-table.php');



// Enqueue all assets
function mb_product_sync_assets(){
    wp_enqueue_script('mbps-script', plugin_dir_url( __FILE__ ) . '/assets/js/script.js', null, time(), true);
}
add_action( 'admin_enqueue_scripts', 'mb_product_sync_assets' );


/**
 * Add menu page for this plugin
 */
function mb_product_sync_menu(){
    add_submenu_page(
        'mb_syncs',
        'Menual Product Sync',
        'Menual Product Sync',
        'manage_options',
        'menual-product-sync',
        'mb_menual_products_sync'
    );
}
add_action( 'admin_menu', 'mb_product_sync_menu', 999 );


function mb_menual_products_sync(){
    ?>
    <style>
        .wrap .d-flex {
            display: flex;
            align-items: center;
            justify-content: space-evenly;
        }
    </style>
        <div class="wrap">
            <h1>This Page for Sincronize all product in manual way</h1><br>
            <div class="d-flex">
            	<form method="GET">
                    <input type="hidden" name="product-item-page" value="1">
                    <input type="hidden" name="post_type" value="product">
                    <input type="hidden" name="page" value="menual-product-sync">
                    <?php
                        submit_button('All ICITEM Product Sync', 'primary', 'mb-product-icitem-sync');
                    ?>
                </form>

                <form method="GET">
                    <input type="hidden" name="product-icpricp-page" value="1">
                    <input type="hidden" name="post_type" value="product">
                    <input type="hidden" name="page" value="menual-product-sync">
                    <?php 
                        submit_button('All ICPRICP Product Sync', 'primary', 'mb-product-icpricp-sync'); 
                    ?>
                </form>

                <form method="GET">
                    <input type="hidden" name="product-iciloc-page" value="1">
                    <input type="hidden" name="post_type" value="product">
                    <input type="hidden" name="page" value="menual-product-sync">
                    <?php 
                        submit_button('All ICILOC Product Sync', 'primary', 'mb-product-iciloc-sync'); 
                    ?>
                </form>

                <form method="GET">
                    <input type="hidden" name="j3-mijoshop-product" value="1">
                    <input type="hidden" name="post_type" value="product">
                    <input type="hidden" name="page" value="menual-product-sync">
                    <?php 
                        submit_button('All Mijoshop Product Sync', 'primary', 'j3-mijoshop-product-sync'); 
                    ?>
                </form>
            </div>
         
            <?php 

                /**
                 * After clicing product sync button
                 * 
                 * For Main product making
                 */
                if(isset($_GET['product-item-page'])){

                    $page = $_GET['product-item-page'] ?? 1;

                    $allProducts = fetch_all_menual_products_data_from_icitem_table($page);
            
                    $chunkarray = array_chunk($allProducts, 25);

                    foreach ($chunkarray as $all_products) {
                    
                        foreach($all_products as $product){
                            /**
                             * Check Product not exit 
                             * 
                             * if product already exit than it will be not created as a product
                             */

                            //var_dump(mb_menual_product_exit($product['ITEMNO']));

                            if(mb_menual_product_exit($product['ITEMNO'])){
				                $post_id = get_menual_product_id_by_itemno_meta_value($product["ITEMNO"]);

				                $inactive = update_post_meta( $post_id, 'inactive', $product['INACTIVE']);

				                if (is_wp_error($inactive)) {
				                	$error_string = $inactive->get_error_message();
				                	$error_from = $product["ITEMNO"];
				                	$log_message = $error_from ."Get This Error" . $error_string;

				                	file_put_contents(plugin_dir_path(__FILE__) . 'debug.log', $log_message, FILE_APPEND);
				                }

				                $category = get_cat_by_meta_value_and_key_menual($product['CATEGORY']);
				                //set category
				                $category_result = wp_set_object_terms( $post_id, $category, 'product_cat');

				                if (is_wp_error($category_result)) {
				                	$error_string = $category_result->get_error_message();
				                	$error_from = $product["ITEMNO"];
				                	$log_message = $error_from ."Get This Error" . $error_string;

				                	file_put_contents(plugin_dir_path(__FILE__) . 'debug.log', $log_message, FILE_APPEND);
				                }
				                //set all meta value

				                $itemLength2 = substr($product['ITEMNO'], 6, 2);
				                $itemLength4 = substr($product['ITEMNO'], 6, 4);
				                $itemLength5 = substr($product['ITEMNO'], 6, 5);
				                $itemLength11 = substr($product['ITEMNO'],0, 11);
				            
				                if ($itemLength2 == "ZZ" || $itemLength2 == "ZI" || $itemLength4 == "MISC" ||$itemLength5 == "INTRO" || $itemLength11 == "RTIBEDLHS03" ) {
				                    $discountable = update_post_meta( $post_id, 'discountable', "No" );

				                    if (is_wp_error($discountable)) {
					                	$error_string = $discountable->get_error_message();
					                	$error_from = $product["ITEMNO"];
					                	$log_message = $error_from ."Get This Error" . $error_string;

					                	file_put_contents(plugin_dir_path(__FILE__) . 'debug.log', $log_message, FILE_APPEND);
					                }

				                }else{
				                    $nondiscountable = update_post_meta( $post_id, 'discountable', "Yes" );

				                    if (is_wp_error($nondiscountable)) {
					                	$error_string = $nondiscountable->get_error_message();
					                	$error_from = $product["ITEMNO"];
					                	$log_message = $error_from ."Get This Error" . $error_string;

					                	file_put_contents(plugin_dir_path(__FILE__) . 'debug.log', $log_message, FILE_APPEND);
					                }
				                }

				                $sku_result = update_post_meta( $post_id, '_sku', $product['FMTITEMNO'] );

				                if (is_wp_error($sku_result)) {
				                	$error_string = $sku_result->get_error_message();
				                	$error_from = $product["ITEMNO"];
				                	$log_message = $error_from ."Get This Error" . $error_string;

				                	file_put_contents(plugin_dir_path(__FILE__) . 'debug.log', $log_message, FILE_APPEND);
				                }
				            }else{
				                //create product
				                $post_id = wp_insert_post( array(
				                    'post_title' => $product['DESC'],
				                    'post_status' => 'publish',
				                    'post_type' => "product",
				                ) );

				                if (is_wp_error($post_id)) {
				                	$error_string = $post_id->get_error_message();
				                	$error_from = $product["ITEMNO"];
				                	$log_message = $error_from ."Get This Error" . $error_string;

				                	file_put_contents(plugin_dir_path(__FILE__) . 'debug.log', $log_message, FILE_APPEND);
				                }

				                //wp_update_post($post_id);
				                $category = get_cat_by_meta_value_and_key_menual($product['CATEGORY']);
				                //set category
				                $category_result = wp_set_object_terms( $post_id, $category, 'product_cat');

				                if (is_wp_error($category_result)) {
				                	$error_string = $category_result->get_error_message();
				                	$error_from = $product["ITEMNO"];
				                	$log_message = $error_from ."Get This Error" . $error_string;

				                	file_put_contents(plugin_dir_path(__FILE__) . 'debug.log', $log_message, FILE_APPEND);
				                }
				                //set all meta value
				                $sku_result = update_post_meta( $post_id, '_sku', $product['FMTITEMNO'] );
				                if (is_wp_error($sku_result)) {
				                	$error_string = $sku_result->get_error_message();
				                	$error_from = $product["ITEMNO"];
				                	$log_message = $error_from ."Get This Error" . $error_string;

				                	file_put_contents(plugin_dir_path(__FILE__) . 'debug.log', $log_message, FILE_APPEND);
				                }

				                $inactive = update_post_meta( $post_id, 'inactive', $product['INACTIVE']);
				                			update_post_meta( $post_id, '_inactive', 'field_64a935f113598');

				                if (is_wp_error($inactive)) {
				                	$error_string = $inactive->get_error_message();
				                	$error_from = $product["ITEMNO"];
				                	$log_message = $error_from ."Get This Error" . $error_string;

				                	file_put_contents(plugin_dir_path(__FILE__) . 'debug.log', $log_message, FILE_APPEND);
				                }

				                $itemno = update_post_meta( $post_id, 'itemno', $product['ITEMNO'] );
				                		  update_post_meta( $post_id, '_itemno', 'field_64a935cc13597' );

				                if (is_wp_error($itemno)) {
				                	$error_string = $itemno->get_error_message();
				                	$error_from = $product["ITEMNO"];
				                	$log_message = $error_from ."Get This Error" . $error_string;

				                	file_put_contents(plugin_dir_path(__FILE__) . 'debug.log', $log_message, FILE_APPEND);
				                }

				                $itemLength2 = substr($product['ITEMNO'], 6, 2);
				                $itemLength4 = substr($product['ITEMNO'], 6, 4);
				                $itemLength5 = substr($product['ITEMNO'], 6, 5);
				                $itemLength11 = substr($product['ITEMNO'],0, 11);
				            
				                if ($itemLength2 == "ZZ" || $itemLength2 == "ZI" || $itemLength4 == "MISC" ||$itemLength5 == "INTRO" || $itemLength11 == "RTIBEDLHS03" ) {
				                    $discountable = update_post_meta( $post_id, 'discountable', "No" );
				                    				update_post_meta( $post_id, '_discountable', "field_6511a1244d2c2" );

				                    if (is_wp_error($discountable)) {
					                	$error_string = $discountable->get_error_message();
					                	$error_from = $product["ITEMNO"];
					                	$log_message = $error_from ."Get This Error" . $error_string;

					                	file_put_contents(plugin_dir_path(__FILE__) . 'debug.log', $log_message, FILE_APPEND);
					                }
				                }else{
				                    $nondiscountable = update_post_meta( $post_id, 'discountable', "Yes" );
				                    				   update_post_meta( $post_id, '_discountable', "field_6511a1244d2c2" );

				                    if (is_wp_error($nondiscountable)) {
					                	$error_string = $nondiscountable->get_error_message();
					                	$error_from = $product["ITEMNO"];
					                	$log_message = $error_from ."Get This Error" . $error_string;

					                	file_put_contents(plugin_dir_path(__FILE__) . 'debug.log', $log_message, FILE_APPEND);
					                }
				                }
				            }
				            //dd($product["ITEMNO"]);
                        }
                    }

                    if(! count($allProducts)){

                        wp_redirect( admin_url( "/edit.php?post_type=product&page=menual-product-sync" ) );
                        exit();
                    }
                }

                if(isset($_GET['product-icpricp-page'])){
                        
                    $page = $_GET['product-icpricp-page'] ?? 1;
                   
                    $start = microtime(true);
                    $product_price_and_metadata = fetch_menual_all_products_data_from_icpricp_table($page);
                    //dd($product_price_and_metadata);
                    $pagemeta = [];
                    foreach ($product_price_and_metadata as $item) {
                        $pagemeta[$item['ITEMNO']][] = $item;
                       
                    }

                    foreach($pagemeta as $itemno => $listvalue){

                        $product_id = get_menual_product_id_by_itemno_meta_value($itemno);
                        //get pvroduct with product id
                        $product = wc_get_product($product_id);

                        

                        // //get meta key by location id
                        if ($product) {
                            //update product PRICELIST as a custom meta 
                            $product->update_meta_data('pricelist', count($listvalue));
                            $product->update_meta_data('_pricelist', 'field_64a9361913599');

                                $showSalePriceList = array_filter($listvalue, function($priceListItem){
                                    $currentDate = date("Ymd");
                                    if ($priceListItem["PRICELIST"] == "SHOW" && $priceListItem['DPRICETYPE'] == 2 && $priceListItem["SALESTART"] > 0 && $priceListItem["SALEEND"] > 0 && $priceListItem["SALEEND"] >= $currentDate) {
                                        return true;
                                    }else{
                                        return false;
                                    }
                                });

                                $showRegPriceList = array_filter($listvalue, function($priceListItem){
                                    $currentDate = date("Ymd");
                                    if ($priceListItem["PRICELIST"] == "SHOW" && $priceListItem['DPRICETYPE'] == 1) {
                                        return true;
                                    }else{
                                        return false;
                                    }
                                });

                                // dd($showSalePriceList);

                                if (count($showSalePriceList)) {
                                    foreach ($showSalePriceList as $showSalePrice) {
                                        $product->update_meta_data('_price', $showSalePrice['UNITPRICE']);

                                        $product->update_meta_data('_sale_price', $showSalePrice['UNITPRICE']);
                                        $salePriceDateStart = strtotime($showSalePrice['SALESTART']);
                                        $salePriceDateEnd = strtotime($showSalePrice['SALEEND']);
                                        $product->update_meta_data('_sale_price_dates_from', $salePriceDateStart);
                                        $product->update_meta_data('_sale_price_dates_to', $salePriceDateEnd);
                                    }
                                     
                                }

                                foreach ($showRegPriceList as $showRegPrice) {
                                    $product->update_meta_data('_regular_price', $showRegPrice['UNITPRICE']);
                                    //$product->update_meta_data('_price', $showRegPrice['UNITPRICE']);
                                }

                            foreach($listvalue as $listkey => $value){
                                //dd($value);
                                $product->update_meta_data('pricelist_'.$listkey.'_price_type', $value['DPRICETYPE']);                        
                                $product->update_meta_data('_pricelist_'.$listkey.'_price_type', "field_64afd94771823");                        
                                $product->update_meta_data('pricelist_'.$listkey.'_single_pricelist', $value['PRICELIST']);                        
                                $product->update_meta_data('_pricelist_'.$listkey.'_single_pricelist', "field_64afdbaa98ffe");                        
                                $product->update_meta_data('pricelist_'.$listkey.'_regular_price', $value['UNITPRICE']);                        
                                $product->update_meta_data('_pricelist_'.$listkey.'_regular_price', "field_64b261ee2b3a9");                        
                                $product->update_meta_data('pricelist_'.$listkey.'_sale_price', $value['UNITPRICE']);                        
                                $product->update_meta_data('_pricelist_'.$listkey.'_sale_price', "field_64b262aa8d2c7");                        
                                $product->update_meta_data('pricelist_'.$listkey.'_sale_sart', $value['SALESTART']);                        
                                $product->update_meta_data('_pricelist_'.$listkey.'_sale_sart', "field_64afdc0411dc2");                        
                                $product->update_meta_data('pricelist_'.$listkey.'_sale_end', $value['SALEEND']);
                                $product->update_meta_data('_pricelist_'.$listkey.'_sale_end', "field_64afdc348da65");
                                
                            }

                            $product->save(); //and finally save price list               
                            
                        }
                    }

                    $total = microtime(true) - $start;
                    echo "<pre>";
                    echo "Total Execution time: " . $total;
                    echo "</pre>";


                    if(! count( $product_price_and_metadata )){
                        wp_redirect( admin_url( "/edit.php?post_type=product&page=menual-product-sync" ) );
                        exit();
                    }
                }

                if(isset($_GET['product-iciloc-page'])){
                        
                    $page = $_GET['product-iciloc-page'] ?? 1;
                    $start = microtime(true);
                    $all_quantity_locations = fetch_all_menual_products_data_from_iciloc_table($page);
                    
                    $arraychunk = array_chunk($all_quantity_locations, 100);
					
                    foreach ($arraychunk as $all_locations) {
                   		
                        foreach($all_locations as $_q_location){
                            
                            //get product id with custom meta value (ITEMNO)
                            $product_id = get_menual_product_id_by_itemno_meta_value($_q_location['ITEMNO']);

                            if ($product_id) {
                                //get product with product id
                                $product = wc_get_product($product_id);
                                //dd($_q_location['QTYONHAND']);
                                // //get meta key by location id
                                $quantity_location_meta_key = 'store_'.$_q_location['LOCATION'];

                                if ($product) {
                                    //update product PRICELIST as a custom meta 
                                    $product->update_meta_data($quantity_location_meta_key, $_q_location['QTYONHAND']);            
                                    $product->save(); //and finally save price list
                                    //echo "<span style='color:green; font-weight:600;'>Product Saved Successfully</span>";
                                }
                            }else{
                                //echo "<span style='color:red; font-weight:600;'>Product Not found!</span>";
                            }
                        }
                    }
                    $total = microtime(true) - $start;
                    echo "<span style='color:red;font-weight:bold'>Total Execution Time: </span>" . $total;

                    if(! count( $all_quantity_locations )){
                        wp_redirect( admin_url( "/edit.php?post_type=product&page=menual-product-sync" ) );
                        exit();
                    }
                }


                if(isset($_GET['j3-mijoshop-product'])){

                    $page = $_GET['j3-mijoshop-product'] ?? 1;

                    $allProducts = fetch_menual_all_products_data_from_j3_mijoshop_product_table($page);
            
                    $chunkarray = array_chunk($allProducts, 25);

                    foreach ($chunkarray as $all_products) {
                    
                        foreach($all_products as $product){
                            /**
                             * Check Product not exit 
                             * 
                             * if product already exit than it will be not created as a product
                             */

                            //var_dump(mb_menual_product_exit($product['ITEMNO']));

                            
                            $post_id = get_menual_product_id_by_sku_meta_value($product["model"]);
                            if ($post_id) {
                                $itemno = get_post_meta($post_id, "itemno", true);

                                $stores = fetch_all_menual_products_data_from_iciloc_table_for_mejoshop($itemno);
                                
                                //dd($existing_meta);
                                $htmlDecodedData = html_entity_decode($product['meta']['description']);
                               
                                // Get the product object
                                $wc_product = wc_get_product($post_id);
                                // Update the product title and description
                                $wc_product->set_name($product['meta']['name']);
                                $wc_product->set_description($htmlDecodedData);

                                // Save the changes
                                $result = $wc_product->save();

                                // Restore the existing meta values
                                foreach ($stores as $meta_data) {
                                    //dd($meta_data);
                                    $quantity_location_meta_key = 'store_'.$meta_data['LOCATION'];

                                    if ($wc_product) {
                                        //update product PRICELIST as a custom meta 
                                        $wc_product->update_meta_data($quantity_location_meta_key, $meta_data['QTYONHAND']);            
                                        $wc_product->save(); //and finally save price list
                                        //echo "<span style='color:green; font-weight:600;'>Product Saved Successfully</span>";
                                    }
                                }

                                //dd($result);
                                
                                $status = update_post_meta( $post_id, 'status', $product['status']);

                                // if (is_wp_error($metaData)) {
                                //     $error_string = $metaData->get_error_message();
                                //     $error_from = $product["model"];
                                //     $log_message = $error_from ."<span style='color:red'>Get This Error</span>" . $error_string . "<br>";

                                //     file_put_contents(plugin_dir_path(__FILE__) . 'error.html', $log_message, FILE_APPEND);
                                // }else{

                                //     $log_message = "<span style='color:green'>Update Meta Successfully</span> <br>";

                                //     file_put_contents(plugin_dir_path(__FILE__) . 'debug.html', $log_message, FILE_APPEND);
                                // }

                                if (is_wp_error($status)) {
                                    $error_string = $status->get_error_message();
                                    $error_from = $product["model"];
                                    $log_message = $error_from ."<span style='color:red'>Get This Error</span>" . $error_string . "<br>";

                                    file_put_contents(plugin_dir_path(__FILE__) . 'error.html', $log_message, FILE_APPEND);
                                }else{

                                    $log_message = "Update status Successfully <br>";

                                    //file_put_contents(plugin_dir_path(__FILE__) . 'debug.html', $log_message, FILE_APPEND);
                                }

                                $status2 = update_post_meta( $post_id, 'status_2', $product['status2']);

                                if (is_wp_error($status2)) {
                                    $error_string = $status2->get_error_message();
                                    $error_from = $product["model"];
                                    $log_message = $error_from ."<span style='color:red'>Get This Error</span>" . $error_string . "<br>";

                                    file_put_contents(plugin_dir_path(__FILE__) . 'error.html', $log_message, FILE_APPEND);
                                }else{

                                    $log_message = "Update status2 Successfully <br>";

                                    //file_put_contents(plugin_dir_path(__FILE__) . 'debug.html', $log_message, FILE_APPEND);
                                }
                            }else{
                                $log_message = $product["model"] . "<span style='color:blue'> - Post Id Not Found!</span> <br>";

                                    file_put_contents(plugin_dir_path(__FILE__) . 'not-found.html', $log_message, FILE_APPEND);
                            }

                            

                            //dd($product["model"]);
                        }
                    }

                    if(! count($allProducts)){

                        wp_redirect( admin_url( "/edit.php?post_type=product&page=menual-product-sync" ) );
                        exit();
                    }
                }
}