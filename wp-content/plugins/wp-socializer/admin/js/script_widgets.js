(function($){
jQuery(document).ready(function(){
    
    var init = function(){
        loadColorPicker();
        
        if(window.location.hash) {
            var hash = window.location.hash.substring(1);
            if( hash.startsWith( 'wp-socializer' ) ){
                var widget_name = hash.split(':')[1];
                $widget = $( '#available-widgets [id*="wpsr_' + widget_name + '_widget"]' );
                $(window).scrollTop( $widget.offset().top - 300 );
                $widget.addClass( 'wpsr_widget_highlight' );
            }
        }
        
    }
    
    var loadColorPicker = function(){
        if( $.fn.wpColorPicker ){
            $( '.wpsr-color-picker' ).wpColorPicker();
        }
    }
    
    $(document).ajaxComplete(function(){
        loadColorPicker();
    });
    
    $( document ).on( 'click', '.wpsr_ppe_fb_open', function(e){
        e.preventDefault();
        
        if( wpsr_ppe_ajax ){
            
            wtmpl_cnt_id = $( this ).attr( 'data-wtmpl-cnt-id' );
            wtmpl_cnt = $( '#' + wtmpl_cnt_id ).val();
            wtmpl_prev_id = $( this ).attr( 'data-wtmpl-prev-id' );
            qstring = 'action=wpsr_follow_icons_editor&cnt_id=' + wtmpl_cnt_id + '&prev_id=' + wtmpl_prev_id;
            
            wpsr_ipopup_show( wpsr_ppe_ajax + '?' + qstring, '800px', '80%' );
            
            $('#wpsr_ipopup_wrap iframe').on('load', function(){
                var content_window = $('#wpsr_ipopup_wrap iframe')[0].contentWindow;
                content_window.postMessage({
                    'type': 'fb_editor_msg',
                    'content': wtmpl_cnt
                });
            });

        }
    });
    
    // Init
    init();
    
});
})( jQuery );