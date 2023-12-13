<?php
/**
 * Shortcodes admin page
 *
 **/

defined( 'ABSPATH' ) || exit;

class WPSR_Admin_Shortcodes{
    
    function __construct(){
        
        add_filter( 'wpsr_register_admin_page', array( $this, 'register' ) );
        
    }
    
    function register( $pages ){

        $pages[ 'shortcodes' ] = array(
            'name' => __( 'Shortcodes', 'wp-socializer' ),
            'description' => __( 'Create shortcodes for social sharing icons and follow icons to use them in any custom location.', 'wp-socializer' ),
            'category' => 'feature',
            'type' => 'shortcodes',
            'callbacks' => array(
                'page' => array( $this, 'page' )
            )
        );
        
        return $pages;
        
    }
    
    function page(){
        
        $form = new WPSR_Form();

        $form->section_start();

        $form->tab_list(array(
            'share_icons' => '<i class="fas fa-share-alt"></i>' . esc_html__( 'Share Icons', 'wp-socializer' ),
            'follow_icons' => '<i class="fas fa-user-plus"></i>' . esc_html__( 'Follow Icons', 'wp-socializer' ),
            'share_link' => '<i class="fas fa-link"></i></i>' . esc_html__( 'Share link', 'wp-socializer' )
        ));

        echo '<div class="tab_wrap">';
        $this->tab_share_icons();
        $this->tab_follow_icons();
        $this->tab_share_link();
        echo '</div>';

        echo '<h3>' . esc_html__( 'Save your shortcodes', 'wp-socializer' ) . '</h3>';
        echo '<p>' . esc_html__( 'Save the shortcodes you created with a shortcode creation and management plugin like "Shortcoder" and insert them easily in posts whenever needed.' ) . '</p>';
        if( class_exists( 'Shortcoder' ) && is_plugin_active( 'shortcoder/shortcoder.php' ) ){
            echo '<p><a href="' . esc_url( admin_url( 'post-new.php?post_type=shortcoder' ) ) . '" target="_blank" class="button button-primary">' . esc_html__( 'Open shortcoder', 'wp-socializer' ) . '</a></p>';
        }else{
            if( function_exists( 'add_thickbox' ) ){
                add_thickbox();
            }
            echo '<p><a href="' . esc_url( admin_url( 'plugin-install.php?tab=plugin-information&plugin=shortcoder&TB_iframe=true&width=700&height=550' ) ) . '" class="button button-primary thickbox">' . esc_html__( 'Learn more', 'wp-socializer' ) . '</a></p>';
        }

        echo '<h3>' . esc_html__( 'Using in theme', 'wp-socializer' ) . '</h3>';
        echo '<p>' . esc_html__( 'To use the shortcode anywhere in your theme, use the below PHP snippet and replace the shortcode with the plugin shortcode.', 'wp-socializer' ) . '</p>';
        echo '<pre>&lt;?php echo do_shortcode( \'THE_SHORTCODE\' ); ?&gt;</pre>';

        $form->section_end();

    }

    function tab_share_icons(){

        echo '<div data-tab="share_icons">';

        echo '<h3>' . esc_html__( 'Syntax', 'wp-socializer' ) . '</h3>';
        echo '<pre>[wpsr_share_icons parameter1="value" parameter2="value" ...]</pre>';

        echo '<h3>' . esc_html__( 'Example', 'wp-socializer' ) . '</h3>';
        echo '<pre>[wpsr_share_icons icons="facebook,twitter,pinterest,email" icon_size="40px" icon_bg_color="red" icon_shape="drop"]</pre>';

        echo '<h3>' . esc_html__( 'Parameter reference', 'wp-socializer' ) . '</h3>';
        $options = WPSR_Options::share_icons();

        unset( $options[ 'selected_icons' ] );
        unset( $options[ 'loc_rules' ] );
        unset( $options[ 'position' ] );
        unset( $options[ 'in_excerpt' ] );
        unset( $options[ 'heading' ] );
        unset( $options[ 'custom_html_above' ] );
        unset( $options[ 'custom_html_below' ] );

        echo '<table class="widefat">
            <thead>
                <tr>
                    <th>' . esc_html__( 'Parameter', 'wp-socializer' ) . '</th>
                    <th>' . esc_html__( 'Default value', 'wp-socializer' ) . '</th>
                    <th>' . esc_html__( 'Description', 'wp-socializer' ) . '</th>
                    <th>' . esc_html__( 'Supported values', 'wp-socializer' ) . '</th>
                </tr>
            </thead>
            <tbody>
        ';

        // Adding icons to the param list
        $icons_param = array(
            'icons' => array(
                'default' => 'facebook,twitter,linkedin,pinterest,email',
                'options' => false,
                'description' => __( 'The ID share icons to display separated by comma. See list below for icon IDs.', 'wp-socializer' )
            ),
            'template' => array(
                'default' => '<empty>',
                'options' => array( '1' => '1', '2' => '2' ),
                'description' => __( 'The ID of the template which is configured in the share icons feature settings page. When this is provided, other configurations are NOT considered. Use this parameter only to use the saved configuration in a custom location.', 'wp-socializer' )
            )
        );
        $options = $icons_param + $options;

        $options[ 'page_url' ] = array(
            'default' => __( 'The current post URL', 'wp-socializer' ),
            'description' => __( 'The URL to share', 'wp-socializer' ),
            'options' => false
        );
        $options[ 'page_title' ] = array(
            'default' => __( 'The current post URL', 'wp-socializer' ),
            'description' => __( 'The title of the URL', 'wp-socializer' ),
            'options' => false
        );
        $options[ 'page_excerpt' ] = array(
            'default' => __( 'The current post\'s excerpt', 'wp-socializer' ),
            'description' => __( 'A short description of the page. Honored by some social sharing sites.', 'wp-socializer' ),
            'options' => false
        );

        foreach( $options as $key => $val ){
            $default = empty( $val[ 'default' ] ) ? '<empty>' : $val[ 'default' ];
            $description = isset( $val[ 'description' ] ) ? $val[ 'description' ] : '';

            $supported_values = '';
            if( $val[ 'options' ] && is_array( $val[ 'options' ] ) ){
                $supported_values = array_keys( $val[ 'options' ] );
                $supported_values = array_map(function( $value ){
                    return empty( $value ) ? esc_html( '<empty>' ) : $value;
                }, $supported_values );
                $supported_values = implode( ', ', $supported_values );
            }

            echo '<tr>';
            echo '<td><code>' . esc_html( $key ) . '</code></td>';
            echo '<td><code>' . esc_html( $default ) . '</code></td>';
            echo '<td>' . wp_kses_post( $description ) . '</td>';
            echo '<td>' . wp_kses_post( $supported_values ) . '</td>';
            echo '</tr>';
        }

        echo '</tbody>
        </table>';

        echo '<h3>' . esc_html__( 'Supported share icons', 'wp-socializer' ) . '</h3>';
        $social_icons = WPSR_Lists::social_icons();
        $social_icons = array_filter( $social_icons, function( $props ){
            if( in_array( 'for_share', $props[ 'features' ] ) ){
                return true;
            }else{
                return false;
            }
        });
        echo '<p>' . esc_html( implode( ', ', array_keys( $social_icons ) ) ) . '</p>';

        echo '</div>';

    }

    function tab_follow_icons(){

        echo '<div data-tab="follow_icons">';

        echo '<h3>' . esc_html__( 'Syntax', 'wp-socializer' ) . '</h3>';
        echo '<pre>[wpsr_follow_icons parameter1="value" parameter2="value" ...]</pre>';

        echo '<h3>' . esc_html__( 'Example', 'wp-socializer' ) . '</h3>';
        echo '<pre>[wpsr_follow_icons facebook="https://facebook.com/aakashweb" twitter="https://twitter.com/aakashweb" instagram="https://instagram.com/aakashweb" bg_color="green" shape="circle"]</pre>';

        echo '<h3>' . esc_html__( 'Parameter reference', 'wp-socializer' ) . '</h3>';
        $options = WPSR_Options::follow_icons();

        unset( $options[ 'ft_status' ] );
        unset( $options[ 'template' ] );
        unset( $options[ 'orientation' ] );
        unset( $options[ 'position' ] );
        unset( $options[ 'title' ] );
        unset( $options[ 'loc_rules' ] );

        echo '<table class="widefat">
            <thead>
                <tr>
                    <th>' . esc_html__( 'Parameter', 'wp-socializer' ) . '</th>
                    <th>' . esc_html__( 'Default value', 'wp-socializer' ) . '</th>
                    <th>' . esc_html__( 'Description', 'wp-socializer' ) . '</th>
                    <th>' . esc_html__( 'Supported values', 'wp-socializer' ) . '</th>
                </tr>
            </thead>
            <tbody>
        ';

        // Adding icons to the param list
        $icons_param = array( '&lt;icon_id&gt;' => array(
            'default' => '',
            'options' => false,
            'description' => __( 'The profile URL of the site. See list below for follow icons ID.', 'wp-socializer' )
        ));
        $options = $icons_param + $options;

        foreach( $options as $key => $val ){
            $default = empty( $val[ 'default' ] ) ? '<empty>' : $val[ 'default' ];
            $description = isset( $val[ 'description' ] ) ? $val[ 'description' ] : '';

            $supported_values = '';
            if( $val[ 'options' ] ){
                $supported_values = array_keys( $val[ 'options' ] );
                $supported_values = array_map(function( $value ){
                    return empty( $value ) ? esc_html( '<empty>' ) : $value;
                }, $supported_values );
                $supported_values = implode( ', ', $supported_values );
            }

            echo '<tr>';
            echo '<td><code>' . esc_html( $key ) . '</code></td>';
            echo '<td><code>' . esc_html( $default ) . '</code></td>';
            echo '<td>' . wp_kses_post( $description ) . '</td>';
            echo '<td>' . wp_kses_post( $supported_values ) . '</td>';
            echo '</tr>';
        }

        echo '</tbody>
        </table>';

        echo '<h3>' . esc_html__( 'Supported icons', 'wp-socializer' ) . '</h3>';
        $social_icons = WPSR_Lists::social_icons();
        $social_icons = array_filter( $social_icons, function( $props ){
            if( in_array( 'for_profile', $props[ 'features' ] ) ){
                return true;
            }else{
                return false;
            }
        });
        echo '<p>' . esc_html( implode( ', ', array_keys( $social_icons ) ) ) . '</p>';

        echo '</div>';

    }

    function tab_share_link(){

        echo '<div data-tab="share_link">';

        echo '<h3>' . esc_html__( 'Syntax', 'wp-socializer' ) . '</h3>';
        echo '<pre>[wpsr_share_link parameter1="value" parameter2="value" ...]</pre>';

        echo '<h3>' . esc_html__( 'Example', 'wp-socializer' ) . '</h3>';
        echo '<pre>[wpsr_share_link for="twitter"]Tweet about this page[/wpsr_share_link]</pre>';

        echo '<h3>' . esc_html__( 'Output', 'wp-socializer' ) . '</h3>';
        echo '<pre><a href="https://twitter.com/intent/tweet?text=Post+by+author%20-%20http://example.com/post-by-author/%20@vaakash" target="_blank" rel="nofollow">Tweet about this page</a></pre>';

        echo '<h3>' . esc_html__( 'Parameter reference', 'wp-socializer' ) . '</h3>';

        echo '<table class="widefat">
            <thead>
                <tr>
                    <th>' . esc_html__( 'Parameter', 'wp-socializer' ) . '</th>
                    <th>' . esc_html__( 'Default value', 'wp-socializer' ) . '</th>
                    <th>' . esc_html__( 'Description', 'wp-socializer' ) . '</th>
                    <th>' . esc_html__( 'Supported values', 'wp-socializer' ) . '</th>
                </tr>
            </thead>
            <tbody>
        ';

        $rows = array(
            array(
                'parameter' => 'for',
                'default_value' => '&lt;empty&gt;',
                'description' => __( 'The ID of the social media service to generate share link for.', 'wp-socializer' ),
                'supported_values' => __( 'Refer list below for the supported IDs', 'wp-socializer' )
            ),
            array(
                'parameter' => 'class',
                'default_value' => '&lt;empty&gt;',
                'description' => __( 'Sets the CSS class value for the a tag.', 'wp-socializer' ),
                'supported_values' => ''
            ),
            array(
                'parameter' => 'target',
                'default_value' => '_blank',
                'description' => __( 'Sets the target attribute for the link.', 'wp-socializer' ),
                'supported_values' => ''
            ),
            array(
                'parameter' => 'page_url',
                'default_value' => 'The URL of the current post/page where the shortcode is used.',
                'description' => __( 'Sets the URL to share.', 'wp-socializer' ),
                'supported_values' => ''
            ),
            array(
                'parameter' => 'page_title',
                'default_value' => 'The title of the current post/page where the shortcode is used.',
                'description' => __( 'The title of the page to share', 'wp-socializer' ),
                'supported_values' => ''
            ),
            array(
                'parameter' => 'page_excerpt',
                'default_value' => 'The description of the current post/page where the shortcode is used.',
                'description' => __( 'The description of the page to share', 'wp-socializer' ),
                'supported_values' => ''
            )
        );

        foreach( $rows as $row ){
            echo '<tr>';
            echo '<td>' . esc_html( $row['parameter'] ) . '</td>';
            echo '<td>' . esc_html( $row['default_value'] ) . '</td>';
            echo '<td>' . esc_html( $row['description'] ) . '</td>';
            echo '<td>' . esc_html( $row['supported_values'] ) . '</td>';
            echo '</tr>';
        }

        echo '</tbody>
        </table>';

        echo '<h3>' . esc_html__( 'Supported icons', 'wp-socializer' ) . '</h3>';
        $social_icons = WPSR_Lists::social_icons();
        $social_icons = array_filter( $social_icons, function( $props ){
            if( in_array( 'for_share', $props[ 'features' ] ) ){
                return true;
            }else{
                return false;
            }
        });
        echo '<p>' . esc_html( implode( ', ', array_keys( $social_icons ) ) ) . '</p>';

        echo '</div>';

    }

    public static function note( $feature = '', $shortcode = '' ){
        echo '<div class="note">';
        echo '<h4><i class="fas fa-code"></i>' . esc_html__( 'Shortcode', 'wp-socializer' ) . '</h4>';
        echo '<p>' . sprintf( wp_kses( __( 'If you want to use %s anywhere in a custom position then you can use the shortcode <code>[%s]</code>. Please refer shortcodes page on how to customize this shortcode.', 'wp-socializer' ), array( 'code' => array() ) ), $feature, $shortcode ) . '</p>';
        echo '<p><a href="' . esc_url( admin_url( 'admin.php?page=wp_socializer&tab=shortcodes' ) ) . '" target="_blank" class="button button-primary">' . esc_html__( 'Create shortcode', 'wp-socializer' ) . '</a></p>';
        echo '</div>';
    }

}

new WPSR_Admin_Shortcodes();

?>