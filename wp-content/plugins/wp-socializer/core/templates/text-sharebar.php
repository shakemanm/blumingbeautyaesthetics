<?php
/**
 * Text sharebar template
 *
 **/

defined( 'ABSPATH' ) || exit;

class WPSR_Template_Text_Sharebar{
    
    public static function init(){

        add_action( 'wp_footer', array( __CLASS__, 'output' ) );

    }

    public static function output(){

        if( is_admin() ){
            return;
        }

        global $post;
        $post_settings = WPSR_Lists::post_settings( $post );
        if( $post_settings[ 'wpsr_disable_text_sharebar' ] == 'yes' ){
            return;
        }

        $tsb_settings = WPSR_Lists::set_defaults( get_option( 'wpsr_text_sharebar_settings' ), WPSR_Options::default_values( 'text_sharebar' ) );
        $loc_rules_answer = WPSR_Location_Rules::check_rule( $tsb_settings[ 'loc_rules' ] );
        
        if( $tsb_settings[ 'ft_status' ] != 'disable' && $loc_rules_answer && !wp_is_mobile() ){
            echo self::html( $tsb_settings );
            do_action( 'wpsr_do_text_sharebar_print_template_end' );
        }
        
    }
    
    public static function html( $opts ){
        
        $opts = WPSR_Lists::set_defaults( $opts, WPSR_Options::default_values( 'text_sharebar' ) );
        $template = $opts[ 'template' ];
        $btns = WPSR_Lists::parse_template( $template );
        $sb_sites = WPSR_Lists::social_icons();
        $page_info = WPSR_Metadata::metadata();
        $html = '';
        
        if( !is_array( $btns ) || empty( $btns ) ){
            return '';
        }
        
        foreach( $btns as $btn ){

            if( !array_key_exists( $btn, $sb_sites ) ){
                continue;
            }

            if( $btn == 'comments' && !comments_open( $page_info[ 'post_id' ] ) ){
                continue;
            }

            $sb_info = $sb_sites[ $btn ];
            $link = array_key_exists( 'link_tsb', $sb_info ) ? $sb_info[ 'link_tsb' ] : $sb_info[ 'link' ];

            $icon = '';
            if( strpos( $sb_info[ 'icon' ], 'http' ) === 0 ){
                $icon = '<img src="' . esc_url( $sb_info[ 'icon' ] ) . '" alt="' . esc_attr( $sb_info[ 'name' ] ) . '"/>';
            }else{
                $icon = '<i class="' . esc_attr( $sb_info[ 'icon' ] ) . '"></i>';
            }

            $icon_tag = new WPSR_HTML_Tag( 'a' );
            $icon_tag->attrs = array(
                'href' => '#',
                'target' => '_blank',
                'title' => $sb_info[ 'title' ],
            );

            if( isset( $sb_info[ 'onclick' ] ) ){
                $icon_tag->add_attr( 'onclick', $sb_info[ 'onclick' ] );
            }

            $icon_tag->data = array(
                'link' => $link,
                'id' => $btn
            );

            $icon_tag->add_style( 'color', $opts[ 'icon_color' ] );

            $html .= '<li>' . $icon_tag->open() . $icon . $icon_tag->close() . '</li>';
        }

        // Wrap tag
        $wrap_tag = new WPSR_HTML_Tag( 'ul', 'wpsr-text-sb wpsr-tsb-' . $opts[ 'size' ] . ' wpsr-clearfix' );

        $wrap_tag->data = array(
            'content' => $opts[ 'content' ],
            'tcount' => $opts[ 'text_count' ],
            'url' => $page_info[ 'url' ],
            'title' => $page_info[ 'title' ],
            'surl' => $page_info[ 'short_url' ],
            'tuname' => $page_info[ 'twitter_username' ],
            'comments-section' => $page_info[ 'comments_section' ]
        );

        $wrap_tag->add_style( 'background-color', $opts[ 'bg_color' ] );

        $wrap_tag = apply_filters( 'wpsr_mod_data_attributes', $wrap_tag, 'text_sharebar', $page_info );

        $html = $wrap_tag->open() . $html . $wrap_tag->close();
        
        return $html;
        
    }
    
}

WPSR_Template_Text_Sharebar::init();

?>