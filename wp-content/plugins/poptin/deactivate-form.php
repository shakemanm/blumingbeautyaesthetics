<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<style>
    .poptin-hidden {
        overflow: hidden;
    }
    .poptin-popup-overlay .poptin-internal-message {
        margin: 3px 0 3px 22px;
        display: none;
    }
    .poptin-reason-input {
        margin: 3px 0 3px 22px;
        display: none;
    }
    .poptin-reason-input input[type="text"] {
        width: 100%;
        display: block;
    }
    .poptin-popup-overlay {
        background: rgba(0, 0, 0, .8);
        position: fixed;
        top: 0;
        left: 0;
        height: 100%;
        width: 100%;
        z-index: 1000;
        overflow: auto;
        visibility: hidden;
        opacity: 0;
        transition: opacity 0.3s ease-in-out :
    }
    .poptin-popup-overlay.poptin-active {
        opacity: 1;
        visibility: visible;
    }
    .poptin-serveypanel {
        width: 600px;
        background: #fff;
        margin: 65px auto 0;
    }
    .poptin-popup-header {
        background: #f1f1f1;
        padding: 20px;
        border-bottom: 1px solid #ccc;
    }
    .poptin-popup-header h2 {
        margin: 0;
    }
    .poptin-popup-body {
        padding: 10px 20px;
    }
    .poptin-popup-footer {
        background: #f9f3f3;
        padding: 10px 20px;
        border-top: 1px solid #ccc;
    }
    .poptin-popup-footer:after {
        content: "";
        display: table;
        clear: both;
    }
    .action-btns {
        float: right;
    }
    .poptin-anonymous {
        display: none;
    }
    .attention, .error-message {
        color: red;
        font-weight: 600;
        display: none;
    }
    .poptin-spinner {
        display: none;
    }
    .poptin-spinner img {
        margin-top: 3px;
    }
    .poptin-hidden-input {
        padding: 10px 0 0;
        display: none;
    }
    .poptin-hidden-input input[type='text'] {
        padding: 0 10px;
        width: 100%;
        height: 26px;
        line-height: 26px;
    }
    .poptin--popup-overlay textarea {
        padding: 10px;
        width: 100%;
        height: 100px;
        margin: 0 0 15px 0;
    }
    span.poptin-error-message {
        color: #dd0000;
        font-weight: 600;
    }
    .poptin-popup-body h3 {
        line-height: 24px;
    }
    .poptin-popup-body textarea {
        width: 100%;
        height: 80px;
    }
    .poptin--popup-overlay .form-control input {
        width: 100%;
        margin: 0 0 15px 0;
    }
    .poptin-serveypanel .form-control input {
        width: 100%;
        margin: 0 0 15px 0;
    }
</style>
<!-- modal for plugin deactivation popup -->
<div class="poptin-popup-overlay">
    <div class="poptin-serveypanel">
        <!-- form start -->
        <form action="#" method="post" id="poptin-deactivate-form">
            <div class="poptin-popup-header">
                <h2><?php esc_html_e('Quick feedback about Poptin', 'poptin'); ?> 🙏</h2>
            </div>
            <div class="poptin-popup-body">
                <h3><?php esc_html_e('Your feedback will help us improve the product, please tell us why did you decide to deactivate Poptin :)', 'poptin'); ?></h3>
                <div class="form-control">
                    <input type="email" value="<?php echo get_option( 'admin_email' ) ?>" placeholder="<?php echo _e("Email address", 'poptin') ?>" id="poptin-deactivation-email_id">
                </div>
                <div class="form-control">
                    <label></label>
                    <textarea placeholder="<?php esc_html_e("Your comment", 'poptin') ?>" id="poptin-deactivation-comment"></textarea>
                </div>
            </div>
            <div class="poptin-popup-footer">
                <label class="poptin-anonymous">
                    <input type="checkbox"/><?php esc_html_e('Anonymous feedback', 'poptin'); ?>
                </label>
                <input type="button" class="button button-secondary button-skip poptin-popup-skip-feedback" value="Skip &amp; Deactivate">
                <div class="action-btns">
                    <span class="poptin-spinner"><img src="<?php echo esc_url(admin_url('/images/spinner.gif')); ?>" alt=""></span>
                    <input type="submit" class="button button-secondary button-deactivate poptin-popup-allow-deactivate" value="Submit &amp; Deactivate" disabled="disabled">
                    <a href="#" class="button button-primary poptin-popup-button-close"><?php esc_html_e('Cancel', 'poptin'); ?></a>
                </div>
            </div>
        </form>
        <!-- form end -->
    </div>
</div>
<script>
    (function ($) {
        $(function () {
            var poptinPluginSlug = 'poptin';
            // Code to fire when the DOM is ready.
            $(document).on('click', 'tr[data-slug="' + poptinPluginSlug + '"] .deactivate', function (e) {
                e.preventDefault();
                $('.poptin-popup-overlay').addClass('poptin-active');
                $('body').addClass('poptin-hidden');
            });
            $(document).on('click', '.poptin-popup-button-close', function () {
                close_popup();
            });
            $(document).on('click', ".poptin-serveypanel,tr[data-slug='" + poptinPluginSlug + "'] .deactivate", function (e) {
                e.stopPropagation();
            });
            $(document).on('click', function () {
                close_popup();
            });
            $('.poptin-reason label').on('click', function () {
                $(".poptin-hidden-input").hide();
                jQuery(".poptin-error-message").remove();
                if ($(this).find('input[type="radio"]').is(':checked')) {
                    $(this).closest("li").find('.poptin-hidden-input').show();
                }
            });
            $(document).on("keyup", "#poptin-deactivation-comment", function(){
                if($.trim($(this).val()) == "") {
                    $(".poptin-popup-allow-deactivate").attr("disabled", true);
                } else {
                    $(".poptin-popup-allow-deactivate").attr("disabled", false);
                }
            });
            $('input[type="radio"][name="poptin-selected-reason"]').on('click', function (event) {
                $(".poptin-popup-allow-deactivate").removeAttr('disabled');
            });
            $(document).on('submit', '#poptin-deactivate-form', function (event) {
                event.preventDefault();
                _reason = "";
                if(jQuery.trim(jQuery("#poptin-deactivation-comment").val()) == "") {
                    jQuery("#alt_plugin").after("<span class='poptin-error-message'>Please provide your feedback</span>");
                    return false;
                } else {
                    _reason = jQuery.trim(jQuery("#poptin-deactivation-comment").val());
                }
                jQuery('[name="poptin-selected-reason"]:checked').val();
                var email_id = jQuery.trim(jQuery("#poptin-deactivation-email_id").val());
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'poptin_plugin_deactivate',
                        reason: _reason,
                        email_id: email_id,
                        nonce: '<?php echo esc_attr(wp_create_nonce("poptin_deactivate_nonce")) ?>'
                    },
                    beforeSend: function () {
                        $(".poptin-spinner").show();
                        $(".poptin-popup-allow-deactivate").attr("disabled", "disabled");
                    }
                }).done(function () {
                    $(".poptin-spinner").hide();
                    $(".poptin-popup-allow-deactivate").removeAttr("disabled");
                    window.location.href = $("tr[data-slug='" + poptinPluginSlug + "'] .deactivate a").attr('href');
                });
            });
            $('.poptin-popup-skip-feedback').on('click', function (e) {
                window.location.href = $("tr[data-slug='" + poptinPluginSlug + "'] .deactivate a").attr('href');
            });
            function close_popup() {
                $('.poptin-popup-overlay').removeClass('poptin-active');
                $('#poptin-deactivate-form').trigger("reset");
                $(".poptin-popup-allow-deactivate").attr('disabled', 'disabled');
                $(".poptin-reason-input").hide();
                $('body').removeClass('poptin-hidden');
                $('.message.error-message').hide();
            }
        });
    })(jQuery); // This invokes the function above and allows us to use '$' in place of 'jQuery' in our code.
</script>
