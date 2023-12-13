<?php
/**
  * Generates the HTML tag
  * 
  */

defined( 'ABSPATH' ) || exit;

class WPSR_HTML_Tag{

    public $tag = null;
    
    public $style = array();
    
    public $class = array();
    
    public $data = array();
    
    public $attrs = array();

    public function __construct( $tag, $default_class = '', $default_style = array() ){
        $this->tag = $tag;
        if( !empty( $default_class ) ){
            $this->class = explode( ' ', $default_class );
        }
        if( !empty( $default_style ) ){
            $this->style = $default_style;
        }
    }
    
    public function add_class( $class ){
        array_push( $this->class, $class );
    }
    
    public function add_style( $prop, $val ){
        $this->style[ $prop ] = $val;
    }
    
    public function add_data( $name, $val ){
        $this->data[ $name ] = $val;
    }

    public function add_attr( $name, $val ){
        $this->attrs[ $name ] = $val;
    }

    public function open(){
        $all_attrs = array();

        if( !empty( $this->class ) ){
            $all_attrs[ 'class' ] = implode( ' ', $this->class );
        }

        if( !empty( $this->data ) ){
            foreach( $this->data as $name => $val ){
                $all_attrs[ 'data-' . $name ] = $val;
            }
        }

        $all_attrs[ 'style' ] = self::build_style( $this->style );

        $all_attrs = array_merge( $all_attrs, $this->attrs );

        $attr_string = '';
        foreach( $all_attrs as $name => $val ){
            if( empty( $val ) ){
                continue;
            }
            $attr_string .= esc_attr( $name ) . '="' . esc_attr( $val ) . '" ';
        }

        return '<' . $this->tag . ' ' . trim( $attr_string ) . '>';
    }

    public function close(){
        return '</' . $this->tag . '>';
    }

    public static function build_style( $styles ){
        $style = '';
        if( !empty( $styles ) ){
            foreach( $styles as $prop => $val ){
                $style .= $prop . ':' . $val . ';';
            }
        }
        return $style;
    }

}

?>