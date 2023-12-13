<?php

defined( 'ABSPATH' ) || exit;

class WPSR_Post_Settings{

    public static function init(){

        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ), 10, 1 );

        add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_boxes' ) );

        add_action( 'save_post', array( __CLASS__, 'save_post' ), 10, 2 );

    }

    public static function supported_post_types(){
        return array( 'post', 'page' );
    }

    public static function enqueue_scripts( $hook ){

        global $post;

        $post_types = self::supported_post_types();

        if ( !($hook == 'post.php' || $hook == 'post-new.php') || !in_array( $post->post_type, $post_types ) ) {
            return;
        }

        wp_enqueue_style( 'wpsr_ps_css', WPSR_ADMIN_URL . 'css/style_post_settings.css', array(), WPSR_VERSION );

        wp_enqueue_script( 'wpsr_ps_js', WPSR_ADMIN_URL . 'js/script_post_settings.js', array( 'jquery' ), WPSR_VERSION );

    }

    public static function add_meta_boxes(){

        $post_types = self::supported_post_types();

        foreach( $post_types as $post_type ){
            add_meta_box( 'wpsr_post_settings', 'WP Socializer', array( __CLASS__, 'post_settings' ), $post_type, 'normal', 'default' );
        }

    }

    public static function post_settings( $post ){

        $options = WPSR_Options::options( 'post_settings' );
        $values = WPSR_Lists::post_settings( $post );
        $form = new WPSR_Form();

        echo '<div class="wpsr_ps_wrap">';

        echo '<div class="wpsr_ps_tab_list_wrap">';
        echo '<ul class="wpsr_ps_tab_list">';
            echo '<li><a href="#features" class="active" data-wpsr-tab-id="features"><span class="dashicons dashicons-share"></span> ' . esc_html__( 'Features', 'wp-socializer' ) . '</a></li>';
            echo '<li><a href="#sharing-information" data-wpsr-tab-id="sharing-information"><span class="dashicons dashicons-info"></span> ' . esc_html__( 'Sharing information', 'wp-socializer' ) . '</a></li>';
        echo '</ul>';
        echo '</div>';

        echo '<div class="wpsr_ps_tab_wrap">';
        self::tab_features( $form, $values, $options );
        self::tab_sharing_information( $form, $values, $options );
        echo '</div>';

        echo '</div>';

        wp_nonce_field( 'wpsr_post_settings_nonce', '_wpsr_ps_nonce' );

    }

    public static function tab_features( $form, $values, $options ){

        echo '<div data-wpsr-tab="features">';

        echo '<p class="wpsr_ps_head">' . esc_html__( 'Disable below social features on this post in specific if they are enabled.', 'wp-socializer' ) . '</p>';

        $form->label( __( 'Share icons', 'wp-socializer' ) );
        $form->field( 'select', array(
            'type' => 'number',
            'name' => 'wpsr_disable_share_icons',
            'value' => $values[ 'wpsr_disable_share_icons' ],
            'list' => $options[ 'wpsr_disable_share_icons' ]
        ));
        $form->end();

        $form->label( __( 'Floating sharebar', 'wp-socializer' ) );
        $form->field( 'select', array(
            'type' => 'number',
            'name' => 'wpsr_disable_floating_sharebar',
            'value' => $values[ 'wpsr_disable_floating_sharebar' ],
            'list' => $options[ 'wpsr_disable_floating_sharebar' ]
        ));
        $form->end();

        $form->label( __( 'Follow icons', 'wp-socializer' ) );
        $form->field( 'select', array(
            'type' => 'number',
            'name' => 'wpsr_disable_follow_icons',
            'value' => $values[ 'wpsr_disable_follow_icons' ],
            'list' => $options[ 'wpsr_disable_follow_icons' ]
        ));
        $form->end();

        $form->label( __( 'Text sharebar', 'wp-socializer' ) );
        $form->field( 'select', array(
            'type' => 'number',
            'name' => 'wpsr_disable_text_sharebar',
            'value' => $values[ 'wpsr_disable_text_sharebar' ],
            'list' => $options[ 'wpsr_disable_text_sharebar' ]
        ));
        $form->end();

        $form->build( 'wpsr' );

        echo '</div>';

    }

    public static function tab_sharing_information( $form, $values, $options ){

        global $post;

        echo '<div data-wpsr-tab="sharing-information">';
        
        echo '<div class="notice notice-success inline"><p>Do you know you can change the <b>tweet text</b>, <b>share URL</b>, <b>title</b> and more which are sent for sharing ? You can with WP Socializer - PRO version. Below are the sample options.</p>
        <p><a href="https://www.aakashweb.com/wordpress-plugins/wp-socializer/?utm_source=admin&utm_medium=post_settings&utm_campaign=wpsr-pro" target="_blank" class="button button-primary">Upgrade to WP Socializer - PRO</a></p>
        </div>';

        $form->label( __( 'Twitter tweet text', 'wp-socializer' ) );
        $form->field( 'textarea', array(
            'value' => '{title} - {url} {twitter-username}',
            'class' => 'widefat',
            'readonly' => 'readonly'
        ));
        $form->end();

        $form->label( __( 'Share URL for this post', 'wp-socializer' ) );
        $form->field( 'text', array(
            'value' => get_the_permalink(),
            'class' => 'widefat',
            'readonly' => 'readonly',
            'helper' => 'Set a different URL to share for this post'
        ));
        $form->end();

        $form->label( __( 'Share title for this post', 'wp-socializer' ) );
        $form->field( 'text', array(
            'value' => $post->post_title,
            'class' => 'widefat',
            'readonly' => 'readonly',
            'helper' => 'Set a different title to share for this post',
        ));
        $form->end();

        $form->label( __( 'Share short URL for this post', 'wp-socializer' ) );
        $form->field( 'text', array(
            'value' => wp_get_shortlink( $post->ID ),
            'class' => 'widefat',
            'readonly' => 'readonly',
            'helper' => 'Set a different short URL to share for this post'
        ));
        $form->end();

        $form->label( __( 'Always use Short URL to share this post instead of full URL', 'wp-socializer' ) );
        $form->field( 'select', array(
            'type' => 'number',
            'value' => 'no',
            'disabled' => 'disabled',
            'list' => array(
                'no' => 'No',
                'yes' => 'Yes'
            )
        ));
        $form->end();

        $form->build( 'wpsr' );

        echo '</div>';

    }

    public static function save_post( $post_id, $post ){

        if ( !isset( $_POST[ '_wpsr_ps_nonce' ] ) || !wp_verify_nonce( $_POST[ '_wpsr_ps_nonce' ], 'wpsr_post_settings_nonce' ) ) {
            return $post_id;
        }

        $post_type = get_post_type_object( $post->post_type );

        if ( !current_user_can( $post_type->cap->edit_post, $post_id ) ) {
            return $post_id;
        }

        if ( defined( 'DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
            return $post_id;
        }

        $settings = WPSR_Options::post_settings();
        $new_settings = array();

        foreach( $settings as $name => $options ){
            if( isset( $_POST[ $name ] ) ) {
                $new_settings[ $name ] = sanitize_text_field( $_POST[ $name ] );
            }
        }

        update_post_meta( $post_id, 'wpsr_post_settings', $new_settings );

    }

}

WPSR_Post_Settings::init();

?>