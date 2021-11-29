<?php

require_once __DIR__ . '/../corona-plugin.php';
class CV_UPDATER
{
    public function update( $transient){

        if ( empty( $transient->checked ) ) {
            return $transient;
          }
        
          $remote = wp_remote_get( 
            'https://plugin.wp.osowsky-webdesign.de/info.json',
            array(
              'timeout' => 10,
              'headers' => array(
                'Accept' => 'application/json'
              )
            )
          );
        
          if(is_wp_error( $remote )|| 200 !== wp_remote_retrieve_response_code( $remote ) || empty( wp_remote_retrieve_body( $remote ))) {
            return $transient;	
          }
          
          $remote = json_decode( wp_remote_retrieve_body( $remote ) );
         
            // your installed plugin version should be on the line below! You can obtain it dynamically of course 
          if($remote && version_compare( '1.3.3', $remote->version, '<' ) && version_compare( $remote->requires, get_bloginfo( 'version' ), '<' ) && version_compare( $remote->requires_php, PHP_VERSION, '<' )) {
            
            $res = new stdClass();
            $res->slug = $remote->slug;
            $res->plugin = plugin_basename( __FILE__ ); // it could be just YOUR_PLUGIN_SLUG.php if your plugin doesn't have its own directory
            $res->new_version = $remote->version;
            $res->tested = $remote->tested;
            $res->package = $remote->download_url;
            $transient->response[ $res->plugin ] = $res;
            
            //$transient->checked[$res->plugin] = $remote->version;
          }
         
          return $transient;
    }  
} 