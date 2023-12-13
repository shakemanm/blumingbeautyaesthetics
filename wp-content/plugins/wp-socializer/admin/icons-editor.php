<?php

defined( 'ABSPATH' ) || exit;

class WPSR_Icons_Editor{

    public static function editor( $selected_icons, $form_name = 'selected_icons' ){

        $social_icons = WPSR_Lists::social_icons();

        echo '<div class="sie_wrap">';

        echo '<div class="sie_editor">';
        echo '<ul class="sic_list sie_selected">';

        $si_selected = array();
        $si_saved = WPSR_Lists::parse_template( $selected_icons );

        foreach( $si_saved as $icon ){
            $id = key( $icon );
            $settings = $icon[ $id ];
            array_push( $si_selected, array( $id => $settings ) );
        }

        foreach( $si_saved as $si_icons ){
            foreach( $si_icons as $id => $settings ){

                if( !array_key_exists( $id, $social_icons ) ){
                    continue;
                }

                $datas = array();
                $props = $social_icons[ $id ];
                array_push( $datas, 'data-id="' . esc_attr( $id ) . '"' );

                foreach( $settings as $ics_id => $ics_value ){
                    array_push( $datas, 'data-icns_' . esc_attr( $ics_id ) . '="' . esc_attr( $ics_value ) . '"' );
                }

                $datas = implode( ' ', $datas );
                echo '<li ' . wp_kses( $datas, array() ) . ' style="background-color: ' . esc_attr( $props[ 'colors' ][ 0 ] ) . '" title="' . esc_attr( $props[ 'name' ] ) . '">';
                    echo '<i class="' . esc_attr( $props[ 'icon' ] ) . ' item_icon" ></i> ';
                    echo '<span>' . esc_html( $props[ 'name' ] ) . '</span>';
                    echo '<i class="fa fa-times sic_action_btn sie_delete_btn" title="' . esc_attr__( 'Delete icon', 'wp-socializer' ) . '"></i> ';
                    echo '<i class="fa fa-cog sic_action_btn sie_settings_btn" title="' . esc_attr__( 'Icon settings', 'wp-socializer' ) . '"></i> ';
                echo '</li>';

            }
        }

        echo '</ul>';
        echo '</div>';

        echo '<div class="sie_toolbar">';
        echo '<button class="button button-primary sie_open_picker_btn"><i class="fas fa-plus" title="Add icon"></i> ' . esc_html__( 'Add social icon', 'wp-socializer' ) . '</button>';
        echo '</div>';

        echo '<input type="hidden" name="' . esc_attr( $form_name ) . '" class="sie_selected_icons" value="' . esc_attr( $selected_icons ) . '"/>';

        echo '</div>';

    }

    public static function commons( $allowed_icon_settings ){

        $social_icons = WPSR_Lists::social_icons();
        $icon_settings = self::icon_settings( $allowed_icon_settings );

        // Editor - Icon settings
        echo '<div class="sie_icon_settings sic_backdrop">
        <div class="sic_content">
        <header>
            <h3></h3>
            <i class="fa fa-times sic_close_btn"></i>
        </header>';
        echo '<section></section>';
        echo '<footer><button class="button button-primary sie_save_settings_btn">' . esc_html__( 'Save icon settings', 'wp-socializer' ) . '</button></footer>';
        echo '</div>
        </div>';

        // Picker
        echo '<div class="sip_picker sic_backdrop">
        <div class="sic_content">
        <header>
            <h3>Select an icon to add</h3>
            <i class="fa fa-times sic_close_btn"></i>
        </header>';

        echo '<section>';
        echo '<input type="search" class="widefat sip_filter" placeholder="Search icon"/>';
        echo '<p class="description">' . esc_html__( 'Note: Only services to which a link can be shared are listed below.', 'wp-socializer' ) . '</p>';
        echo '<ul class="sic_list sip_selector">';

        foreach( $social_icons as $id => $props ){
            $datas = array();
            array_push( $datas, 'data-id="' . esc_attr( $id ) . '"' );

            if( !in_array( 'for_share', $props[ 'features' ] ) ){
                continue;
            }

            foreach( $icon_settings as $is_id => $is_props ){

                if( $id == 'html' && $is_id != 'html' ){
                    continue;
                }
                
                if( $id != 'html' && $is_id == 'html' ){
                    continue;
                }

                array_push( $datas, 'data-icns_' . $is_id . '=""' );
            }

            $datas = implode( ' ', $datas );
            echo '<li ' . wp_kses( $datas, array() ) . ' style="background-color: ' . esc_attr( $props[ 'colors' ][ 0 ] ) . '" title="' . esc_attr( $props[ 'name' ] ) . '">';
                echo '<i class="' . esc_attr( $props[ 'icon' ] ) . ' item_icon" ></i> ';
                echo '<span>' . esc_html( $props[ 'name' ] ) . '</span>';
                echo '<i class="fas fa-plus sic_action_btn sip_add_btn" title="' . esc_attr__( 'Add icon', 'wp-socializer' ) . '"></i>';
                echo '<i class="fa fa-times sic_action_btn sie_delete_btn" title="' . esc_attr__( 'Delete icon', 'wp-socializer' ) . '"></i> ';
                echo '<i class="fa fa-cog sic_action_btn sie_settings_btn" title="' . esc_attr__( 'Icon settings', 'wp-socializer' ) . '"></i> ';
            echo '</li>';
        }

        echo '</ul>';
        echo '</section>';

        echo '<footer style="text-align: left"><a class="button" href="https://www.aakashweb.com/wordpress-plugins/wp-socializer/?utm_source=admin&utm_medium=custom_icons&utm_campaign=wpsr-pro" target="_blank">Create custom icon <span class="pro_tag">PRO</span></a></footer>';

        echo '</div>
        </div>';

        echo '<script>';
        echo 'var sip_icons = ' . json_encode( $social_icons ) . ';';
        echo 'var sip_icon_settings = ' . json_encode( $icon_settings ) . ';';
        echo '</script>';

    }

    public static function icon_settings( $allowed_icon_settings ){

        $all_icon_settings = array(
            'icon' => array(
                'type' => 'text',
                'helper' => __( 'Custom icon', 'wp-socializer' ),
                'placeholder' => __( 'Enter a custom icon URL for this site, starting with <code>http://</code>. You can also use class name of the icon font Example: <code>fa fa-star</code> Leave blank to use default icon', 'wp-socializer' )
            ),
            'text' => array(
                'type' => 'text',
                'helper' => __( 'Text to show next to icon', 'wp-socializer' ),
                'placeholder' => __( 'Enter custom text to appear to next to the icon. Leave blank to show no text.', 'wp-socializer' )
            ),
            'hover_text' => array(
                'type' => 'text',
                'helper' => __( 'Text to show on hovering the icon', 'wp-socializer' ),
                'placeholder' => __( 'Enter custom text to appear when the icon is hovered.', 'wp-socializer' )
            ),
            'html' => array(
                'type' => 'textarea',
                'helper' => __( 'Custom HTML', 'wp-socializer' ),
                'placeholder' => __( 'Enter custom HTML to occur. You can use the parameters <code>{url}</code> and <code>{title}</code> to replace them with the current page URL and title respectively. Shortcodes can also be used here.<br/><strong>Note:</strong> For any formatting issues, please use custom CSS to adjust the output as required.', 'wp-socializer' )
            )
        );

        foreach( $all_icon_settings as $id => $props ){
            if( !in_array( $id, $allowed_icon_settings ) ){
                unset( $all_icon_settings[ $id ] );
            }
        }

        return $all_icon_settings;

    }

}

?>