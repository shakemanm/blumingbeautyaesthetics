<?php
/**
  * Options of all the features
  * 
  */

defined( 'ABSPATH' ) || exit;

class WPSR_Options{

    public static function common_options( $field ){

        $fields = array(
            'icon_size' => array(
                '32px' => array( '32px', 'size.svg', '32px' ),
                '40px' => array( '40px', 'size.svg', '40px' ),
                '48px' => array( '48px', 'size.svg', '48px' ),
                '64px' => array( '64px', 'size.svg', '64px' )
            ),
            'icon_shape' => array(
                '' => array( 'Square', 'shape-square.svg', '32px' ),
                'circle' => array( 'Circle', 'shape-circle.svg', '32px' ),
                'squircle' => array( 'Squircle', 'shape-squircle.svg', '32px' ),
                'squircle-2' => array( 'Squircle 2', 'shape-squircle-2.svg', '32px' ),
                'drop' => array( 'Drop', 'shape-drop.svg', '32px' ),
                'diamond' => array( 'Diamond*', 'shape-diamond.svg', '32px' ),
                'ribbon' => array( 'Ribbon*', 'shape-ribbon.svg', '32px' )
            ),
            'hover_effect' => array(
                '' => __( 'None', 'wp-socializer' ),
                'opacity' => 'Fade',
                'rotate' => 'Rotate',
                'zoom' => 'Zoom',
                'shrink' => 'Shrink',
                'float' => 'Float',
                'sink' => 'Sink',
                'fade-dark' => 'Fade dark'
            ),
            'share_counter' => array(
                '' => 'No share count',
                'individual' => 'Individual count',
                'total' => 'Total count only',
                'total-individual' => 'Both individual and total counts',
            ),
            'sc_style' => array(
                'count-1' => array( 'Style 1', 'counter-1.svg', '60px' ),
                'count-2' => array( 'Style 2', 'counter-2.svg', '70px' ),
                'count-3' => array( 'Style 3', 'counter-3.svg', '70px' ),
            ),
            'more_icons' => array(
                '0' => 'No grouping',
                '1' => '1',
                '2' => '2',
                '3' => '3',
                '4' => '4',
                '5' => '5',
                '6' => '6',
                '7' => '7',
                '8' => '8',
                '9' => '9',
                '10' => '10',
            )
        );

        return $fields[ $field ];

    }

    public static function filter_options( $feature, $prop ){

        $fields = call_user_func( array( __class__, $feature ) );
        $output = array();

        foreach( $fields as $key => $val ){
            $filter_val = array_key_exists( $prop, $val ) ? $val[ $prop ] : '';
            $output[ $key ] = $filter_val;
        }

        return $output;

    }

    public static function default_values( $feature ){
        return self::filter_options( $feature, 'default' );
    }

    public static function options( $feature ){
        return self::filter_options( $feature, 'options' );
    }

    public static function share_icons(){

        return array(
            'selected_icons' => array(
                'default' => '[{"facebook":{"hover_text":"","text":"","icon":""}},{"twitter":{"icon":"","text":"","hover_text":""}},{"linkedin":{"icon":"","text":"","hover_text":""}},{"pinterest":{"icon":"","text":"","hover_text":""}},{"print":{"icon":"","text":"","hover_text":""}},{"pdf":{"icon":"","text":"","hover_text":""}}]',
                'options' => false,
                'description' => __( 'The social media icons selected for sharing', 'wp-socializer' )
            ),
            'layout' => array(
                'default' => '',
                'options' => array(
                    '' => array( 'Normal', 'layout-horizontal.svg', '64px' ),
                    'fluid' => array( 'Full width', 'layout-fluid.svg', '64px' ),
                ),
                'description' => __( 'The layout of the social icons. It decides whether the icons should be of normal width or full width. Select fluid for full width.', 'wp-socializer' )
            ),
            'icon_size' => array(
                'default' => '32px',
                'options' => self::common_options( 'icon_size' ),
                'description' => __( 'The size of the icons.', 'wp-socializer' )
            ),
            'icon_shape' => array(
                'default' => 'circle',
                'options' => self::common_options( 'icon_shape' ),
                'description' => __( 'The shape of the icons.', 'wp-socializer' )
            ),
            'hover_effect' => array(
                'default' => 'opacity',
                'options' => self::common_options( 'hover_effect' ),
                'description' => __( 'The behavior of the icons when mouse is hovered over them.', 'wp-socializer' )
            ),
            'icon_color' => array(
                'default' => '#ffffff',
                'options' => false,
                'description' => __( 'The color of the icons.', 'wp-socializer' )
            ),
            'icon_bg_color' => array(
                'default' => '',
                'options' => false,
                'description' => __( 'The background color of the icons. Leave empty to take the social media site\'s own color.', 'wp-socializer' )
            ),
            'padding' => array(
                'default' => 'pad',
                'options' => array(
                    '' => 'No',
                    'pad' => 'Yes'
                ),
                'description' => __( 'Decides whether to add a space between the icons.', 'wp-socializer' )
            ),
            'share_counter' => array(
                'default' => 'total-individual',
                'options' => self::common_options( 'share_counter' ),
                'description' => __( 'The type of share counters to display in the share icons bar.', 'wp-socializer' )
            ),
            'sc_style' => array(
                'default' => 'count-1',
                'options' => self::common_options( 'sc_style' ),
                'description' => __( 'The design style of the share count numbers and how they are displayed.', 'wp-socializer' )
            ),
            'sc_total_position' => array(
                'default' => 'left',
                'options' => array(
                    'left' => 'Left to the icons',
                    'right' => 'Right to the icons'
                ),
                'description' => __( 'The position of the total count. This is effective only when share_counter includes total count.', 'wp-socializer' )
            ),
            'more_icons' => array(
                'default' => '0',
                'options' => self::common_options( 'more_icons' ),
                'description' => __( 'The number of icons from the last to group into a single icon.', 'wp-socializer' )
            ),
            'center_icons' => array(
                'default' => '',
                'options' => array(
                    '' => 'No',
                    'yes' => 'Yes'
                ),
                'description' => __( 'Centers the icon in the content.', 'wp-socializer' )
            ),
            'heading' => array(
                'default' => '<h3>Share and Enjoy !</h3>',
                'options' => false,
                'description' => __( 'The heading to display above the icons. HTML is allowed.', 'wp-socializer' )
            ),
            'custom_html_above' => array(
                'default' => '',
                'options' => false
            ),
            'custom_html_below' => array(
                'default' => '',
                'options' => false
            ),

            'sm_screen_width' => array(
                'default' => '768',
                'options' => false,
                'description' => __( 'The screen width below which the icons will act in mobile/small screen mode. In pixels.', 'wp-socializer' )
            ),
            'lg_screen_action' => array(
                'default' => 'show',
                'options' => array(
                    'show' => __( 'Show', 'wp-socializer' ),
                    'hide' => __( 'Hide', 'wp-socializer' )
                ),
                'description' => __( 'The behavior of the icons in desktop/large screens.', 'wp-socializer' )
            ),
            'sm_screen_action' => array(
                'default' => 'show',
                'options' => array(
                    'show' => __( 'Show', 'wp-socializer' ),
                    'hide' => __( 'Hide', 'wp-socializer' )
                ),
                'description' => __( 'The behavior of the icons in mobile/small screens.', 'wp-socializer' )
            ),
            
            'loc_rules' => array(
                'default' => array(
                    'type' => 'show_all',
                    'rule' => 'W10='
                ),
                'options' => false
            ),

            'position' => array(
                'default' => 'below_posts',
                'options' => array(
                    'above_posts' => __( 'Above posts', 'wp-socializer' ),
                    'below_posts' => __( 'Below posts', 'wp-socializer' ),
                    'above_below_posts' => __( 'Both above and below posts', 'wp-socializer' )
                ),
                'description' => __( 'The position of the social icons in a post.', 'wp-socializer' )
            ),
            'in_excerpt' => array(
                'default' => 'hide',
                'options' => array(
                    'show' => __( 'Show in excerpt', 'wp-socializer' ),
                    'hide' => __( 'Hide in excerpt', 'wp-socializer' )
                ),
                'description' => __( 'Decides whether to show the icons in the excerpts.', 'wp-socializer' )
            )
        );

    }

    public static function floating_sharebar(){

        return array(
            'ft_status' => array(
                'default' => 'disable',
                'options' => array(
                    'enable' => __( 'Enable floating sharebar', 'wp-socializer' ),
                    'disable' => __( 'Disable floating sharebar', 'wp-socializer' )
                )
            ),
            'selected_icons' => array(
                'default' => '[{"facebook":{"hover_text":"","icon":""}},{"twitter":{"hover_text":"","icon":""}},{"linkedin":{"hover_text":"","icon":""}},{"email":{"hover_text":"","icon":""}},{"pdf":{"hover_text":"","icon":""}},{"whatsapp":{"hover_text":"","icon":""}}]',
                'options' => false
            ),
            'icon_size' => array(
                'default' => '40px',
                'options' => self::common_options( 'icon_size' )
            ),
            'icon_shape' => array(
                'default' => '',
                'options' => self::common_options( 'icon_shape' )
            ),
            'hover_effect' => array(
                'default' => 'opacity',
                'options' => self::common_options( 'hover_effect' )
            ),
            'icon_color' => array(
                'default' => '#ffffff',
                'options' => false
            ),
            'icon_bg_color' => array(
                'default' => '',
                'options' => false
            ),
            'padding' => array(
                'default' => '',
                'options' => array(
                    '' => 'No',
                    'pad' => 'Yes'
                )
            ),
            'style' => array(
                'default' => '',
                'options' => array(
                    '' => array( 'Simple', 'layout-vertical.svg', '64px' ),
                    'enclosed' => array( 'Enclosed', 'fsb-enclosed.svg', '64px' ),
                )
            ),
            'sb_bg_color' => array(
                'default' => '#ffffff',
                'options' => false
            ),
            'sb_position' => array(
                'default' => 'wleft',
                'options' => array(
                    'wleft' => 'Left of the page',
                    'wright' => 'Right of the page',
                    'scontent' => 'Stick to the content'
                )
            ),
            'stick_element' => array(
                'default' => '.entry',
                'options' => false
            ),
            'offset' => array(
                'default' => '10px',
                'options' => false
            ),
            'movement' => array(
                'default' => 'move',
                'options' => array(
                    'move' => __( 'Sticky, move when page is scrolled', 'wp-socializer' ),
                    'static' => __( 'Static, no movement', 'wp-socializer' )
                )
            ),
            'share_counter' => array(
                'default' => 'total-individual',
                'options' => self::common_options( 'share_counter' )
            ),
            'sc_style' => array(
                'default' => 'count-1',
                'options' => self::common_options( 'sc_style' )
            ),
            'sc_total_position' => array(
                'default' => 'top',
                'options' => array(
                    'top' => 'Above the icons',
                    'bottom' => 'Below the icons'
                )
            ),
            'sc_total_color' => array(
                'default' => '#000000',
                'options' => false
            ),
        
            'sm_screen_width' => array(
                'default' => '768',
                'options' => false
            ),
            'lg_screen_action' => array(
                'default' => 'show',
                'options' => array(
                    'show' => __( 'Show', 'wp-socializer' ),
                    'hide' => __( 'Hide', 'wp-socializer' ),
                    'close' => __( 'Close', 'wp-socializer' )
                )
            ),
            'sm_screen_action' => array(
                'default' => 'bottom',
                'options' => array(
                    'bottom' => __( 'Show to bottom of the page', 'wp-socializer' ),
                    'hide' => __( 'Hide', 'wp-socializer' )
                )
            ),
            'sm_simple' => array(
                'default' => 'yes',
                'options' => array(
                    'yes' => __( 'Yes', 'wp-socializer' ),
                    'no' => __( 'No', 'wp-socializer' )
                )
            ),
        
            'more_icons' => array(
                'default' => '0',
                'options' => self::common_options( 'more_icons' )
            ),
            'loc_rules' => array(
                'default' => array(
                    'type' => 'show_all',
                    'rule' => 'W10='
                ),
                'options' => false
            )
        );

    }

    public static function follow_icons(){

        return array(
            'ft_status' => array(
                'default' => 'disable',
                'options' => array(
                    'enable' => __( 'Enable follow icons', 'wp-socializer' ),
                    'disable' => __( 'Disable follow icons', 'wp-socializer' )
                )
            ),
            'template' => array(
                'default' => '[]',
                'options' => false
            ),
            'shape' => array(
                'default' => '',
                'options' => self::common_options( 'icon_shape' ),
                'description' => __( 'The shape of the icons.', 'wp-socializer' )
            ),
            'size' => array(
                'default' => '32px',
                'options' => self::common_options( 'icon_size' ),
                'description' => __( 'The size of the icons.', 'wp-socializer' )
            ),
            'bg_color' => array(
                'default' => '',
                'options' => false,
                'description' => __( 'The background color of the icons. Leave empty to take the default social media site\'s brand color', 'wp-socializer' )
            ),
            'icon_color' => array(
                'default' => '#ffffff',
                'options' => false,
                'description' => __( 'The color of the icon.', 'wp-socializer' )
            ),
            'orientation' => array(
                'default' => 'vertical',
                'options' => array(
                    'vertical' => array( 'Vertical', 'layout-vertical.svg', '75px' ),
                    'horizontal' => array( 'Horizontal', 'layout-horizontal.svg', '75px' ),
                ),
                'description' => __( 'The orientation of the icon bar.', 'wp-socializer' )
            ),
            'position' => array(
                'default' => 'rm',
                'options' => array(
                    'tl' => array( 'Top left', 'pos-tl.svg', '60px' ),
                    'tm' => array( 'Top middle', 'pos-tm.svg', '60px' ),
                    'tr' => array( 'Top right', 'pos-tr.svg', '60px' ),
                    'rm' => array( 'Right middle', 'pos-rm.svg', '60px' ),
                    'br' => array( 'Bottom right', 'pos-br.svg', '60px' ),
                    'bm' => array( 'Bottom middle', 'pos-bm.svg', '60px' ),
                    'bl' => array( 'Bottom left', 'pos-bl.svg', '60px' ),
                    'lm' => array( 'Left middle', 'pos-lm.svg', '60px' ),
                )
            ),
            'hover' => array(
                'default' => 'zoom',
                'options' => self::common_options( 'hover_effect' ),
                'description' => __( 'The behavior of the icons when mouse is hovered over them.', 'wp-socializer' )
            ),
            'pad' => array(
                'default' => 'pad',
                'options' => array(
                    '' => __( 'No', 'wp-socializer' ),
                    'pad' => __( 'Yes', 'wp-socializer' )
                ),
                'description' => __( 'Decides whether to add a space between the icons.', 'wp-socializer' )
            ),
            'title' => array(
                'default' => '',
                'options' => false
            ),
            'open_popup' => array(
                'default' => 'no',
                'options' => array(
                    'no' => 'No',
                    '' => 'Yes',
                ),
                'description' => __( 'Decides whether to open the links in a popup or in a new tab.', 'wp-socializer' )
            ),
            'sm_screen_width' => array(
                'default' => '768',
                'options' => false,
                'description' => __( 'The screen width below which the icons will act in mobile/small screen mode. In pixels.', 'wp-socializer' )
            ),
            'lg_screen_action' => array(
                'default' => 'show',
                'options' => array(
                    'show' => __( 'Show', 'wp-socializer' ),
                    'hide' => __( 'Hide', 'wp-socializer' ),
                    'close' => __( 'Close', 'wp-socializer' )
                ),
                'description' => __( 'The behavior of the icons in desktop/large screens.', 'wp-socializer' )
            ),
            'sm_screen_action' => array(
                'default' => 'show',
                'options' => array(
                    'show' => __( 'Show', 'wp-socializer' ),
                    'hide' => __( 'Hide', 'wp-socializer' ),
                    'close' => __( 'Close', 'wp-socializer' )
                ),
                'description' => __( 'The behavior of the icons in mobile/small screens.', 'wp-socializer' )
            ),
            'loc_rules' => array(
                'default' => array(
                    'type' => 'show_all',
                    'rule' => 'W10='
                ),
                'options' => false
            )
        );

    }

    public static function text_sharebar(){

        return array(
            'ft_status' => array(
                'options' => array(
                    'enable' => __( 'Enable text sharebar', 'wp-socializer' ),
                    'disable' => __( 'Disable text sharebar', 'wp-socializer' )
                ),
                'default' => 'disable'
            ),
            'template' => array(
                'options' => false,
                'default' => '[]',
            ),
            'content' => array(
                'options' => false,
                'default' => '.entry-content',
            ),
            'size' => array(
                'options' => self::common_options( 'icon_size' ),
                'default' => '32px',
            ),
            'bg_color' => array(
                'options' => false,
                'default' => '#333',
            ),
            'icon_color' => array(
                'options' => false,
                'default' => '#fff',
            ),
            'text_count' => array(
                'options' => false,
                'default' => '20',
            ),
            'loc_rules' => array(
                'options' => false,
                'default' => array(
                    'type' => 'show_selected',
                    'rule' => 'W1tbInNpbmdsZSIsImVxdWFsIiwiIl1dLFtbInBhZ2UiLCJlcXVhbCIsIiJdXV0='
                )
            )
        );

    }

    public static function general_settings(){

        return array(

            // Share icons
            'share_menu' => array(
                'default' => 'yes',
                'options' => array(
                    'no' => __( 'No', 'wp-socializer' ),
                    'yes' => __( 'Yes', 'wp-socializer' )
                )
            ),
            'facebook_app_id' => array(
                'default' => '',
                'options' => false
            ),
            'facebook_app_secret' => array(
                'default' => '',
                'options' => false
            ),
            'facebook_lang' => array(
                'default' => 'en_US',
                'options' => false
            ),
            'twitter_username' => array(
                'default' => '',
                'options' => false
            ),
            'comments_section' => array(
                'default' => 'comments',
                'options' => false
            ),

            // Share counter
            'counter_expiration' => array(
                'default' => '43200',
                'options' => false
            ),
            'counter_both_protocols' => array(
                'default' => 'no',
                'options' => array(
                    'no' => __( 'No', 'wp-socializer' ),
                    'yes' => __( 'Yes', 'wp-socializer' )
                )
            ),

            // Misc settings
            'font_icon' => array(
                'default' => 'fa5',
                'options' => false
            ),
            'misc_additional_css' => array(
                'default' => '',
                'options' => false
            ),
            'skip_res_load' => array(
                'default' => '',
                'options' => false
            )

        );

    }

    public static function post_settings(){

        return array(

            'wpsr_disable_share_icons' => array(
                'default' => 'no',
                'options' => array(
                    'no' => __( 'No', 'wp-socializer' ),
                    'yes' => __( 'Yes', 'wp-socializer' )
                )
            ),
            'wpsr_disable_floating_sharebar' => array(
                'default' => 'no',
                'options' => array(
                    'no' => __( 'No', 'wp-socializer' ),
                    'yes' => __( 'Yes', 'wp-socializer' )
                )
            ),
            'wpsr_disable_follow_icons' => array(
                'default' => 'no',
                'options' => array(
                    'no' => __( 'No', 'wp-socializer' ),
                    'yes' => __( 'Yes', 'wp-socializer' )
                )
            ),
            'wpsr_disable_text_sharebar' => array(
                'default' => 'no',
                'options' => array(
                    'no' => __( 'No', 'wp-socializer' ),
                    'yes' => __( 'Yes', 'wp-socializer' )
                )
            ),

        );

    }

}

?>