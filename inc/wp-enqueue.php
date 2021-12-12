<?php

/**
 * Required Styles and Scripts
 */
function corona_test_verify_wp_equeue() {
  
    // CSS
    $stylesheet_uri = CORONA_TEST_VERIFY_PLUGIN_PLUGIN_PATH . 'css/styles.css';
    $stylesheet = CORONA_TEST_VERIFY_PLUGIN_PLUGIN_DIR . '/css/styles.css';

    wp_register_style( 
      'corona-test-verify-style', 
      $stylesheet_uri,
      array(), 
      filemtime( $stylesheet ) 
    );
    wp_enqueue_style( 'corona-test-verify-style' ); 

    // FA
    $stylesheet_fa_uri = CORONA_TEST_VERIFY_PLUGIN_PLUGIN_PATH . 'css/fa/css/all.css';
    $stylesheet_fa = CORONA_TEST_VERIFY_PLUGIN_PLUGIN_DIR . '/css/fa/css/all.css';
    wp_register_style( 
      'corona-test-verify-fa-style', 
      $stylesheet_fa_uri,
      array(), 
      filemtime( $stylesheet_fa ) 
    );
    wp_enqueue_style( 'corona-test-verify-fa-style' );
}
add_action( 'wp_enqueue_scripts', 'corona_test_verify_wp_equeue' );


/**
 * Required Admin Styles and Scripts
 */
function corona_test_verify_admin_equeue() {

    // JS
    $javascript_uri = CORONA_TEST_VERIFY_PLUGIN_PLUGIN_PATH . 'admin/js/corona-admin.js';
    $javascript = CORONA_TEST_VERIFY_PLUGIN_PLUGIN_DIR . '/admin/js/corona-admin.js';

    wp_register_script( 
      'corona_test_verify-script-admin', 
      $javascript_uri,
      array( 'jquery' ), 
      filemtime( $javascript ) 
    );
    wp_enqueue_script( 'corona_test_verify-script-admin' );

    // CSS
    $stylesheet_uri = CORONA_TEST_VERIFY_PLUGIN_PLUGIN_PATH . 'admin/css/admin-styles.css';
    $stylesheet = CORONA_TEST_VERIFY_PLUGIN_PLUGIN_DIR . '/admin/css/admin-styles.css';

    wp_register_style( 
      'corona_test_verify-style-admin', 
      $stylesheet_uri,
      array(), 
      filemtime( $stylesheet ) 
    );
    wp_enqueue_style( 'corona_test_verify-style-admin' ); 
}
add_action( 'admin_enqueue_scripts', 'corona_test_verify_admin_equeue', 999999 );