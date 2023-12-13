<?php
/**
 * Twitter widget
 */

defined( 'ABSPATH' ) || exit;

class WPSR_Twitter_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'WPSR_Twitter_Widget',
            'X timeline widget',
            array( 'description' => __( 'Display your X/twitter timeline using this widget.', 'wp-socializer' ), ),
            array( 'width' => 500, 'height' => 500 )
        );
    }

    public static function init(){
        add_filter( 'wpsr_register_widget', array( __class__, 'register' ) );
        add_filter( 'wpsr_register_admin_page', array( __class__, 'register_admin_page' ) );
    }

    public static function register( $widgets ){

        $widgets[ 'twitter' ] = array(
            'name' => __( 'X widget', 'wp-socializer' ),
            'widget_class' => __class__
        );

        return $widgets;

    }

    public static function register_admin_page( $pages ){
        
        $pages[ 'twitter-widget' ] = array(
            'name' => __( 'X timeline widget', 'wp-socializer' ),
            'banner' => WPSR_ADMIN_URL . '/images/banners/twitter-widget.svg',
            'link' => admin_url('widgets.php#wp-socializer:twitter'),
            'category' => 'widget',
            'type' => 'widget',
            'description' => __( 'Add a widget to sidebar/footer to display your X posts.', 'wp-socializer' )
        );

        return $pages;

    }

    public static function defaults(){

        return array(
            'title' => '',
            'twitter_widget_url' => '',
            'twitter_widget_height' => '600',
            'twitter_widget_theme' => 'light'
        );

    }

    function widget( $args, $instance ){

        $instance = WPSR_Lists::set_defaults( $instance, self::defaults() );

        WPSR_Widgets::before_widget( $args, $instance );

        echo '<a class="twitter-timeline" data-height="' . esc_attr( $instance[ 'twitter_widget_height' ] ) . '" data-theme="' . esc_attr( $instance[ 'twitter_widget_theme' ] ) . '" href="' . esc_attr( $instance[ 'twitter_widget_url' ] ) . '">Twitter</a>';
        
        WPSR_Includes::add_active_includes( array( 'twitter_js' ) );

        WPSR_Widgets::after_widget( $args, $instance );

    }

    function form( $instance ){

        $instance = WPSR_Lists::set_defaults( $instance, self::defaults() );
        $fields = new WPSR_Widget_Form_Fields( $this, $instance );
        
        echo '<div class="wpsr_widget_wrap">';

        $fields->text( 'title', 'Title' );
        $fields->text( 'twitter_widget_url', 'Enter a twitter URL', array( 'placeholder' => 'Ex: https://twitter.com/vaakash' ) );

        echo '<h5>Examples:</h5>';
        echo '<ul>';
        echo '<li><code>List</code> - https://twitter.com/TwitterDev/lists/national-parks</li>';
        echo '<li><code>Profile</code> - https://twitter.com/TwitterDev</li>';
        echo '</ul>';

        $fields->number( 'twitter_widget_height', 'Height ( in pixels )' );
        $fields->select( 'twitter_widget_theme', 'Color scheme', array( 'light' => 'Light', 'dark' => 'Dark' ), array( 'class' => 'smallfat' ) );

        $fields->footer();

        echo '</div>';

    }

    function update( $new_instance, $old_instance ){

        if( !current_user_can( 'unfiltered_html' ) ) {
            $new_instance = wp_kses_post_deep( $new_instance );
        }

        return $new_instance;
    }

}

WPSR_Twitter_Widget::init();

?>