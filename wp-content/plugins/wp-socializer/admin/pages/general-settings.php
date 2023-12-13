<?php
/**
  * General settings admin page
  *
  **/

defined( 'ABSPATH' ) || exit;

class WPSR_Admin_Settings{
    
    function __construct(){
        
        add_filter( 'wpsr_register_admin_page', array( $this, 'register' ) );
        
        add_action( 'wpsr_form_general_settings', array( $this, 'general_settings' ), 10, 1 );
        
    }
    
    function register( $pages ){
        
        $pages[ 'general_settings' ] = array(
            'name' => __( 'Settings', 'wp-socializer' ),
            'category' => 'other',
            'type' => 'settings',
            'form_name' => 'general_settings',
            'callbacks' => array(
                'page' => array( $this, 'page' ),
                'validation' => array( $this, 'validation' )
            )
        );
        
        return $pages;
        
    }
    
    function page(){
        
        WPSR_Admin::settings_form( 'general_settings' );
    }
    
    function validation( $input ){

        if( intval( $input[ 'counter_expiration' ] ) < 1800 ){
            $input[ 'counter_expiration' ] = 1800;
        }

        array_walk_recursive( $input, function ( &$value, $key ){

            if( $key == 'misc_additional_css' ){
                $value = sanitize_textarea_field( $value );
                return;
            }

            $value = WPSR_Lists::sanitize_data( $key, $value, array());

        });

        return $input;

    }

    function general_settings( $values ){
        
        $form = new WPSR_Form();
        $values = WPSR_Lists::set_defaults( $values, WPSR_Options::default_values( 'general_settings' ) );
        $options = WPSR_Options::options( 'general_settings' );
        
        // Share icons settings
        $form->section_start( __( 'Share icons settings', 'wp-socializer' ) );

        $form->label( __( 'Show share menu', 'wp-socializer' ) );
        $form->field( 'select', array(
            'type' => 'number',
            'name' => 'share_menu',
            'value' => $values[ 'share_menu' ],
            'list' => $options[ 'share_menu' ]
        ));
        $form->end();

        $form->label( __( 'Facebook App ID', 'wp-socializer' ) );
        $form->field( 'text', array(
            'name' => 'facebook_app_id',
            'value' => $values[ 'facebook_app_id' ]
        ));
        $form->end();

        $form->label( __( 'Facebook App secret', 'wp-socializer' ) );
        $form->field( 'text', array(
            'name' => 'facebook_app_secret',
            'value' => $values[ 'facebook_app_secret' ],
            'helper' => 'You can find your facebook app ID and secret in <a href="https://developers.facebook.com/apps/" target="_blank">this page</a> under your app --> Settings --> Basic. Please create a new facebook app if there are no apps. This is used to retrieve facebook share/like count of the URL.'
        ));
        $form->end();

        $form->label( __( 'Facebook SDK language', 'wp-socializer' ) );
        $form->field( 'select', array(
            'name' => 'facebook_lang',
            'value' => $values[ 'facebook_lang' ],
            'list' => WPSR_Lists::lang_codes( 'facebook' )
        ));
        $form->end();

        $form->label( __( 'Twitter username', 'wp-socializer' ) );
        $form->field( 'text', array(
            'name' => 'twitter_username',
            'value' => $values[ 'twitter_username' ],
            'helper' => __( 'Your twitter username without @ sign', 'wp-socializer' )
        ));
        $form->end();

        $form->label( __( 'Comments section ID', 'wp-socializer' ) );
        $form->field( 'text', array(
            'name' => 'comments_section',
            'value' => $values[ 'comments_section' ],
            'helper' => __( 'The ID of the comments section. This is used by the comments button to navigate.', 'wp-socializer' )
        ));
        $form->end();

        $form->build();
        $form->section_end();
        
        // Share counter
        $form->section_start( __( 'Share counter settings', 'wp-socializer' ) );

        $form->label( __( 'Share count cache duration', 'wp-socializer' ) );
        $form->field( 'text', array(
            'type' => 'number',
            'name' => 'counter_expiration',
            'value' => $values[ 'counter_expiration' ],
            'after_text' => 'seconds',
            'helper' => __( 'Enter the number of seconds to cache the share count for every URL in the database. Default is 43200 seconds i.e 1/2 day. Minimum value required is 1800 seconds i.e 30 minutes', 'wp-socializer' )
        ));
        $form->end();

        $form->label( __( 'Include share count of both http and https URL ?', 'wp-socializer' ) );
        $form->field( 'select', array(
            'name' => 'counter_both_protocols',
            'value' => $values[ 'counter_both_protocols' ],
            'list' => $options[ 'counter_both_protocols' ],
            'helper' => __( 'If your site was migrated from http to https and lost your share counts then please enable this option to include the old URL for the count.', 'wp-socializer' )
        ));
        $form->end();

        $form->build();
        $form->section_end();

        // Misc settings
        $form->section_start( __( 'Miscellaneous settings', 'wp-socializer' ) );

        $font_icons = WPSR_Lists::font_icons();
        $font_icons_list = array();
        
        foreach( $font_icons as $id => $prop ){
            $font_icons_list[$id] = $prop['name'];
        }
        
        $inc_list = WPSR_Includes::list_all();
        $inc_text = '<code>' . implode('</code>, <code>', array_keys($inc_list) ) . '</code>';

        $form->label( __( 'Font icon', 'wp-socializer' ) );
        $form->field( 'select', array(
            'name' => 'font_icon',
            'value' => $values['font_icon'], 
            'list' => $font_icons_list
        ));
        $form->end();
        
        $form->label( __( 'Additional CSS rules', 'wp-socializer' ) );
        $form->field( 'textarea', array(
            'name' => 'misc_additional_css',
            'value' => $values['misc_additional_css'],
            'helper' => __( 'Enter custom CSS rules to customize without the style tag', 'wp-socializer' ),
            'rows' => '3',
            'class' => 'widefat'
        ));
        $form->end();
        
        $form->label( __( 'CSS/JS to not to load in any page', 'wp-socializer' ) );
        $form->field( 'text', array(
            'name' => 'skip_res_load',
            'value' => $values['skip_res_load'],
            'helper' => __( 'Enter the ID of the CSS/JS resources to not to load in any page. <a href="#" class="tblr_btn" data-id="res_info_box">Click here</a> to see the list of resources. <div class="hidden" data-tglr="res_info_box"><p>' . $inc_text . '</p> <p>Enter the IDs separated by comma. <b>Note: Many of the resources are intelligently loaded based on buttons used in the page. Please use this field only after discussion with the developer.</b></p></div>', 'wp-socializer' )
        ));
        $form->end();

        $form->build();
        $form->section_end();

    }
    
}

new WPSR_Admin_Settings();

?>