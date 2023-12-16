jQuery(document).ready(function ($) {
  jQuery('.where-is-my-id-inside-lb').on('click', function (e) {
    $('#oopsiewrongid').modal('hide');
    $('#whereIsMyId').modal();
  });

  function show_loader(el) {
    $(el).find('.text-content').hide();
    $(el).find('.loader').show();
  }

  function hide_loader(el) {
    $(el).find('.text-content').show();
    $(el).find('.loader').hide();
  }

  jQuery('.pp_signup_btn').on('click', function (e) {
    e.preventDefault();
    var email = $('#poptinRegisterEmail').val();
    if (!isEmail(email)) {
      e.preventDefault();
      $('#oopsiewrongemailid').fadeIn(500);

      $('#oopsiewrongemailid').delay(2500).fadeOut();
      $('#poptinRegisterEmail')
        .addClass('error')
        .delay(3000)
        .queue(function () {
          $(this).removeClass('error').dequeue();
        });

      return false;
    } else {
      var el = this;
      show_loader(el);
      jQuery.ajax({
        url: ajaxurl,
        dataType: 'JSON',
        method: 'POST',
        data: jQuery('#registration_form').serialize(),
        success: function (data) {
          hide_loader(el);
          if (data.success == true) {
            jQuery('.ppaccountmanager').fadeOut(300);
            jQuery('#customersWrap').fadeOut(300);
            jQuery('.poptinLogged').fadeIn(300);
            jQuery('.poptinLoggedBg').fadeIn(300);
            $('.goto_dashboard_button_pp_updatable').attr(
              'href',
              'admin.php?page=Poptin&poptin_logmein=true&after_registration=wordpress'
            );
            // window.open("admin.php?page=Poptin&poptin_logmein=true&after_registration=wordpress","_blank");
          } else {
            if (
              data.message === 'Registration failed. User already registered.'
            ) {
              jQuery('#lookfamiliar').modal();
            } else if ((data.message = 'The email has already been taken.')) {
              jQuery('#lookfamiliar').modal();
            } else {
              swal('Error', data.message, 'error');
            }
          }
        }
      });
    }
  });

  jQuery('.goto_dashboard_button_pp_updatable').on('click', function () {
    link = $(this);
    href = link.attr('href');
    setTimeout(function () {
      link.attr('href', href.replace('&after_registration=wordpress', ''));
    }, 1000);
  });

  jQuery('.dashboard_link').on('click', function () {
    href = $(this).data('target');
    window.open(href, '_blank');
  });

  jQuery(document).on('click', '.deactivate-poptin-confirm-yes', function () {
    var el = this;
    show_loader(el);
    jQuery.post(
      ajaxurl,
      {
        action: 'delete-id',
        data: { nonce: $('#ppFormIdDeactivate').val() }
      },
      function (status) {
        hide_loader(el);
        status = JSON.parse(status);
        if (status.success == true) {
          jQuery('#makingsure').modal('hide');
          jQuery('#byebyeModal').modal('show');
          $('.poptinLogged').hide();
          $('.poptinLoggedBg').hide();
          $('.ppaccountmanager').fadeIn('slow');
          $('#customersWrap').fadeIn('slow');
          $('.popotinLogin').show();
          $('.popotinRegister').hide();
        }
      }
    );
  });

  jQuery('.pplogout').on('click', function (e) {
    e.preventDefault();
    jQuery('#makingsure').modal('show');
  });

  jQuery('.poptinWalkthroughVideoTrigger').on('click', function (e) {
    e.preventDefault();
    jQuery('#poptinExplanatoryVideo').modal('show');
  });

  $('.ppLogin').on('click', function (e) {
    e.preventDefault();
    $('.popotinLogin').fadeIn('slow');
    $('.popotinRegister').hide();
    $('#poptinUserId').focus();
  });

  $('.ppRegister').on('click', function (e) {
    e.preventDefault();
    $('.popotinRegister').fadeIn('slow');
    $('.popotinLogin').hide();
    $('#poptinRegisterEmail').focus();
  });

  $('.ppFormLogin').on('submit', function (e) {
    e.preventDefault();
    var id = $('.ppFormLogin input[type="text"]').val();
    if (id.length != 13) {
      e.preventDefault();
      //   $('#oopsiewrongid').modal('show');
      $('#oopsiewrongid').fadeIn(500);

      $('#oopsiewrongid').delay(2500).fadeOut();
      $('#poptinUserId')
        .addClass('error')
        .delay(3000)
        .queue(function () {
          $(this).removeClass('error').dequeue();
        });
      return false;
    } else {
      var el = this;
      show_loader(el);
      $.post(
        ajaxurl,
        {
          data: { poptin_id: id, nonce: $('#ppFormIdRegister').val() },
          action: 'add-id'
        },
        function (status) {
          hide_loader(el);
          status = JSON.parse(status);
          if (status.success == true) {
            jQuery('.poptinLogged').fadeIn('slow');
            jQuery('.poptinLoggedBg').fadeIn('slow');
            jQuery('#customersWrap').hide();
            jQuery('.ppaccountmanager').hide();
            jQuery('.popotinLogin').hide();
            jQuery('.popotinRegister').hide();
            $('.goto_dashboard_button_pp_updatable').attr(
              'href',
              'https://app.popt.in/login'
            );
          }
        }
      );
    }
  });

  $('input').on('change', function (e) {
    let value = e.target.value;
    if (value) {
      $(this).addClass('active');
    } else {
      $(this).removeClass('active');
    }
  });
});

function isEmail(email) {
  var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  return regex.test(email);
}
