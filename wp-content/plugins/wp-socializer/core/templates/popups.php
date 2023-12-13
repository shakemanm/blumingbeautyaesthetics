<?php
/**
 * Share menu
 *
 **/

defined( 'ABSPATH' ) || exit;

class WPSR_Template_Popups{

    public static $share_menu_usage = 0;

    public static $short_link_usage = 0;

    public static function init(){

        add_action( 'wp_footer', array( __CLASS__, 'output' ) );

    }

    public static function output(){

        $gs = WPSR_Lists::set_defaults( get_option( 'wpsr_general_settings' ), WPSR_Options::default_values( 'general_settings' ) );
        $share_menu_shown = false;

        if( $gs[ 'share_menu' ] == 'yes' ){
            if( self::$share_menu_usage > 0 ){
                self::share_menu();
                $share_menu_shown = true;
            }
        }

        if( self::$short_link_usage > 0 || $share_menu_shown ){
            self::short_link();
        }

    }

    public static function header( $for, $heading ){
        
        echo '<div class="wpsr-pp-head">
        <h3>' . esc_html( $heading ) . '</h3>
        <a href="#" class="wpsr-pp-close" data-id="' . esc_attr( $for ) . '" title="' . esc_attr__( 'Close', 'wp-socializer' ) . '"><i class="fas fa-times"></i></a>
        </div>';

    }

    public static function share_menu(){

        echo '<div id="wpsr-share-menu" class="wpsr-bg wpsr-pp-closed">';
        echo '<div class="wpsr-pp-inner">';
        echo '<div class="wpsr-popup">';
            self::header( 'wpsr-share-menu', __( 'Share', 'wp-socializer' ) );

            // Build share menu links wrap
            $sm_links_wrap = new WPSR_HTML_Tag( 'div', 'wpsr-sm-links' );

            echo '<div class="wpsr-pp-content">';
            echo $sm_links_wrap->open();

            $social_icons = WPSR_Lists::social_icons();
            $exclude_icons = apply_filters( 'wpsr_mod_popup_exclude_icons', array( 'addtofavorites', 'comments', 'html' ) );

            foreach( $social_icons as $id => $props ){

                if( !in_array( 'for_share', $props[ 'features' ] ) ){
                    continue;
                }

                if( in_array( $id, $exclude_icons ) ){
                    continue;
                }

                $classes = array( 'wpsr-sm-link', 'wpsr-sml-' . $id );
                $classes = implode( ' ', $classes );

                $link = base64_encode( $props[ 'link' ] );
                $data_attr = ' data-d="' . esc_attr( $link ) . '"';

                if( array_key_exists( 'link_mobile', $props ) ){
                    $mobile_link = base64_encode( $props[ 'link_mobile' ] );
                    $data_attr .= ' data-m="' . esc_attr( $mobile_link ) . '"';
                }

                $title = esc_attr( $props[ 'title' ] );

                $icon = '<span class="wpsr-sm-icon"><i class="' . esc_attr( $props[ 'icon' ] ) . '"></i></span>';
                $text = '<span class="wpsr-sm-text">' . esc_html( $props[ 'name' ] ) . '</span>';

                echo '<div><a href="#" rel="nofollow" title="' . esc_attr( $title ) . '" class="' . esc_attr( $classes ) . '" ' . wp_kses( $data_attr, array() ) . ' style="background-color: ' . esc_attr( $props[ 'colors' ][ 0 ] ) . '">' . $icon . $text . '</a></div>';
            }
            echo $sm_links_wrap->close();

            echo '</div>'; // Content

            echo '</div>';
        echo '</div>';
        echo '</div>';

    }

    public static function short_link(){

        echo '<div id="wpsr-short-link" class="wpsr-bg wpsr-pp-closed">';
        echo '<div class="wpsr-pp-inner">';
        echo '<div class="wpsr-popup">';
            self::header( 'wpsr-short-link', __( 'Copy short link', 'wp-socializer' ) );
            echo '<div class="wpsr-pp-content">';
            $copy_text = __( 'Copy link', 'wp-socializer' );
            $copied_text = __( 'Copied !', 'wp-socializer' );
            echo '<input type="text" id="wpsr-short-link-url" readonly /><a href="#" id="wpsr-sl-copy-btn" data-c="' . esc_attr( $copy_text ) . '" data-d="' . esc_attr( $copied_text ) . '">' . esc_html( $copy_text ) . '</a>';
            echo '</div>';
            echo '</div>';
        echo '</div>';
        echo '</div>';

    }

}

WPSR_Template_Popups::init();

?>