<?php
/**
 * Pinterest widget
 */

defined( 'ABSPATH' ) || exit;

class WPSR_Pinterest_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'WPSR_Pinterest_Widget',
            'Pinterest widget',
            array( 'description' => __( 'Display pinterest board, your profile pins.', 'wp-socializer' ), ),
            array( 'width' => 500, 'height' => 500 )
        );
    }

    public static function init(){
        add_filter( 'wpsr_register_widget', array( __class__, 'register' ) );
        add_filter( 'wpsr_register_admin_page', array( __class__, 'register_admin_page' ) );
    }

    public static function register( $widgets ){

        $widgets[ 'pinterest' ] = array(
            'name' => __( 'Pinterest widget', 'wp-socializer' ),
            'widget_class' => __class__
        );

        return $widgets;

    }

    public static function register_admin_page( $pages ){
        
        $pages[ 'pinterest-widget' ] = array(
            'name' => __( 'Pinterest widget', 'wp-socializer' ),
            'link' => admin_url('widgets.php#wp-socializer:pinterest'),
            'category' => 'widget',
            'type' => 'widget',
            'description' => __( 'Add a widget to sidebar/footer to display your Pinterest board, profile.', 'wp-socializer' )
        );

        return $pages;

    }

    public static function defaults(){

        return array(
            'title' => '',
            'pinterest_url' => '',
            'pinterest_widget_type' => 'profile',
            'pinterest_widget_height' => '500',
        );

    }

    function widget( $args, $instance ){

        $instance = WPSR_Lists::set_defaults( $instance, self::defaults() );

        WPSR_Widgets::before_widget( $args, $instance );

        $widget_types = array(
            'pin' => 'embedPin',
            'board' => 'embedBoard',
            'profile' => 'embedUser'
        );

        if( !array_key_exists( $instance[ 'pinterest_widget_type' ], $widget_types ) ){
            $do_widget = 'embedUser';
        }else{
            $do_widget = $widget_types[ $instance[ 'pinterest_widget_type' ] ];
        }

        $attrs = array(
            'data-pin-do' => $do_widget,
            'data-pin-scale-height' => $instance[ 'pinterest_widget_height' ],
            'href' => $instance[ 'pinterest_url' ]
        );

        if( $do_widget == 'embedPin' ){
            $attrs[ 'data-pin-width' ] = 'medium';
        }

        $attr_string = '';
        foreach( $attrs as $attr_name => $attr_val ){
            $attr_string .= $attr_name . '="' . esc_attr( $attr_val ) . '" ';
        }

        echo '<a ' . $attr_string . '></a>';

        WPSR_Includes::add_active_includes( array( 'pinterest_js' ) );

        WPSR_Widgets::after_widget( $args, $instance );

    }

    function form( $instance ){

        $instance = WPSR_Lists::set_defaults( $instance, self::defaults() );
        $fields = new WPSR_Widget_Form_Fields( $this, $instance );
        
        echo '<div class="wpsr_widget_wrap">';

        $fields->text( 'title', 'Title' );
        $fields->select( 'pinterest_widget_type', 'Widget type', array( 'profile' => 'Profile', 'board' => 'Board', 'pin' => 'Pin' ), array( 'class' => 'widefat' ) );
        $fields->text( 'pinterest_url', 'Enter a pinterest URL', array( 'placeholder' => 'Ex: https://www.pinterest.com/pinterest/' ) );

        echo '<h5>Examples:</h5>';
        echo '<ul>';
        echo '<li><code>Profile</code> - https://www.pinterest.com/pinterest/</li>';
        echo '<li><code>Board</code> - https://www.pinterest.com/pinterest/official-news/</li>';
        echo '<li><code>Pin</code> - https://www.pinterest.com/pin/99360735500167749/</li>';
        echo '</ul>';

        $fields->number( 'pinterest_widget_height', 'Height (in pixels)' );

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

WPSR_Pinterest_Widget::init();

?>