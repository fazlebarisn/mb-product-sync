<?php 
/**
 * Check product has exit or not
 * 
 * we want to check it with product no meta key (ITEMNO)
 */

function mb_menual_product_exit($itemno_meta_value){
 
    // Set the custom meta tag key and value to search for
    $meta_key = 'itemno';
    $meta_value = $itemno_meta_value;

    // Prepare the arguments for the product query
    $args = array(
        'post_type'      => 'product',
        'post_status'    => 'any',
        'meta_query'     => array(
            array(
                'key'     => $meta_key,
                'value'   => $meta_value,
                'compare' => '='
            )
        ),
        'posts_per_page' => 1 // Limit the query to 1 result
    );

    // Run the product query
    $products = new WP_Query( $args );

    // Check if the product(s) with the custom meta tag exist
    if ( $products->have_posts() ) {
        return true;
    }
    return false;
}


/**
 * To get product category by category meta value
 */
function get_cat_by_meta_value_and_key_menual($meta_value){
    // Define the meta query arguments
    $meta_query_args = array(
        array(
            'key'     => 'segval', 
            'value'   => $meta_value, 
                'compare' => '=', // Adjust the comparison operator as needed (e.g., '=', '>', '<', 'LIKE')
        ),
    );

    // Get terms with the specified meta query
    $terms = get_terms( array(
        'taxonomy' => 'product_cat', // Replace 'your_taxonomy' with the actual taxonomy name
        'hide_empty' => false, // Include terms with no associated posts
        'meta_query' => $meta_query_args,
    ) );

    if(count($terms) == 1 ){
        // Loop through terms
        foreach ( $terms as $term ) {
            // echo 'Term ID: ' . $term->term_id . '<br>';
            return $term->name;
        }
    }
}


function get_menual_product_id_by_itemno_meta_value($itemno_value){
    if($itemno_value == ''){
        return;
    }
    $args = array(
        'post_type' => 'product',
        'meta_key' => 'itemno',
        //'meta_key' => '_sku',
        'meta_value' => $itemno_value,
        'meta_compare' => '=',
        'posts_per_page' => 1,
    );
    
    $query = new WP_Query( $args );
    
    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $post_id = get_the_ID();
            // Do something with the post ID
            return $post_id;
        }
    } 
    // Restore the original post data
    wp_reset_postdata();
}

function get_menual_product_id_by_sku_meta_value($sku_value){
    if($sku_value == ''){
        return;
    }
    $args = array(
        'post_type' => 'product',
        'meta_key' => '_sku',
        //'meta_key' => '_sku',
        'meta_value' => $sku_value,
        'meta_compare' => '=',
        'posts_per_page' => 1,
    );
    
    $query = new WP_Query( $args );
    
    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $post_id = get_the_ID();
            // Do something with the post ID
            return $post_id;
        }
    } 
    // Restore the original post data
    wp_reset_postdata();
}