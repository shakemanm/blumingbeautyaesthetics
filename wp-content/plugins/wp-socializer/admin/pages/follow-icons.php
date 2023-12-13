<?php
/**
 * Follow icons admin page
 *
 **/

defined( 'ABSPATH' ) || exit;

class WPSR_Admin_Follow_Icons{
    
    public static function init(){
        
        add_filter( 'wpsr_register_admin_page', array( __class__, 'register' ) );
        
        add_action( 'wp_ajax_wpsr_follow_icons_editor', array( __class__, 'popup_editor' ) );
        
    }
    
    public static function register( $pages ){

        $pages[ 'follow_icons' ] = array(
            'name' => __( 'Follow icons', 'wp-socializer' ),
            'description' => __( 'Add floating/sticky follow icons with links to your social media profiles.', 'wp-socializer' ),
            'category' => 'feature',
            'type' => 'feature',
            'form_name' => 'followbar_settings',
            'callbacks' => array(
                'page' => array( __class__, 'page' ),
                'form' => array( __class__, 'form' ),
                'validation' => array( __class__, 'validation' ),
            )
        );
        
        return $pages;
        
    }
    
    public static function page(){
        WPSR_Admin::settings_form( 'follow_icons' );
    }

    public static function form( $values ){
        
        $values = WPSR_Lists::set_defaults( $values, WPSR_Options::default_values( 'follow_icons' ) );
        $options = WPSR_Options::options( 'follow_icons' );
        $form = new WPSR_Form( $values, $options );

        // Section 0
        $form->section_start( __( 'Enable/disable follow icons', 'wp-socializer' ), '1' );
        $form->label( __( 'Select to enable or disable follow icons feature', 'wp-socializer' ) );
        $form->field( 'select', array(
            'name' => 'ft_status',
            'value' => $values[ 'ft_status' ],
            'list' => $options[ 'ft_status' ],
        ));
        $form->build();
        $form->section_end();

        echo '<div class="feature_wrap">';

        // Section 1
        $form->section_start( __( 'Add and edit follow icons', 'wp-socializer' ), '2' );
        $form->section_description( __( 'Below are the icons added to follow bar. Open editor to add and rearrange the icons.', 'wp-socializer' ) );

        echo '<h4>' . esc_html__( 'Selected icons', 'wp-socializer' ) . '</h4>';
        $template = self::read_template( $values[ 'template' ] );
        echo '<div id="fb_prev_wrap">' . wp_kses( $template[ 'prev' ], WPSR_Lists::allowed_tags() ) . '</div>';
        
        echo '<input type="hidden" id="fb_template_val" name="template" value="' . esc_attr( $values[ 'template' ] ) . '" />';
        echo '<p align="center"><button class="button button-primary wpsr_ppe_fb_open" data-cnt-id="fb_template_val" data-prev-id="fb_prev_wrap"><i class="fa fa-pencil-alt"></i> ' . esc_html__( 'Open editor', 'wp-socializer' ) . '</button></p>';
        $form->section_end();

        // Settings
        $form->section_start( __( 'Settings', 'wp-socializer' ), '3' );

        $form->tab_list(array(
            'style' => '<i class="fas fa-paint-brush"></i>' . esc_html__( 'Style', 'wp-socializer' ),
            'position' => '<i class="fas fa-arrows-alt"></i>' . esc_html__( 'Position', 'wp-socializer' ),
            'responsiveness' => '<i class="fas fa-mobile-alt"></i>' . esc_html__( 'Responsiveness', 'wp-socializer' ),
            'misc' => '<i class="fas fa-cog"></i>' . esc_html__( 'Miscellaneous', 'wp-socializer' ),
        ));

        echo '<div class="tab_wrap">';
        self::tab_style( $form );
        self::tab_position( $form );
        self::tab_responsiveness( $form );
        self::tab_misc( $form );
        echo '</div>';

        $form->section_end();
        
        // Location rules
        $form->section_start( __( 'Conditions to display the follow icons', 'wp-socializer' ), '4' );
        $form->section_description( __( 'Choose the below options to select the pages which will display the follow icons.', 'wp-socializer' ) );
        WPSR_Location_Rules::display_rules( 'loc_rules', $values[ 'loc_rules' ] );
        $form->section_end();
        
        echo '</div>';
    }
    
    public static function tab_style( $form ){

        echo '<div data-tab="style">';

        $form->label( __( 'Icon shape', 'wp-socializer' ) );
        $form->field( 'image_select', array(
            'name' => 'shape',
            'value' => $form->values[ 'shape' ], 
            'list' => $form->options[ 'shape' ],
        ));
        $form->end();

        $form->label( __( 'Icon size', 'wp-socializer' ) );
        $form->field( 'image_select', array(
            'name' => 'size',
            'value' => $form->values[ 'size' ], 
            'list' => $form->options[ 'size' ],
        ));
        $form->end();

        $form->label( __( 'Icon background color', 'wp-socializer' ) );
        $form->field( 'text', array(
            'name' => 'bg_color',
            'value' => $form->values['bg_color'],
            'class' => 'color_picker',
            'helper' => __( 'Set empty value to use brand color', 'wp-socializer' )
        ));
        $form->end();

        $form->label( __( 'Icon color', 'wp-socializer' ) );
        $form->field( 'text', array(
            'name' => 'icon_color',
            'value' => $form->values['icon_color'],
            'class' => 'color_picker'
        ));
        $form->end();
        
        $form->label( __( 'Hover effect', 'wp-socializer' ) );
        $form->field( 'select', array(
            'name' => 'hover',
            'value' => $form->values[ 'hover' ], 
            'list' => $form->options[ 'hover' ],
        ));
        $form->end();

        $form->label( __ ( 'Space between icons', 'wp-socializer' ) );
        $form->field( 'select', array(
            'name' => 'pad',
            'value' => $form->values[ 'pad' ],
            'list' => $form->options[ 'pad' ],
            'helper' => __( 'Select to add space between buttons', 'wp-socializer' )
        ));
        $form->end();

        $form->build();

        echo '</div>';

    }

    public static function tab_position( $form ){

        echo '<div data-tab="position">';

        $form->label( __( 'Orientation', 'wp-socializer' ) );
        $form->field( 'image_select', array(
            'name' => 'orientation',
            'value' => $form->values[ 'orientation' ],
            'list' => $form->options[ 'orientation' ]
        ));
        $form->end();
        
        $form->label( __( 'Position', 'wp-socializer' ) );
        $form->field( 'image_select', array(
            'name' => 'position',
            'value' => $form->values[ 'position' ], 
            'list' => $form->options[ 'position' ]
        ));
        $form->end();

        $form->build();

        WPSR_Admin_Shortcodes::note( __( 'Follow icons', 'wp-socializer' ), 'wpsr_follow_icons' );

        echo '</div>';

    }

    public static function tab_responsiveness( $form ){

        echo '<div data-tab="responsiveness">';

        $form->label( __( 'On desktop (or) large screen', 'wp-socializer' ) );
        $form->field( 'select', array(
            'name' => 'lg_screen_action',
            'value' => $form->values[ 'lg_screen_action' ],
            'list' => $form->options[ 'lg_screen_action' ],
        ));
        $form->end();

        $form->label( __( 'On mobile (or) small screen', 'wp-socializer' ) );
        $form->field( 'select', array(
            'name' => 'sm_screen_action',
            'value' => $form->values[ 'sm_screen_action' ],
            'list' => $form->options[ 'sm_screen_action' ],
        ));
        $form->end();

        $form->label( __( 'Responsive width', 'wp-socializer' ) );
        $form->field( 'text', array(
            'name' => 'sm_screen_width',
            'value' => $form->values[ 'sm_screen_width' ],
            'type' => 'number',
            'helper' => __( 'The width of the screen below which the follow icons switches to mobile mode.' ),
            'after_text' => 'px'
        ));
        $form->end();

        $form->build();

        echo '</div>';

    }

    public static function tab_misc( $form ){

        echo '<div data-tab="misc">';

        $form->label( __( 'Text above the icons', 'wp-socializer' ) );
        $form->field( 'text', array(
            'name' => 'title',
            'value' => $form->values['title'],
            'helper' => __( 'Text to show above the follow icons', 'wp-socializer' ),
        ));
        $form->end();

        $form->label( __( 'Open links in popup', 'wp-socializer' ) );
        $form->field( 'select', array(
            'name' => 'open_popup',
            'value' => $form->values[ 'open_popup' ],
            'list' => $form->options[ 'open_popup' ],
        ));
        $form->end();

        $form->build();

        echo '</div>';

    }

    public static function validation( $input ){

        array_walk_recursive( $input, function ( &$value, $key ){
            $value = WPSR_Lists::sanitize_data( $key, $value, array(
                'title'
            ));
        });

        return $input;

    }

    public static function popup_editor(){
        
        global $hook_suffix;
        $hook_suffix = WPSR_Admin::$pagehook;
        set_current_screen( $hook_suffix );
        
        iframe_header( 'WP Socializer follow icons editor' );
        
        if( !isset( $_GET[ 'cnt_id' ] ) || !isset( $_GET[ 'prev_id' ] ) ){
            echo '<p align="center">Incomplete info to load editor !</p></body></html>';
            wp_die();
        }

        $sb_sites = WPSR_Lists::social_icons();
        echo '<script>window.social_icons = ' . json_encode( $sb_sites ) . ';</script>';
        echo '<script>window.li_template = \'' . self::li_template() . '\';</script>';
        echo '<style>body{background: #fff;}</style>';
        
        echo '<div id="wpsr_pp_editor">';
        echo '<h2>WP Socializer - Follow icons editor</h2>';
        echo '<p>' . esc_html__( 'Select the follow icon below and add it to follow icons list. Drag and drop to rearrange the icons.', 'wp-socializer' ) . '</p>';

        echo '<table class="form-table follow_bar_tbl"><tr><td width="80%">';
        echo '<select class="fb_list widefat">';
        foreach( $sb_sites as $id => $prop ){
            if( !in_array( 'for_profile', $prop[ 'features' ] ) ){
                continue;
            }
            echo '<option value="' . esc_attr( $id ) . '">' . esc_html( $prop[ 'name' ] ) . '</option>';
        }
        echo '</select>';
        echo '</td><td>';
        echo '<button class="fb_add button button-primary widefat">' . esc_html__( 'Add to follow icons', 'wp-socializer' ) . '</button>';
        echo '</td></tr></table>';
        
        $template = self::read_template();
        echo '<ul class="fb_selected">';
        echo wp_kses( $template[ 'editor' ], WPSR_Lists::allowed_tags() );
        echo '</ul>';
        
        echo '<input type="hidden" class="fb_template" />';
        
        echo '<hr/ >';
        echo '<h3>Note:</h3>';
        echo '<ul>';
        echo '<li>For WhatsApp use profile URL as <code>https://wa.me/1XXXXXXXXXX</code>. <a href="https://faq.whatsapp.com/general/chats/how-to-use-click-to-chat/" target="_blank">Refer this page</a> for more details.</li>';
        echo '<li>For Email use profile URL as <code>mailto:YOUR_EMAIL_ID</code></li>';
        echo '<li>For Phone use profile URL as <code>tel:YOUR_PHONE_NUMBER</code></li>';
        echo '</ul>';

        echo '<p class="wpsr_ppe_footer" align="right"><a href="https://www.aakashweb.com/wordpress-plugins/wp-socializer/?utm_source=admin&utm_medium=custom_icons&utm_campaign=wpsr-pro" class="button float_left" target="_blank">Add custom icon <span class="pro_tag">PRO</span></a> <button class="button wpsr_ppe_cancel">Cancel</button> &nbsp; <button class="button button-primary wpsr_ppe_save" data-mode="followbar" data-cnt-id="' . esc_attr( $_GET[ 'cnt_id' ] ) . '" data-prev-id="' . esc_attr( $_GET[ 'prev_id' ] ) . '">Apply settings</button></p>';
        
        echo '</div>';

        iframe_footer();
        wp_die();
        
    }
    
    public static function read_template( $template = '' ){

        $btns = WPSR_Lists::parse_template( $template );

        if( !is_array( $btns ) ){
            return array(
                'prev' => '',
                'editor' => ''
            );
        }
        
        $sb_sites = WPSR_Lists::social_icons();
        $editor = '';
        $prev = '';
        
        foreach( $btns as $btn_obj ){
            
            $id = key( $btn_obj );
            
            if(!array_key_exists($id, $sb_sites)){
                continue;
            }
            
            $prop = $sb_sites[ $id ];
            
            $editor .= self::li_template( $id, $prop[ 'name' ], $prop[ 'icon' ], $prop[ 'colors' ][0], $btn_obj[ $id ][ 'url' ], $btn_obj[ $id ][ 'icon' ], $btn_obj[ $id ][ 'text' ] );
            $prev .= '<li title="' . esc_attr( $prop[ 'name' ] ) . '" style="background-color:' . esc_attr( $prop[ 'colors' ][0] ) . '"><i class="' . esc_attr( $prop[ 'icon' ] ) . '"></i></li>';
        }
        
        if( $prev == '' )
            $prev = '<span>' . esc_html__( 'No buttons are added. Open the editor to add buttons.', 'wp-socializer' ) . '</span>';
        
        $prev = '<ul class="fb_preview">' . $prev . '</ul>';
        
        return array(
            'editor' => $editor,
            'prev' => $prev
        );
        
    }
    
    public static function li_template( $id = '%id%', $name = '%name%', $icon = '%icon%', $color = '%color%', $url = '%url%', $iurl = '%iurl%', $text = '%text%' ){

        $title = __( 'Leave blank to use default', 'wp-socializer' );
        $label_url = __( 'Your profile URL (start with https://)', 'wp-socializer' );
        $label_hover_text = __( 'Button hover text', 'wp-socializer' );
        $label_icon_url = __( 'Icon image URL (optional)', 'wp-socializer' );

        return '<li data-id="' . esc_attr( $id ) . '"><h4 style="background-color: ' . esc_attr( $color ) . '" title="' . esc_attr( $name ) . '"><i class="' . esc_attr( $icon ) . ' item_icon"></i>' . esc_html( $name ) . '<a href="#" class="fb_item_control fb_item_remove">' . __( 'Delete', 'wp-socializer' ) . '</a></h4><div><label><span>' . esc_html( $label_url ) . '</span><input type="text" class="widefat fb_item_url" placeholder="Enter profile URL" value="' . esc_attr( $url ) . '" /></label><label><span>' . esc_html( $label_hover_text ) . '</span><input type="text" class="widefat fb_btn_text" placeholder="Enter custom text to show for button" title="' . esc_attr( $title ) . '" value="' . esc_attr( $text ) . '"/></label><label><span>' . esc_html( $label_icon_url ) . '</span><input type="text" class="widefat fb_icon_url" placeholder="Enter custom Icon URL." title="' . esc_attr( $title ) . '" value="' . esc_attr( $iurl ) . '"/></label></div></li>';

    }
    
}

WPSR_Admin_Follow_Icons::init();

?>