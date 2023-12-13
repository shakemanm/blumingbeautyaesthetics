<?php
/**
  * Gives the page details for the services
  * 
  */

defined( 'ABSPATH' ) || exit;

class WPSR_Metadata{

    public static $defaults = array();

    public static function init(){

        self::$defaults = array(
            'url' => '',
            'title' => '',
            'excerpt' => '',
            'short_url' => '',
            'comments_section' => '',
            'post_id' => '',
            'post_image' => '',
            'rss_url' => '',
            'twitter_username' => '',
            'fb_app_id' => '',
            'fb_app_secret' => ''
        );

    }

    public static function metadata_raw(){

        $page_info = self::page_info();
        $gs = WPSR_Lists::set_defaults( get_option( 'wpsr_general_settings' ), WPSR_Options::default_values( 'general_settings' ) );

        $misc_info = array(
            'rss_url' => get_bloginfo( 'rss2_url' ),
            'twitter_username' => ( empty( $gs[ 'twitter_username' ] ) ? '' : '@' . $gs[ 'twitter_username' ] ),
            'fb_app_id' => $gs[ 'facebook_app_id' ],
            'fb_app_secret' => $gs[ 'facebook_app_secret' ],
            'comments_section' => $gs[ 'comments_section' ],
            'raw_url' => $page_info[ 'url' ]
        );

        $metadata = array_merge( $page_info, $misc_info );
        $metadata = WPSR_Lists::set_defaults( $metadata, self::$defaults );

        return $metadata;
        
    }
    
    public static function metadata(){

        $metadata = self::metadata_raw();
        return apply_filters( 'wpsr_mod_metadata', $metadata );

    }

    public static function page_info(){

        global $post;

        $d = array();

        if( in_the_loop() ) {
            
            $d = self::post_info_by_id( get_the_ID() );
            
        }else{
            
            if( is_home() && get_option( 'show_on_front' ) == 'page' ){
                
                $d = self::post_info_by_id( get_option( 'page_for_posts' ) );
                
            }elseif( is_front_page() || ( is_home() && ( get_option( 'show_on_front' ) == 'posts' || !get_option( 'page_for_posts' ) ) ) ){
                
                $d = array(
                    'title' => get_bloginfo( 'name' ),
                    'url' => get_bloginfo( 'url' ),
                    'excerpt' => get_bloginfo( 'description' ),
                    'short_url' => get_bloginfo( 'url' ),
                );
                
            }elseif( is_singular() ){
                
                $d = self::post_info_by_id( $post->ID );
            
            }elseif( is_tax() || is_tag() || is_category() ){
                
                $term = get_queried_object();
                $d = array(
                    'title' => wp_title( '', false ),
                    'url' => get_term_link( $term, $term->taxonomy ),
                    'excerpt' => $term->description
                );
                
            }elseif( function_exists( 'get_post_type_archive_link' ) && is_post_type_archive() ){
                
                $post_type = get_query_var( 'post_type' );
                $post_type_obj = get_post_type_object( $post_type );
                
                $d = array(
                    'title' => wp_title( '', false ),
                    'url' => get_post_type_archive_link( $post_type ),
                    'excerpt' => $post_type_obj->description
                );
                
            }elseif( is_date() ){
                
                if( is_day() ){
                    
                    $d = array(
                        'title' => wp_title( '', false ),
                        'url' => get_day_link( get_query_var( 'year' ), get_query_var( 'monthnum' ), get_query_var( 'day' ) )
                    );
                    
                }elseif( is_month() ){
                    
                    $d = array(
                        'title' => wp_title( '', false ),
                        'url' => get_month_link( get_query_var( 'year' ), get_query_var( 'monthnum' ) )
                    );
                    
                }elseif( is_year() ){
                    
                    $d = array(
                        'title' => wp_title( '', false ),
                        'url' => get_year_link( get_query_var( 'year' ) )
                    );
                    
                }
                
            }elseif( is_author() ){
                
                $d = array(
                    'title' => wp_title( '', false ),
                    'url' => get_author_posts_url( get_query_var( 'author' ), get_query_var( 'author_name' ) )
                );
                
            }elseif( is_search() ){
                
                $d = array(
                    'title' => wp_title( '', false ),
                    'url' => get_search_link()
                );
                
            }elseif( is_404() ){
                
                $d = array(
                    'title' => wp_title( '', false ),
                    'url' => home_url( esc_url_raw( $_SERVER['REQUEST_URI'] ) )
                );
                
            }
        }
        
        $d = array_map( 'trim', $d );
        
        return $d;

    }

    public static function post_info_by_id( $id ){

        global $post;

        $d = array();

        if( $id ){
            $d = array(
                'post_id' => $id,
                'title' => get_the_title( $id ),
                'url' => get_permalink( $id ),
                'excerpt' => self::excerpt( $post->post_excerpt, 100 ), // using $post->post_excerpt instead of get_the_excerpt as the_content filter loses shortcode formatting
                'short_url' => wp_get_shortlink( $id ),
                'post_image' => self::post_image( $id )
            );
        }
        
        return $d;
        
    }
    
    public static function excerpt( $excerpt, $length = 250 ){
        
        global $post;
        
        $excerpt = ( empty( $excerpt ) ) ? strip_tags( strip_shortcodes( $post->post_content ) ) : $excerpt;
        return substr( $excerpt, 0, $length );
        
    }
    
    public static function post_image( $post_id ){
        
        $thumbnail = get_the_post_thumbnail_url( $post_id );
        
        if( $thumbnail === false ){
            return '';
        }else{
            return $thumbnail;
        }
        
    }
    
    public static function map_metadata_params( $metadata ){

        $params = array(
            'url' => $metadata[ 'url' ],
            'title' => $metadata[ 'title' ],
            'excerpt' => $metadata[ 'excerpt' ],
            'image' => $metadata[ 'post_image' ],

            'short-url' => $metadata[ 'short_url' ],
            'rss-url' => $metadata[ 'rss_url' ],
            'comments-section' => $metadata[ 'comments_section' ],
            'raw-url' => $metadata[ 'raw_url' ],
            
            'twitter-username' => $metadata[ 'twitter_username' ],
            'fb-app-id' => $metadata[ 'fb_app_id' ],
            'fb-app-secret' => $metadata[ 'fb_app_secret' ]
        );

        return apply_filters( 'wpsr_mod_metadata_params_map', $params );

    }

    public static function replace_params( $text, $metadata = array() ){

        $metadata = wp_parse_args( $metadata, self::$defaults);
        $params_raw = self::map_metadata_params( $metadata );
        $param_tags = array();
        $param_values = array();

        foreach( $params_raw as $param => $value ){
            if( in_array( $param, array( 'url', 'title', 'excerpt' ) ) ){
                $value = rawurlencode( $value );
            }
            array_push( $param_tags, '{' . $param . '}' );
            array_push( $param_values, $value );
        }

        return str_ireplace( $param_tags, $param_values, $text );

    }

}

WPSR_Metadata::init();

?>