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
    $desc    = "QR code for certificate";
    $file_array  = [ 'name' => wp_basename( $url ), 'tmp_name' => download_url( $url ) ];

    // If error storing temporarily, return the error.
    if ( is_wp_error( $file_array['tmp_name'] ) ) {
        return $file_array['tmp_name'];
    }

    // Do the validation and storage stuff.
    $id = media_handle_sideload( $file_array, 0, $desc );
    var_dump($id);
    // If error storing permanently, unlink.
    if ( is_wp_error( $id ) ) {
        @unlink( $file_array['tmp_name'] );
        return $id;
    }

    set_post_thumbnail( $post_id, $id );
    }