<?php

add_action( 'wp', 'onload' );
function onload()
{
    $one = 0;
    $two = 0;
    $three = 0;
    $four = 0;
    $five = 0;
    $six = 0;
    $seven = 0;
    $eight = 0;
    $nine = 0;
    $ten = 0;
    if ('product' === get_post_type() && is_singular()) {
        $sku_values = get_post_meta( get_the_ID(), '_sku' );
        //var_dump($sku_values[0]);
        $posts = get_posts(array(
            'numberposts'   => -1,
            'post_type'     => 'us_portfolio',
            'meta_key'      => 'sku',
            'meta_value'    => $sku_values[0]
        ));
        foreach($posts as $post){
            //var_dump($post);
            $id = $post->ID;
            $grade = get_post_meta( $id, "card_grading");
            $grade = $grade[0];
            if($grade == "Ungraded"){

            }
            if($grade == "1"){
                $one = $one + 1;
            }
            if($grade == "2"){
                $two = $two + 1;
            }
            if($grade == "3"){
                $three = $three + 1;
            }
            if($grade == "4"){
                $four = $four + 1;
            }
            if($grade == "5"){
                $five = $five + 1;
            }
            if($grade == "6"){
                $six = $six + 1;
            }
            if($grade == "7"){
                $seven = $seven + 1;
            }
            if($grade == "8"){
                $eight = $eight + 1;
            }
            if($grade == "9"){
                $nine = $nine + 1;
            }
            if($grade == "10"){
                $ten = $ten + 1;
            }
        }

        update_field('card_totals-1', $one, get_the_ID());
        update_field('card_totals-2', $two, get_the_ID());
        update_field('card_totals-3', $three, get_the_ID());
        update_field('card_totals-4', $four, get_the_ID());
        update_field('card_totals-5', $five, get_the_ID());
        update_field('card_totals-6', $six, get_the_ID());
        update_field('card_totals-7', $seven, get_the_ID());
        update_field('card_totals-8', $eight, get_the_ID());
        update_field('card_totals-9', $nine, get_the_ID());
        update_field('card_totals-10', $ten, get_the_ID());


    }
    
}