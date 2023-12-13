<?php
/**
  * Widgets admin page helpers
  *
  **/

defined( 'ABSPATH' ) || exit;

class WPSR_Widget_Form_Fields{
    
    public $obj;

    public $instance;

    function __construct( $widget_obj, $instance ){
        $this->obj = $widget_obj;
        $this->instance = $instance;
    }

    function heading( $text ){
        echo '<h3>' . esc_html( $text ) . '</h3>';
    }

    function text( $id, $name, $opts = array() ){
        
        $opts = WPSR_Lists::set_defaults( $opts, array(
            'class' => 'widefat',
            'helper' => '',
            'placeholder' => '',
            'custom' => ''
        ));
        
        $class = ( $opts[ 'class' ] == 'widefat' ) ? 'full_width' : '';

        echo '<p class="' . esc_attr( $class ) . '">';
        echo '<label for="' . esc_attr( $this->obj->get_field_id( $id ) ) . '">' . esc_html( $name ) . '</label>';
        echo WPSR_Form::field_html( 'text', array(
            'type' => 'text',
            'name' => $this->obj->get_field_name( $id ),
            'id' => $this->obj->get_field_id( $id ),
            'value' => $this->instance[ $id ],
            'class' => $opts[ 'class' ],
            'helper' => $opts[ 'helper' ],
            'placeholder' => $opts[ 'placeholder' ],
            'custom' => $opts[ 'custom' ]
        ));
        echo '</p>';
    }

    function select( $id, $name, $list, $opts = array() ){
        
        $opts = WPSR_Lists::set_defaults( $opts, array(
            'class' => 'widefat',
            'helper' => '',
            'placeholder' => '',
            'custom' => ''
        ));
        
        echo '<p>';
        echo '<label for="' . esc_attr( $this->obj->get_field_id( $id ) ) . '">' . esc_html( $name ) . '</label>';
        echo WPSR_Form::field_html( 'select', array(
            'name' => $this->obj->get_field_name( $id ),
            'id' => $this->obj->get_field_id( $id ),
            'value' => $this->instance[ $id ],
            'list' => $list,
            'class' => $opts[ 'class' ],
            'helper' => $opts[ 'helper' ],
            'placeholder' => $opts[ 'placeholder' ],
            'custom' => $opts[ 'custom' ]
        ));
        echo '</p>';
    }

    function number( $id, $name, $opts = array() ){
        
        $opts = WPSR_Lists::set_defaults( $opts, array(
            'class' => 'smallfat',
            'helper' => '',
            'placeholder' => '',
            'custom' => ''
        ));
        
        echo '<p>';
        echo '<label for="' . esc_attr( $this->obj->get_field_id( $id ) ) . '">' . esc_html( $name ) . '</label>';
        echo WPSR_Form::field_html( 'text', array(
            'name' => $this->obj->get_field_name( $id ),
            'id' => $this->obj->get_field_id( $id ),
            'value' => $this->instance[ $id ],
            'class' => $opts[ 'class' ],
            'helper' => $opts[ 'helper' ],
            'placeholder' => $opts[ 'placeholder' ],
            'type' => 'number',
            'custom' => $opts[ 'custom' ]
        ));
        echo '</p>';
    }

    function textarea( $id, $name, $opts = array() ){
        
        $opts = WPSR_Lists::set_defaults( $opts, array(
            'class' => 'widefat',
            'helper' => '',
            'placeholder' => '',
            'custom' => ''
        ));
        
        echo '<p>';
        echo '<label for="' . esc_attr( $this->obj->get_field_id( $id ) ) . '">' . esc_html( $name ) . '</label>';
        echo WPSR_Form::field_html( 'textarea', array(
            'name' => $this->obj->get_field_name( $id ),
            'id' => $this->obj->get_field_id( $id ),
            'value' => $this->instance[ $id ],
            'class' => $opts[ 'class' ],
            'helper' => $opts[ 'helper' ],
            'placeholder' => $opts[ 'placeholder' ],
            'custom' => $opts[ 'custom' ]
        ));
        echo '</p>';
    }

    function footer(){

        echo '<footer>';
        echo '<span class="wpsr_version">WP Socializer v' . WPSR_VERSION . '</span> | <a href="https://wordpress.org/support/plugin/wp-socializer/reviews/?rate=5#new-post" target="_blank">Rate this plugin</a>';
        echo '</footer>';

    }

}

?>