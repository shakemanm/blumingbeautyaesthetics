<?php
/**
 * Follow icons template
 *
 **/

defined( 'ABSPATH' ) || exit;

class WPSR_Template_Follow_Icons{
    
    public static function init(){

        add_action( 'wp_footer', array( __CLASS__, 'output' ) );

    }
    
    public static function output(){

        if( is_admin() ){
            return;
        }

        global $post;
        $post_settings = WPSR_Lists::post_settings( $post );
        if( $post_settings[ 'wpsr_disable_follow_icons' ] == 'yes' ){
            return;
        }

        $fb_settings = WPSR_Lists::set_defaults( get_option( 'wpsr_followbar_settings' ), WPSR_Options::default_values( 'follow_icons' ) );
        $loc_rules_answer = WPSR_Location_Rules::check_rule( $fb_settings[ 'loc_rules' ] );
        
        if( $fb_settings[ 'ft_status' ] != 'disable' && $loc_rules_answer ){
            echo self::html( $fb_settings );
            do_action( 'wpsr_do_followbar_print_template_end' );
        }
        
    }
    
    public static function html( $opts, $floating = True ){
        
        $opts = WPSR_Lists::set_defaults( $opts, WPSR_Options::default_values( 'follow_icons' ) );
        $template = $opts[ 'template' ];
        $btns = WPSR_Lists::parse_template( $template );
        $sb_sites = WPSR_Lists::social_icons();
        $html = '';
        
        if( !is_array( $btns ) || empty( $btns ) ){
            return '';
        }
        
        $icon_styles = array();
        if ( $opts[ 'bg_color' ] != '' ){
            $icon_styles[ 'background-color' ] = $opts[ 'bg_color' ];
            $icon_styles[ 'border-color' ] = $opts[ 'bg_color' ];
        }

        if ( $opts[ 'icon_color' ] != '' ){
            $icon_styles[ 'color' ] = $opts[ 'icon_color' ];
        }

        foreach( $btns as $btn_obj ){

            $btn_obj = apply_filters( 'wpsr_mod_followbar_button', $btn_obj );
            $id = key( $btn_obj );

            $icon_wrap_tag = new WPSR_HTML_Tag( 'span', 'sr-' . $id );
            $icon_tag = new WPSR_HTML_Tag( 'a', '', $icon_styles );

            if( !array_key_exists( $id, $sb_sites ) ){
                continue;
            }

            $prop = $sb_sites[ $id ];

            $icon = '';
            if( $btn_obj[ $id ][ 'icon' ] == '' ){
                $icon = '<i class="' . esc_attr( $prop[ 'icon' ] ) . '"></i>';
            }else{
                $settings_icon = $btn_obj[ $id ][ 'icon' ];
                if (strpos( $settings_icon, 'http' ) === 0) {
                    $icon = '<img src="' . esc_attr( $settings_icon ) . '" alt="' . esc_attr( $prop[ 'name' ] ) . '" />';
                }else{
                    $icon = '<i class="' . esc_attr( $settings_icon ) . '"></i>';
                }
            }
            
            $icon_tag->attrs = array(
                'rel' => 'nofollow',
                'href' => $btn_obj[ $id ][ 'url' ],
                'target' => '_blank',
                'title' => ( ( $btn_obj[ $id ][ 'text' ] == '' ) ? $prop[ 'name' ] : urldecode( $btn_obj[ $id ][ 'text' ] ) )
            );

            if( array_key_exists( 'onclick', $prop ) ){
                $icon_tag->add_attr( 'onclick', $prop[ 'onclick' ] );
            }
            
            $icon_tag->add_data( 'id', $id );

            $html .= $icon_wrap_tag->open();
            $html .= $icon_tag->open() . $icon . $icon_tag->close();
            $html .= $icon_wrap_tag->close();
            
        }
        
        // Socializer tag
        $scr_tag = new WPSR_HTML_Tag( 'div', 'socializer sr-followbar sr-' . $opts[ 'size' ] );
        
        if( $opts[ 'shape' ] != '' ) $scr_tag->add_class( 'sr-' . $opts[ 'shape' ] );
        if( $opts[ 'hover' ] != '' ) $scr_tag->add_class( 'sr-' . $opts[ 'hover' ] );
        if( $opts[ 'pad' ] != '' ) $scr_tag->add_class( 'sr-' . $opts[ 'pad' ] );
        if( $opts[ 'orientation' ] == 'vertical' ) $scr_tag->add_class( 'sr-vertical' );
        if( $opts[ 'open_popup' ] == '' ) $scr_tag->add_class( 'sr-popup' );
        if( !$floating ) $scr_tag->add_class( 'sr-multiline' );
        
        $html = $scr_tag->open() . $html . $scr_tag->close();
        
        if( $floating ){
            $title = ( $opts[ 'title' ] != '' ) ? '<div class="sr-fb-title">' . esc_html( $opts[ 'title' ] ) . '</div>' : '';
            $open_icon = WPSR_Lists::public_icons( 'fb_open' );
            $close_icon = WPSR_Lists::public_icons( 'fb_close' );
            $close_btn = '<div class="wpsr-fb-close wpsr-close-btn" title="Open or close follow icons"><span class="wpsr-bar-icon">' . $open_icon . $close_icon . '</span></div>';
            $orientation = ( $opts[ 'orientation' ] == 'horizontal' ) ? 'sr-fb-hl' : 'sr-fb-vl';

            // Wrap tag
            $wrap_tag = new WPSR_HTML_Tag( 'div', 'wp-socializer wpsr-follow-icons sr-fb-' . $opts[ 'position' ] );
            $wrap_tag->add_class( $orientation );
            $wrap_tag->data = array(
                'lg-action' => $opts[ 'lg_screen_action' ],
                'sm-action' => $opts[ 'sm_screen_action' ],
                'sm-width' => $opts[ 'sm_screen_width' ],
            );

            $html = $wrap_tag->open() . $title . $html . $close_btn . $wrap_tag->close();
        }
        
        if( !$floating && isset( $opts[ 'profile_text' ] ) && trim( $opts[ 'profile_text' ] ) != '' ){
            $html = '<p>' . wp_kses_post( $opts[ 'profile_text' ] ) . '</p>' . $html;
        }
        
        return $html;
        
    }
    
}

WPSR_Template_Follow_Icons::init();

?>