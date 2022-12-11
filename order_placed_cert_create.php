<?php
add_action('woocommerce_thankyou', 'generate_cert', 10, 1);
function generate_cert( $order_id ) {
    if ( ! $order_id )
        return;

    // Allow code execution only once 
    if( ! get_post_meta( $order_id, '_thankyou_action_done', true ) ) {

        // Get an instance of the WC_Order object
        $order = wc_get_order( $order_id );

        // Get the order key
        $order_key = $order->get_order_key();

        // Get the order number
        $order_key = $order->get_order_number();

        if($order->is_paid()){
            $paid = __('yes');
        

        // Loop through order items
        foreach ( $order->get_items() as $item_id => $item ) {

            // Get the product object
            $product = $item->get_product();

            // Get the product Id
            $product_id = $product->get_id();

            // Get the product name
            $name = $product->get_name();
            $sku = $product->get_sku();
            $number = get_post_meta( $product->get_id(), 'number', true );
            $set_name = get_post_meta( $product->get_id(), 'set_name', true );
            $year = get_post_meta( $product->get_id(), 'year', true );
            $rarity = get_post_meta( $product->get_id(), 'rarity_variant', true );
            $args = array(
                'post_type' =>'us_portfolio',
                'posts_per_page' => 1,
                'orderby'=>'post_date',
                'order' => 'DESC',
            );
            
            $image_posts = get_posts($args);
            foreach ( $image_posts  as $post ) {
                $last_post_name = $post->post_name;
                $last_post_name = explode(" - ", $last_post_name);
                $last_post_name = $last_post_name[0];
                $last_post_name = intval($last_post_name);
                $new_post_name = $last_post_name + 1;
                $new_post_name = str_pad($new_post_name, 4, '0', STR_PAD_LEFT);
            }
            $post_title = $new_post_name." - ".$name;

             wp_reset_query(); 
             
             $post_arr = array(
                'post_type' => 'us_portfolio',
                 'post_title'   => $post_title,
                 'post_content' => '',
                 'post_status'  => 'publish',
                 'post_author'  => 2,
             );
             $post_id = wp_insert_post( $post_arr );
             update_field('card_name', $name, $post_id);
             update_field('rarity_variant', $rarity, $post_id);
             update_field('set_name', $set_name, $post_id);
             update_field('number', $number, $post_id);
             update_field('year', $year, $post_id);
             update_field('sku', $sku, $post_id);
             update_field('card_grading', "Ungraded", $post_id);

             make_qr($post_title, $post_id);
        }

        // Flag the action as done (to avoid repetitions on reload for example)
        $order->update_meta_data( '_thankyou_action_done', true );
        $order->save();

        
    }
}
}