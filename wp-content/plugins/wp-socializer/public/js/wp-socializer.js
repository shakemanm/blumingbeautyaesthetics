window.wpsr_helpers = {
    addClass: function( ele, className ){
        if ( ele.classList )
          ele.classList.add( className );
        else
          ele.className += ' ' + className;
    },
    
    removeClass: function( ele, className ){
        if (ele.classList)
            ele.classList.remove(className);
        else
            ele.className = ele.className.replace(new RegExp('(^|\\b)' + className.split(' ').join('|') + '(\\b|$)', 'gi'), ' ');
    },
    
    popup_window: function( url, target, w, h ){
        var left = ( screen.width/2 )-( w/2 );
        var top = ( screen.height/2 )-( h/2 );
        return window.open( url, target, 'toolbar=no,location=no,menubar=no,scrollbars=yes,width='+w+',height='+h+',top='+top+',left='+left );
    },
    
    offset: function( el ){
        var rect = el.getBoundingClientRect();
        return {
            top: rect.top + document.body.scrollTop,
            left: rect.left + document.body.scrollLeft
        }
    },

    ajax: function( ajax_url, method, send, callback, props ){
        
        var request = new XMLHttpRequest();
        request.props = props;
        request.open( method, ajax_url, true );
        
        if ( method == 'POST' )
            request.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8' );
        
        request.onreadystatechange = function(){
            if ( request.readyState == 4 && request.status == 200 ){
                return callback( request );
            }
        };
        
        request.send( send );
        
    },
    
    format_num: function( num ){
        
        if( num < 1000 )
            return num;
        
        var suffixes = ['k', 'm', 'b', 't' ];
        var final_no = num;

        for( var i=0; i< suffixes.length; i++ ){
            num = num/1000;
            
            if( num > 1000 ){
                continue;
            }else{
                final_no = (Math.round( num*100 )/100) + suffixes[i];
                break;
            }
        }
        
        return final_no;
        
    },
    
    is_mobile: function(){
        return /Mobi|Android/i.test(navigator.userAgent);
    },

    open_popup: function(id){
        var pp_overlay = document.getElementById(id);
        if( !pp_overlay ){
            return;
        }
        var that = this;
        this.removeClass(pp_overlay, 'wpsr-pp-closed');
        this.addClass(pp_overlay, 'wpsr-pp-opened');
        this.removeClass(pp_overlay, 'wpsr-pp-slide-up');
        setTimeout(function(){
            that.addClass(pp_overlay, 'wpsr-pp-slide-down');
        }, 10);
    },

    close_popup: function(id){
        var pp_overlay = document.getElementById(id);
        if( !pp_overlay ){
            return;
        }
        var that = this;
        this.removeClass(pp_overlay, 'wpsr-pp-slide-down');
        this.addClass(pp_overlay, 'wpsr-pp-slide-up');
        setTimeout(function(){
            that.removeClass(pp_overlay, 'wpsr-pp-opened');
            that.addClass(pp_overlay, 'wpsr-pp-closed');
        }, 250);
    },

    copy: function(str){
        var el = document.createElement('textarea');
        el.value = str;
        el.setAttribute('readonly', '');
        el.style.position = 'absolute';
        el.style.left = '-9999px';
        document.body.appendChild(el);
        el.select();
        document.execCommand('copy');
        document.body.removeChild(el);
    }

};

document.addEventListener( 'DOMContentLoaded', function(){
    
    // Class names
    var hide_class = 'wpsr-hide';
    var closed_class = 'wpsr-closed';

    // Socializer links
    var scr_links = document.querySelectorAll( '.socializer.sr-popup a' );

    for( i = 0; i < scr_links.length; i++ ){
        var link = scr_links[i];
        link.addEventListener( 'click', function(e){
            var href = this.getAttribute( 'href' );
            var btn_id = this.getAttribute( 'data-id' );
            if( btn_id == 'comments' ){
                this.setAttribute('target', '_self');
                return;
            }
            if( !( href == '#' || this.hasAttribute( 'onclick' ) || href == null ) ){
                wpsr_helpers.popup_window( href, '_blank', 1024, 576 );
            }
            e.preventDefault();
        });
    }
    
    // Change share URL if device is mobile
    if(wpsr_helpers.is_mobile()){
        var mobile_links = document.querySelectorAll( '.socializer a[data-mobile]' );
        for( i = 0; i < mobile_links.length; i++ ){
            var link = mobile_links[i];
            var mobile_url = link.getAttribute( 'data-mobile' );
            link.setAttribute( 'href', mobile_url );
        }
    }

    // Sharebar
    var the_sb = document.querySelector( '.wpsr-sharebar' );
    
    if( the_sb ){

        var icons = the_sb.querySelector( '.socializer' );

        the_sb.sm_action_call = function(ele){
            wpsr_helpers.removeClass( this, 'wpsr-sb-vl' );
            wpsr_helpers.addClass( this, 'wpsr-sb-hl' );
            
            wpsr_helpers.removeClass( icons, 'sr-vertical' );
            wpsr_helpers.addClass( icons, 'sr-horizontal' );
            wpsr_helpers.addClass( icons, 'sr-fluid' );
        }

        the_sb.lg_action_call = function(){
            wpsr_helpers.addClass( this, 'wpsr-sb-vl' );
            wpsr_helpers.removeClass( this, 'wpsr-sb-hl' );
            
            wpsr_helpers.addClass( icons, 'sr-vertical' );
            wpsr_helpers.removeClass( icons, 'sr-horizontal' );
            wpsr_helpers.removeClass( icons, 'sr-fluid' );
        }

        var sb_resize = function(){
            stick_sb = document.querySelector( '.wpsr-sb-vl-scontent' );
            if( stick_sb ){
                stick = stick_sb.getAttribute( 'data-stick-to' );
                stick_ele = document.querySelector( stick );
                if( stick_ele ){
                    stick_offset = wpsr_helpers.offset( stick_ele );
                    stick_sb.style.left = stick_offset.left + 'px';
                }
            }

        }
        
        sb_resize();
        window.addEventListener( 'resize', sb_resize );

    }
    
    // Text sharebar
    tsb = document.querySelector( '.wpsr-text-sb' );
    
    if( tsb ){
        
        window.wpsr_tsb = {
            stext: '',
            startx: 0,
            starty: 0
        };
        
        var tsb_attr = {
            ptitle: tsb.getAttribute( 'data-title' ),
            purl: tsb.getAttribute( 'data-url' ),
            psurl: tsb.getAttribute( 'data-surl' ),
            ptuname: tsb.getAttribute( 'data-tuname' ),
            cnt_sel: tsb.getAttribute( 'data-content' ),
            word_count: tsb.getAttribute( 'data-tcount' ),
            comments_section: tsb.getAttribute( 'data-comments-section' ),
            raw_url: tsb.getAttribute( 'data-url' ),
        };
        
        var get_selection_text = function() {
            var text = '';
            if( window.getSelection ){
                text = window.getSelection().toString();
            }else if( document.selection && document.selection.type != 'Control' ){
                text = document.selection.createRange().text;
            }
            return text;
        };
        
        var tsb_show = function( x, y ){
            tsb.style.left = x + 'px';
            tsb.style.top = y + 'px';
            wpsr_helpers.addClass( tsb, 'wpsr-tsb-active' );
        };
        
        var tsb_hide = function(){
            wpsr_helpers.removeClass( tsb, 'wpsr-tsb-active' );
        };
        
        var sel_link_text = function(){
            var sel_text = wpsr_tsb.stext;
            var wcount = parseInt( tsb_attr.word_count );
            sel_text = sel_text.replace(/[^\x00-\x7F]/g, '');

            if( wcount == 0 ){
                return sel_text;
            }else{
                return sel_text.split( ' ' ).slice( 0, wcount ).join( ' ' );
            }
        };
        
        var replace_link = function( link ){
            var to_replace = {
                '{title}': escape(tsb_attr.ptitle),
                '{url}': tsb_attr.purl,
                '{s-url}': tsb_attr.psurl,
                '{twitter-username}': tsb_attr.ptuname,
                '{excerpt}': escape(sel_link_text()),
                '{comments-section}': tsb_attr.comments_section,
                '{raw-url}': tsb_attr.raw_url
            };
            
            for( var key in to_replace ){
                if( to_replace.hasOwnProperty( key ) ){
                    link = link.replace( RegExp( key, "g" ), to_replace[ key ] );
                }
            }
            
            return link;
            
        }
        
        if( tsb_attr.cnt_sel != '' ){
            
            var tsb_cnt_sel = tsb_attr.cnt_sel.replace( /[\[\]<>"'/\\=&%]/g,'' );
            var tsb_content = document.querySelectorAll( tsb_cnt_sel );
            
            for( var i = 0; i < tsb_content.length; i++ ){
                
                var content = tsb_content[i];
                
                content.addEventListener( 'mousedown', function(e){
                    wpsr_tsb.startx = e.pageX;
                    wpsr_tsb.starty = e.pageY;
                });
                
                content.addEventListener( 'mouseup', function(e){
                    var sel_text = get_selection_text();
                    
                    if( sel_text != '' ){
                        
                        tsb_x = ( e.pageX + parseInt( wpsr_tsb.startx ) )/2;
                        tsb_y = Math.min( wpsr_tsb.starty, e.pageY );
                        
                        if( sel_text != wpsr_tsb.stext ){
                            tsb_show( tsb_x, tsb_y );
                            wpsr_tsb.stext = sel_text;
                        }else{
                            tsb_hide();
                        }
                        
                    }else{
                        
                        tsb_hide();
                        
                    }
                });
            }
        }
        
        document.body.addEventListener( 'mousedown', function(e){
            tsb_hide();
        });
        
        tsb.addEventListener( 'mousedown', function(e){
            e.stopPropagation();
        });
        
        var atags = tsb.querySelectorAll( 'a' );
        for( var i = 0; i < atags.length; i++ ){
            var atag = atags[i];
            atag.addEventListener( 'click', function(e){
                var alink = this.getAttribute( 'data-link' );
                var aid = this.getAttribute( 'data-id' );
                
                if( aid == 'comments' ){
                    this.setAttribute('target', '_self');
                    this.setAttribute('href', replace_link( alink ));
                    return;
                }

                if( alink != '#' ){
                    rep_link = replace_link( alink );
                    wpsr_helpers.popup_window( rep_link, '_blank', 1024, 576 );
                }
                
                e.preventDefault();
            });
        }
        
    }

    // Respond to screen size
    var wpsr = document.querySelectorAll( '.wp-socializer' );
    if( wpsr.length > 0 ){
        
        [ 'resize', 'load' ].forEach(function(e){
            window.addEventListener(e, function(){
                for( var i = 0; i < wpsr.length; i++ ){

                    var wpsr_ele = wpsr[ i ];
                    var lg_action = wpsr_ele.getAttribute( 'data-lg-action' );
                    var sm_action = wpsr_ele.getAttribute( 'data-sm-action' );
                    var sm_width = wpsr_ele.getAttribute( 'data-sm-width' );
                    var current_action = (window.innerWidth <= sm_width) ? sm_action : lg_action;

                    if(current_action == 'close'){
                        wpsr_helpers.addClass(wpsr_ele, closed_class);
                    }else{
                        wpsr_helpers.removeClass(wpsr_ele, closed_class);
                    }
                    if(current_action == 'hide'){
                        wpsr_helpers.addClass(wpsr_ele, hide_class);
                    }else{
                        wpsr_helpers.removeClass(wpsr_ele, hide_class);
                    }

                    if(typeof wpsr_ele.sm_action_call === 'function' && current_action == sm_action){
                        wpsr_ele.sm_action_call();
                    }

                    if(typeof wpsr_ele.lg_action_call === 'function' && current_action == lg_action){
                        wpsr_ele.lg_action_call();
                    }

                }
            });
        });
        
    }
    
    // Close button event
    var close_btns = document.querySelectorAll( '.wpsr-close-btn' );
    if( close_btns.length > 0 ){
        for( i = 0; i < close_btns.length; i++ ){
            var close_btn = close_btns[i];

            close_btn.addEventListener( 'click', function(){
                var parent = this.parentNode;
                if( parent.classList.contains( closed_class ) ){
                    wpsr_helpers.removeClass( parent, closed_class );
                }else{
                    wpsr_helpers.addClass( parent, closed_class );
                }
            });

        }

    }

    // Ajax share count
    if( typeof wp_socializer !== 'undefined' ){
        
        var share_count = document.querySelectorAll( '[data-wpsrs]' );
        
        if( share_count.length > 0 ){
            
            var data = {};
            var ajax_url = wp_socializer.ajax_url + '?action=wpsr_share_count';
            
            for( i = 0; i < share_count.length; i++ ){
                var sEle = share_count[ i ];
                var url = sEle.getAttribute( 'data-wpsrs' );
                var services = sEle.getAttribute( 'data-wpsrs-svcs' ).split( ',' );
                
                if( !( url in data ) ){
                    data[ url ] = [];
                }
                
                for( j = 0; j < services.length; j++ ){
                    if( data[ url ].indexOf( services[j] ) === -1 ){
                        data[ url ].push( services[j] );
                    }
                }
                
            }
            
            var ajax_res = function( req ){
                
                var out = JSON.parse( req.responseText );
                var ph = document.querySelectorAll( '[data-wpsrs="' + req.props.forURL + '"]' );
                
                for( i = 0; i < ph.length; i++ ){
                    var phEle = ph[i];
                    var services = phEle.getAttribute( 'data-wpsrs-svcs' ).split( ',' );
                    var count = 0;
                    for( j = 0; j < services.length; j++ ){
                        var svc = services[j];
                        if( svc in out ){
                            count += parseInt( out[ svc ] ) || 0;
                        }
                    }
                    if( count > 0 ){
                        phEle.innerHTML = wpsr_helpers.format_num( count );
                    }
                }
                
            }
            
            for( var url in data ){
                if( data.hasOwnProperty( url ) ){
                    send_data = {
                        'url': url,
                        'services': data[ url ]
                    };
                    to_send = 'data=' + JSON.stringify( send_data );
                    
                    wpsr_helpers.ajax( ajax_url, 'POST', to_send, ajax_res, { forURL: url } );
                    
                }
            }
            
        }
        
    }

    // Popup events
    var pp_close_btns = document.querySelectorAll('.wpsr-pp-close');
    if( pp_close_btns.length > 0 ){
        for( i = 0; i < pp_close_btns.length; i++ ){
            var pp_close_btn = pp_close_btns[i];
            pp_close_btn.addEventListener( 'click', function(e){
                e.preventDefault();
                var id = this.getAttribute('data-id');
                wpsr_helpers.close_popup(id);
            });
        }
    }

    // Share menu
    window.wpsr_share_menu = {
        set_data: function(metadata){
            try{
                this.data = JSON.parse(metadata);
            }catch(e){
                this.data = {};
                console.log(e);
            }
        },
        get_param: function(param){
            var defaults = {
                'url': document.location.href,
                'title': document.title,
                'short-url': document.location.href
            };
            this.data = (typeof this.data === 'undefined') ? defaults : this.data;
            if(param in this.data){
                return this.data[param];
            }else{
                return (param in defaults) ? defaults[param] : '';
            }
        },
        process_link: function(link){
            var pattern = /\{(.+?)\}/g;
            var current;
            while(current = pattern.exec(link)){
                var param_val = this.get_param(current[1]);
                link = link.replaceAll('{' + current[1] + '}', encodeURI(param_val));
            }
            return link;
        }
    }

    var sm_btns = document.querySelectorAll('.sr-share-menu a');
    if( sm_btns.length > 0 ){
        for( i = 0; i < sm_btns.length; i++ ){
            var sm_btn = sm_btns[i];
            sm_btn.addEventListener( 'click', function(){
                var metadata = this.getAttribute( 'data-metadata' );
                wpsr_share_menu.set_data(metadata);
                wpsr_helpers.open_popup('wpsr-share-menu');
            });
        }
    }

    var sm_links = document.querySelectorAll('.wpsr-sm-link');
    if( sm_links.length > 0 ){
        for( i = 0; i < sm_links.length; i++ ){
            var sm_link = sm_links[i];
            sm_link.addEventListener( 'click', function(e){
                e.preventDefault();

                var link_raw = (wpsr_helpers.is_mobile() && this.getAttribute('data-m')) ? this.getAttribute('data-m') : this.getAttribute('data-d');
                var link = wpsr_share_menu.process_link(atob(link_raw));

                if(this.classList.contains('wpsr-sml-shortlink')){
                    socializer_shortlink(e, false, link);
                    return;
                }

                if(link.indexOf('http') == 0){
                    wpsr_helpers.popup_window(link, '_blank', 1024, 576);
                }else{
                    window.location.href = link;
                }
            });
        }
    }

    var sl_copy_btn = document.getElementById('wpsr-sl-copy-btn');
    if( sl_copy_btn ){
        sl_copy_btn.addEventListener( 'click', function(e){
            e.preventDefault();
            wpsr_helpers.copy(document.getElementById('wpsr-short-link-url').value);
            this.innerText = this.getAttribute('data-d');
            var that = this;
            setTimeout(function(){
                that.innerText = that.getAttribute('data-c');
            }, 3000);
        });
    }

});

function socializer_addbookmark( e ){
    var ua = navigator.userAgent.toLowerCase();
    var isMac = (ua.indexOf('mac') != -1), str = '';
    e.preventDefault();
    str = (isMac ? 'Command/Cmd' : 'CTRL') + ' + D';
    alert('Press ' + str + ' to bookmark this page');
}

function socializer_shortlink(e, link_ele, link_raw){
    if( typeof e.preventDefault === 'function' ){
        e.preventDefault();
    }
    var link = link_ele ? link_ele.getAttribute( 'href' ) : link_raw;
    wpsr_helpers.open_popup('wpsr-short-link');
    document.getElementById('wpsr-short-link-url').value = link;
}