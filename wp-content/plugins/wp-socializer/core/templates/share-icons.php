<?php
/**
 * Share icons template
 *
 **/

defined( 'ABSPATH' ) || exit;

class WPSR_Template_Share_Icons{

    public static function init(){

        add_action( 'init', array( __CLASS__, 'output' ) );

    }

    public static function output(){

        if( is_admin() ){
            return;
        }

        $si_settings = WPSR_Lists::set_defaults( get_option( 'wpsr_social_icons_settings' ), array(
            'ft_status' => 'disable',
            'tmpl' => array()
        ));
        $si_templates = $si_settings[ 'tmpl' ];
        
        if( empty( $si_templates ) ){
            $default_tmpl = WPSR_Options::default_values( 'share_icons' );
            array_push( $si_templates, $default_tmpl );
        }

        if($si_settings[ 'ft_status' ] != 'disable'){
            foreach( $si_templates as $tmpl ){
                
                $content_obj = new wpsr_template_button_handler( $tmpl, 'content' );
                $excerpt_obj = new wpsr_template_button_handler( $tmpl, 'excerpt' );
                
                add_filter( 'the_content', array( $content_obj, 'print_template' ), 10 );
                add_filter( 'the_excerpt', array( $excerpt_obj, 'print_template' ), 10 );
                
            }
        }

    }

    public static function html( $tmpl, $default_page_info = array() ){

        global $post;
        $post_settings = WPSR_Lists::post_settings( $post );
        if( $post_settings[ 'wpsr_disable_share_icons' ] == 'yes' ){
            return array(
                'html' => ''
            );
        }

        $social_icons = WPSR_Lists::social_icons();
        $page_info = WPSR_Metadata::metadata();
        $page_info = WPSR_Lists::set_defaults( $default_page_info, $page_info );

        $counter_services = WPSR_Share_Counter::counter_services();
        $selected_icons = WPSR_Lists::parse_template( $tmpl[ 'selected_icons' ] );

        if( empty( $selected_icons ) ){
            return array(
                'html' => ''
            );
        }

        $gs = WPSR_Lists::set_defaults( get_option( 'wpsr_general_settings' ), WPSR_Options::default_values( 'general_settings' ) );

        $scr_tag = new WPSR_HTML_Tag( 'div', 'socializer sr-popup' );
        $icons_html = array();
        $counters_selected = array();

        // Icon default styles
        $icon_styles = array();
        if ( $tmpl[ 'icon_bg_color' ] != '' ){
            $icon_styles[ 'background-color' ] = $tmpl[ 'icon_bg_color' ];
            if( $tmpl[ 'icon_shape' ] == 'ribbon' ){
                $icon_styles[ 'border-color' ] = $tmpl[ 'icon_bg_color' ];
            }
        }

        if ( $tmpl[ 'icon_color' ] != '' ){
            $icon_styles[ 'color' ] = $tmpl[ 'icon_color' ];
        }

        $icon_styles_attr = 'style="' . esc_attr( WPSR_HTML_Tag::build_style( $icon_styles ) ) . '"';

        foreach( $selected_icons as $icon ){

            $id = key( $icon );

            if( !array_key_exists( $id, $social_icons ) ){
                continue;
            }

            $props = $social_icons[ $id ];

            $icon_wrap_tag = new WPSR_HTML_Tag( 'span', 'sr-' . $id );
            $icon_tag = new WPSR_HTML_Tag( 'a', '', $icon_styles );

            $settings = WPSR_Lists::set_defaults( $icon[ $id ], array(
                'icon' => '',
                'text' => '',
                'hover_text' => ''
            ));

            // If custom HTML button
            if( $id == 'html' ){
                $custom_html = WPSR_Metadata::replace_params( $settings[ 'html' ], $page_info );
                $ihtml = '<div class="sr-custom-html">' . do_shortcode( $custom_html ) . '</div>';
                array_push( $icons_html, $ihtml );
                continue;
            }

            // If comments button
            if( $id == 'comments' && !comments_open( $page_info[ 'post_id' ] ) ){
                continue;
            }

            $icon_link = $props[ 'link' ];
            $url = WPSR_Metadata::replace_params( $icon_link, $page_info );
            
            $text = '';
            if( $settings[ 'text' ] != '' ){
                $text = '<span class="text">' . esc_html( $settings[ 'text' ] ) . '</span>';
                $icon_wrap_tag->add_class( 'sr-text-in' );
            }

            $icon = '';
            if( $settings[ 'icon' ] == '' ){
                $icon = '<i class="' . esc_attr( $props[ 'icon' ] ) . '"></i>';
            }else{
                $icon_val = $settings[ 'icon' ];
                if (strpos( $settings[ 'icon' ], 'http' ) === 0) {
                    $icon = '<img src="' . esc_attr( $icon_val ) . '" alt="' . esc_attr( $props[ 'name' ] ) . '" />';
                }else{
                    $icon = '<i class="' . esc_attr( $icon_val ) . '"></i>';
                }
            }

            $title = '';
            if( $settings[ 'hover_text' ] == '' ){
                $title = $props[ 'title' ];
            }else{
                $title = $settings[ 'hover_text' ];
            }

            $count_tag = '';
            if( ( $tmpl[ 'share_counter' ] == 'individual' || $tmpl[ 'share_counter' ] == 'total-individual' ) && array_key_exists( $id, $counter_services) ){
                $count_holder = WPSR_Share_Counter::placeholder( $page_info, $id, 'ctext' );
                $count_tag = $count_holder;
                $scr_tag->add_class( 'sr-' . $tmpl[ 'sc_style' ] );
                if( $tmpl[ 'sc_style' ] != 'count-1' ){
                    $icon_wrap_tag->add_class( 'sr-text-in' );
                }
            }
            array_push( $counters_selected, $id );

            if( $id == 'pinterest' ){
                $icon_tag->add_data( 'pin-custom', 'true' );
            }

            if( $id == 'shortlink' ){
                WPSR_Template_Popups::$short_link_usage += 1;
            }

            if( array_key_exists( 'link_mobile', $props ) ){
                $mobile_link = WPSR_Metadata::replace_params( $props[ 'link_mobile' ], $page_info );
                $icon_tag->add_data( 'mobile', $mobile_link );
            }

            $icon_tag->add_data( 'id', $id );

            $icon_tag->attrs = array(
                'rel' => 'nofollow',
                'href' => $url,
                'target' => '_blank',
                'title' => $title,
            );

            if( isset( $props[ 'onclick' ] ) ){
                $icon_tag->add_attr( 'onclick', $props[ 'onclick' ] );
            }

            $ihtml = $icon_wrap_tag->open();
            $ihtml .= $icon_tag->open() . $icon . $text . $count_tag . $icon_tag->close();
            $ihtml .= $icon_wrap_tag->close();

            array_push( $icons_html, $ihtml );

        }

        if( intval( $tmpl[ 'more_icons' ] ) > 0 ){
            $more_count = intval( $tmpl[ 'more_icons' ] );
            $more_icons = array_slice( $icons_html, -$more_count, $more_count );
            $more_html = '<span class="sr-more"><a href="#" target="_blank" title="More sites" ' . $icon_styles_attr . '><i class="fa fa-share-alt"></i></a><ul class="socializer">' . implode( "\n", $more_icons ) . '</ul></span>';
            $icons_html = array_slice( $icons_html, 0, -$more_count );
            array_push( $icons_html, $more_html );
        }

        if( $gs[ 'share_menu' ] == 'yes' ){
            $menu_metadata = json_encode( WPSR_Metadata::map_metadata_params( $page_info ) );
            array_push( $icons_html, '<span class="sr-share-menu"><a href="#" target="_blank" title="More share links" ' . $icon_styles_attr . ' data-metadata="' . esc_attr( $menu_metadata ) . '"><i class="fa fa-plus"></i></a></span>' );
            WPSR_Template_Popups::$share_menu_usage += 1;
        }

        // Building the socializer tag
        if( $tmpl[ 'layout' ] != '' ){
            $scr_tag->add_class( 'sr-' . $tmpl[ 'layout' ] );
        }

        if( $tmpl[ 'icon_size' ] != '' ){
            $scr_tag->add_class( 'sr-' . $tmpl[ 'icon_size' ] );
        }

        if( $tmpl[ 'icon_shape' ] != '' && $tmpl[ 'layout' ] == '' ){
            $scr_tag->add_class( 'sr-' . $tmpl[ 'icon_shape' ] );
        }

        if( $tmpl[ 'hover_effect' ] != '' ){
            $scr_tag->add_class( 'sr-' . $tmpl[ 'hover_effect' ] );
        }

        if( $tmpl[ 'padding' ] != '' ){
            $scr_tag->add_class( 'sr-' . $tmpl[ 'padding' ] );
        }

        $all_icons_html = $scr_tag->open() . implode( "\n", $icons_html ) . $scr_tag->close();
        $row_html = $all_icons_html;
        $html = '';

        if( $tmpl[ 'share_counter' ] == 'total' || $tmpl[ 'share_counter' ] == 'total-individual' ){
            $total_counter_html = WPSR_Share_Counter::total_count_html( array(
                'text' => 'Shares',
                'counter_color' => '#000',
                'add_services' => $counters_selected,
                'size' => $tmpl[ 'icon_size' ]
            ), $page_info);

            if( $tmpl[ 'sc_total_position' ] == 'left' ){
                $row_html = $total_counter_html;
                $row_html .= $all_icons_html;
            }else{
                $row_html = $all_icons_html;
                $row_html .= $total_counter_html;
            }

        }

        // Wrap tag
        $wrap_tag = new WPSR_HTML_Tag( 'div', 'wp-socializer wpsr-share-icons' );

        if( $tmpl[ 'layout' ] == '' && $tmpl[ 'center_icons' ] == 'yes' ){
            $wrap_tag->add_class( 'wpsr-flex-center' );
        }

        $wrap_tag->data = array(
            'lg-action' => $tmpl[ 'lg_screen_action' ],
            'sm-action' => $tmpl[ 'sm_screen_action' ],
            'sm-width' => $tmpl[ 'sm_screen_width' ],
        );

        $wrap_tag = apply_filters( 'wpsr_mod_html_tag', $wrap_tag, 'share_icons', $page_info );

        if( trim( $tmpl[ 'custom_html_above' ] ) != '' ){
            $html .= $tmpl[ 'custom_html_above' ];
        }

        $html .= $wrap_tag->open();
        if( trim( $tmpl[ 'heading' ] ) != '' ) $html .= wp_kses_post( $tmpl[ 'heading' ] );
        $html .= '<div class="wpsr-si-inner">' . $row_html . '</div>';
        $html .= $wrap_tag->close();

        if( trim( $tmpl[ 'custom_html_below' ] ) != '' ){
            $html .= $tmpl[ 'custom_html_below' ];
        }

        return array(
            'html' => $html
        );

    }

}

WPSR_Template_Share_Icons::init();

class wpsr_template_button_handler{
    
    private $props;
    private $type;
    
    function __construct( $properties, $type ){

        $this->type = $type;
        $this->props = WPSR_Lists::set_defaults( $properties, WPSR_Options::default_values( 'share_icons' ) );

    }
    
    function print_template( $content ){
        
        $call_from_excerpt = 0;
        $call_stack = debug_backtrace();
        
        foreach( $call_stack as $call ){
            if( $call['function'] == 'the_excerpt' || $call['function'] == 'get_the_excerpt' ){
                $call_from_excerpt = 1;
            }
        }
        
        $loc_rules_answer = WPSR_Location_Rules::check_rule( $this->props[ 'loc_rules' ] );
        $rule_in_excerpt = ( $this->props[ 'in_excerpt' ] == 'show' );
        $output = $content;
        
        if( $loc_rules_answer ){
            
            if( ( $this->type == 'content' && $call_from_excerpt != 1 ) || ( $this->type == 'excerpt' && $rule_in_excerpt == 1 ) ){
                
                $gen_out = WPSR_Template_Share_Icons::html( $this->props );
                
                if( !empty( $gen_out[ 'html' ] ) ){
                
                    $final_template = $gen_out[ 'html' ];
                    
                    if( $this->props[ 'position' ] == 'above_below_posts' )
                        $output = $final_template . $content . $final_template;
                    
                    if( $this->props[ 'position' ] == 'above_posts' )
                        $output = $final_template . $content;
                    
                    if( $this->props[ 'position' ] == 'below_posts' )
                        $output = $content . $final_template;
                    
                }
            }
            
            do_action( 'wpsr_do_buttons_print_template_end' );
            
        }
        
        return $output;
        
    }
    
}

?>