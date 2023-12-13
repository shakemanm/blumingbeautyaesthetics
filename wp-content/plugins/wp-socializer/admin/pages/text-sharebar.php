<?php
/**
  * Text sharebar settings page
  *
  **/

defined( 'ABSPATH' ) || exit;

class WPSR_Admin_Text_Sharebar{
    
    function __construct(){
        
        add_filter( 'wpsr_register_admin_page', array( $this, 'register' ) );
        
    }
    
    function register( $pages ){

        $pages[ 'text_sharebar' ] = array(
            'name' => __( 'Text sharebar', 'wp-socializer' ),
            'description' => __( 'Add tooltip to share the text selected by the user on social media sites.', 'wp-socializer' ),
            'category' => 'feature',
            'type' => 'feature',
            'form_name' => 'text_sharebar_settings',
            'callbacks' => array(
                'page' => array( $this, 'page' ),
                'form' => array( $this, 'form' ),
                'validation' => array( $this, 'validation' ),
            ),
        );
        
        return $pages;
        
    }
    
    function page(){
        
        WPSR_Admin::settings_form( 'text_sharebar' );
        
    }

    function form( $values ){

        $values = WPSR_Lists::set_defaults( $values, WPSR_Options::default_values( 'text_sharebar' ) );
        $options = WPSR_Options::options( 'text_sharebar' );
        $form = new WPSR_Form();

        $form->section_start( __( 'Enable/disable text sharebar', 'wp-socializer' ), '1' );
        $form->label( __( 'Select to enable or disable text sharebar feature', 'wp-socializer' ) );
        $form->field( 'select', array(
            'name' => 'ft_status',
            'value' => $values[ 'ft_status' ],
            'list' => $options[ 'ft_status' ],
        ));
        $form->build();
        $form->section_end();

        echo '<div class="feature_wrap">';
        
        $sb_sites = WPSR_Lists::social_icons();
        
        $form->section_start( __( 'Add buttons to text sharebar', 'wp-socializer' ) );
        $form->section_description( __( 'Select buttons from the list below and add it to the selected list.', 'wp-socializer' ) );

        echo '<table class="form-table ssb_tbl"><tr><td width="90%">';
        echo '<select class="ssb_list widefat">';
        foreach( $sb_sites as $id=>$prop ){
            if( in_array( 'for_tsb', $prop[ 'features' ] ) ){
                echo '<option value="' . esc_attr( $id ) . '" data-color="' . esc_attr( $prop['colors'][0] ) . '">' . esc_html( $prop[ 'name' ] ) . '</option>';
            }
        }
        echo '</select>';
        echo '</td><td>';
        echo '<button class="button button-primary ssb_add">' . esc_html__( 'Add button', 'wp-socializer' ) . '</button>';
        echo '</td></tr></table>';
        
        $tsb_btns = WPSR_Lists::parse_template( $values[ 'template' ] );
        
        if( !is_array( $tsb_btns ) ){
            $tsb_btns = array();
        }
        
        echo '<h4>' . esc_html__( 'Selected buttons', 'wp-socializer' ) . '</h4>';
        echo '<ul class="ssb_selected_list clearfix">';
        if( count( $tsb_btns ) > 0 ){
            foreach( $tsb_btns as $tsb_item ){

                if( !array_key_exists( $tsb_item, $sb_sites ) ){
                    continue;
                }

                $sb_info = $sb_sites[ $tsb_item ];
                echo '<li title="' . esc_attr( $sb_info[ 'name' ] ) . '" data-id="' . esc_attr( $tsb_item ) . '" style="background-color:' . esc_attr( $sb_info['colors'][0] ) . '"><i class="' . esc_attr( $sb_info[ 'icon' ] ) . '"></i> <span class="ssb_remove" title="' . esc_attr__( 'Delete button', 'wp-socializer' ) . '">x</span></li>';
            }
        }else{
            echo '<span class="ssb_empty">' . esc_html__( 'No buttons are selected for text sharebar', 'wp-socializer' ) . '</span>';
        }
        echo '</ul>';
        echo '<input type="hidden" name="template" class="ssb_template" value="' . esc_attr( $values[ 'template' ] ) . '"/>';
        
        $form->section_end();
        
        // Settings form
        $form->section_start( __( 'Settings' ), '3' );

        $form->label( __( 'ID or CSS class name of the content to show text sharebar', 'wp-socializer' ) );
        $form->field( 'text', array(
            'name' => 'content',
            'value' => $values['content'],
            'placeholder' => 'Ex: .entry-content',
            'helper' => '<a href="https://www.youtube.com/watch?v=GQ1YO0xZ7WA" target="_blank">Watch quick video to identify this</a>'
        ));
        $form->end();

        $form->label( __( 'Button size', 'wp-socializer' ) );
        $form->field( 'image_select', array(
            'name' => 'size',
            'value' => $values['size'], 
            'list' => $options[ 'size' ]
        ));
        $form->end();

        $form->label( __( 'Background color', 'wp-socializer' ) );
        $form->field( 'text', array(
            'name' => 'bg_color',
            'value' => $values['bg_color'],
            'class' => 'color_picker'
        ));
        $form->end();

        $form->label( __( 'Icon color', 'wp-socializer' ) );
        $form->field( 'text', array(
            'name' => 'icon_color',
            'value' => $values['icon_color'],
            'class' => 'color_picker'
        ));
        $form->end();

        $form->label( __( 'Maximum word count to quote', 'wp-socializer' ) );
        $form->field( 'text', array(
            'type' => 'number',
            'name' => 'text_count',
            'value' => $values['text_count'],
            'helper' => __( 'Set value to 0 to include all the selected text', 'wp-socializer' )
        ));
        $form->end();

        $form->build();
        $form->section_end();
        
        // Location rules
        $form->section_start( __( 'Conditions to display the text sharebar', 'wp-socializer' ), '4' );
        $form->section_description( __( 'Choose the below options to select the pages which will display the text sharebar.', 'wp-socializer' ) );
        WPSR_Location_Rules::display_rules( 'loc_rules', $values[ 'loc_rules' ] );
        $form->section_end();

        echo '</div>';
        
        echo '<script>';
        echo 'var sb_sites = ' . json_encode( $sb_sites ) . ';';
        echo '</script>';
        
    }
    
    function validation( $input ){

        array_walk_recursive( $input, function ( &$value, $key ){
            $value = WPSR_Lists::sanitize_data( $key, $value, array() );
        });

        return $input;

    }

}

new WPSR_Admin_Text_Sharebar();

?>