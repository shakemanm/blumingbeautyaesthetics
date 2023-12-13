(function($){
jQuery(document).ready(function(){

    var init = function(){
        tabs();
    }

    var tabs = function(){

        $('.wpsr_ps_tab_list li:first-child').addClass('active');
        $('.wpsr_ps_tab_wrap > div:first-child').addClass('active');

        $('.wpsr_ps_tab_list a').click(function(e){

            e.preventDefault();

            var id = $(this).attr('href').substr(1);

            var $tab_list = $(this).closest('.wpsr_ps_tab_list_wrap');
            var $tab_wrap = $tab_list.next('.wpsr_ps_tab_wrap');

            $tab_wrap.children('div').removeClass('active');
            $tab = $tab_wrap.find('[data-wpsr-tab="' + id + '"]');
            $tab.addClass('active');

            $tab_list.find('li').removeClass('active');
            $(this).parent().addClass('active');

        });

    }

    init();

});
})( jQuery );