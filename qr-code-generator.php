<?php

    include('phpqrcode/qrlib.php');
    require_once(ABSPATH . 'wp-admin/includes/media.php');
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    //include('config.php');

    function make_qr($cert_name, $post_id) {
    // how to save PNG codes to server
    $post_url = get_permalink($post_id);
    $tempDir = plugin_dir_path( __FILE__ ).'qrcodes/';
    
    $codeContents = $post_url;
    
    // we need to generate filename somehow, 
    // with md5 or with database ID used to obtains $codeContents...
    $fileName = 'qr_code'.$cert_name.'.png';
    
    $pngAbsoluteFilePath = $tempDir.$fileName;    
    // generating
    QRcode::png($codeContents, $pngAbsoluteFilePath);

    $url     = get_home_url().'/wp-content/plugins/mythicuk-product-importer/qrcodes/'.$fileName;
    $post_id = $post_id;
    $desc    = "image description";

    $image = media_sideload_image( $url, $post_id, $desc,'id' );

    set_post_thumbnail( $post_id, $image );
    }