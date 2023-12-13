<?php
/**
 * Floating sharebar template
 *
 **/

defined( 'ABSPATH' ) || exit;

class WPSR_Template_Floating_Sharebar{

    public static function init(){

        add_action( 'wp_footer', array( __CLASS__, 'output' ) );

    }

    public static function output(){

        if( is_admin() ){
            return;
        }

        global $post;
        $post_settings = WPSR_Lists::post_settings( $post );
        if( $post_settings[ 'wpsr_disable_floating_sharebar' ] == 'yes' ){
            return;
        }

        $fsb_settings = WPSR_Lists::set_defaults( get_option( 'wpsr_floating_sharebar_settings' ), WPSR_Options::default_values( 'floating_sharebar' ) );
        $loc_rules_answer = WPSR_Location_Rules::check_rule( $fsb_settings[ 'loc_rules' ] );
        
        if( $fsb_settings[ 'ft_status' ] != 'disable' && $loc_rules_answer ){
            wp_reset_query();
            echo self::html( $fsb_settings );
            do_action( 'wpsr_do_floating_sharebar_print_template_end' );
        }

    }

    public static function html( $o ){

        $social_icons = WPSR_Lists::social_icons();
        $page_info = WPSR_Metadata::metadata();

        $counter_services = WPSR_Share_Counter::counter_services();
        $selected_icons = WPSR_Lists::parse_template( $o[ 'selected_icons' ] );

        $gs = WPSR_Lists::set_defaults( get_option( 'wpsr_general_settings' ), WPSR_Options::default_values( 'general_settings' ) );

        $scr_tag = new WPSR_HTML_Tag( 'div', 'socializer sr-popup sr-vertical' );
        $icons_html = array();
        $counters_selected = array();

        // Icon default styles
        $icon_styles = array();
        if ( $o[ 'icon_bg_color' ] != '' ){
            $icon_styles[ 'background-color' ] = $o[ 'icon_bg_color' ];
            if( $o[ 'icon_shape' ] == 'ribbon' ){
                $icon_styles[ 'border-color' ] = $o[ 'icon_bg_color' ];
            }
        }

        if ( $o[ 'icon_color' ] != '' ){
            $icon_styles[ 'color' ] = $o[ 'icon_color' ];
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

            $url = WPSR_Metadata::replace_params( $props[ 'link' ], $page_info );

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
            if( ( $o[ 'share_counter' ] == 'individual' || $o[ 'share_counter' ] == 'total-individual' ) && array_key_exists( $id, $counter_services) ){
                $count_holder = WPSR_Share_Counter::placeholder( $page_info, $id, 'ctext' );
                $count_tag = $count_holder;
                $scr_tag->add_class( 'sr-' . $o[ 'sc_style' ] );
                if( $o[ 'sc_style' ] != 'count-1' ){
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
            $ihtml .= $icon_tag->open() . $icon . $count_tag . $icon_tag->close();
            $ihtml .= $icon_wrap_tag->close();

            array_push( $icons_html, $ihtml );

        }

        if( intval( $o[ 'more_icons' ] ) > 0 ){
            $more_count = intval( $o[ 'more_icons' ] );
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
        if( $o[ 'icon_size' ] != '' ){
            $scr_tag->add_class( 'sr-' . $o[ 'icon_size' ] );
        }

        if( $o[ 'icon_shape' ] != '' ){
            $scr_tag->add_class( 'sr-' . $o[ 'icon_shape' ] );
            $scr_tag->add_class( 'sr-pad' );
        }

        if( $o[ 'hover_effect' ] != '' ){
            $scr_tag->add_class( 'sr-' . $o[ 'hover_effect' ] );
        }

        if( $o[ 'padding' ] != '' && $o[ 'icon_shape' ] == '' ){
            $scr_tag->add_class( 'sr-' . $o[ 'padding' ] );
        }

        $all_icons_html = $scr_tag->open() . implode( "\n", $icons_html ) . $scr_tag->close();
        $row_html = $all_icons_html;
        $html = '';

        if( $o[ 'share_counter' ] == 'total' || $o[ 'share_counter' ] == 'total-individual' ){
            $total_counter_html = WPSR_Share_Counter::total_count_html( array(
                'text' => 'Shares',
                'counter_color' => $o['sc_total_color'],
                'add_services' => $counters_selected,
                'size' => $o[ 'icon_size' ]
            ), $page_info);

            if( $o[ 'sc_total_position' ] == 'top' ){
                $row_html = $total_counter_html;
                $row_html .= $all_icons_html;
            }else{
                $row_html = $all_icons_html;
                $row_html .= $total_counter_html;
            }

        }

        // Wrap classes
        $wrap_tag = new WPSR_HTML_Tag( 'div', 'wp-socializer wpsr-sharebar wpsr-sb-vl wpsr-hide' );
        $wrap_tag->add_class( 'wpsr-sb-vl-' . $o[ 'sb_position' ] );
        $wrap_tag->add_class( 'wpsr-sb-vl-' . $o[ 'movement' ] );

        if( $o[ 'style' ] == 'enclosed' ){
            $wrap_tag->add_class( 'wpsr-sb-simple' );
        }

        if( $o[ 'sm_simple' ] == 'yes' ){
            $wrap_tag->add_class( 'wpsr-sb-sm-simple' );
        }

        // Wrap styles
        $pos_style = ( $o[ 'sb_position' ] == 'wleft' ) ? 'left' : ( ( $o[ 'sb_position' ] == 'scontent' ) ? 'margin-left' : 'right' );
        $wrap_tag->add_style( $pos_style, $o[ 'offset' ] );

        if( $o['style'] == 'enclosed' ){
            $wrap_tag->add_style( 'background-color', $o[ 'sb_bg_color' ] );
        }

        // Wrap data attributes
        $wrap_tag->data = array(
            'stick-to' => $o[ 'stick_element' ],
            'lg-action' => $o[ 'lg_screen_action' ],
            'sm-action' => $o[ 'sm_screen_action' ],
            'sm-width' => $o[ 'sm_screen_width' ],
        );

        $open_icon = WPSR_Lists::public_icons( 'sb_open' );
        $close_icon = WPSR_Lists::public_icons( 'sb_close' );

        $html .= $wrap_tag->open();
        $html .= '<div class="wpsr-sb-inner">';
        $html .= $row_html;
        $html .= '</div>';
        $html .= '<div class="wpsr-sb-close wpsr-close-btn" title="Open or close sharebar"><span class="wpsr-bar-icon">' . $open_icon . $close_icon . '</span></div>';
        $html .= $wrap_tag->close();

        return $html;

    }


}

WPSR_Template_Floating_Sharebar::init();

?>