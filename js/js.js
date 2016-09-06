$(document).ready( function () {
  $('[data-toggle="tooltip"]').tooltip(); 
  $('input:checkbox').change(function(){
    if($('#checkbox').is(":checked")){          
      $("#new").show();
      $("#select").hide();
      $("select[name = 'customer']").attr('required',false);
      $("select[name = 'customer']").attr('disabled',true);
      $("input[name = 'fname']").attr('required',true);
      $("input[name = 'mname']").attr('required',true);
      $("input[name = 'lname']").attr('required',true);
      $("textarea[name = 'address']").attr('required',true);
      $("input[name = 'contact']").attr('required',true);
    }else{
      $("#select").show();
      $("#new").hide();
      $("select[name = 'customer']").attr('required',true);
      $("select[name = 'customer']").attr('disabled',false);
      $("input[name = 'fname']").attr('required',false);
      $("input[name = 'mname']").attr('required',false);
      $("input[name = 'lname']").attr('required',false);
      $("textarea[name = 'address']").attr('required',false);
      $("input[name = 'contact']").attr('required',false);
    }
    if($('#specialrate').is(":checked")){          
      $("#asterisk").show();
      $("input[name = 'specialrate']").attr('required',true);
      $("input[name = 'specialrate']").attr('disabled',false);
      if($("input[name = 'amount']").val() != "" || $("input[name = 'duration']").val() != ""){
        showUser();
      }
    }else{
      $("#asterisk").hide();
      $("input[name = 'specialrate']").attr('required',false);
      $("input[name = 'specialrate']").attr('disabled',true);
      $("input[name = 'specialrate']").val("");
      if($("input[name = 'amount']").val() != "" || $("input[name = 'duration']").val() != ""){
        showUser();
      }
    }
  });
  $("#submitss").click(function(){
    if($("#psw").val() != $("#psw1").val()){
      alert("Password does not match");
      return false;
    }
  });
  $('.dropdown-toggle').click(function(){ 
    return false
  });
});

$(window).load(function(){
  $(".highcharts-contextmenu").hide();
});

//sticky thead
$(document).ready(function(){
  $('.table-fixed-header').fixedHeader();
  NProgress.start();
  $("#b-0").click(function() { NProgress.start(); });
  $("#b-40").click(function() { NProgress.set(0.4); });
  $("#b-inc").click(function() { NProgress.inc(); });
  setTimeout(function() { NProgress.done(); $("#loader").css('display','none'); $("#tohide").css('display','block'); }, 1300);
  $("#b-100").click(function() { NProgress.done(); });
});

(function ($) {

  $.fn.fixedHeader = function (options) {
    var config = {
        topOffset: 50
        //bgColor: 'white'
      };
    if (options) {
      $.extend(config, options);
    }

    return this.each(function () {
      var o = $(this);

      var $win = $(window);
      var $head = $('thead.header', o);
      var isFixed = 0;
      var headTop = $head.length && $head.offset().top - config.topOffset;

      function processScroll() {
        if (!o.is(':visible')) {
          return;
        }
        if ($('thead.header-copy').size()) {
          $('thead.header-copy').width($('thead.header').width());
        }
        var i;
        var scrollTop = $win.scrollTop();
        var t = $head.length && $head.offset().top - config.topOffset;
        if (!isFixed && headTop !== t) {
          headTop = t;
        }
        if (scrollTop >= headTop && !isFixed) {
          isFixed = 1;
        } else if (scrollTop <= headTop && isFixed) {
          isFixed = 0;
        }
        isFixed ? $('thead.header-copy', o).offset({
          left: $head.offset().left
        }).removeClass('hide') : $('thead.header-copy', o).addClass('hide');
      }
      $win.on('scroll', processScroll);

      // hack sad times - holdover until rewrite for 2.1
      $head.on('click', function () {
        if (!isFixed) {
          setTimeout(function () {
            $win.scrollTop($win.scrollTop() - 47);
          }, 10);
        }
      });

      $head.clone().removeClass('header').addClass('header-copy header-fixed').appendTo(o);
      var header_width = $head.width();
      o.find('thead.header-copy').width(header_width);
      o.find('thead.header > tr:first > th').each(function (i, h) {
        var w = $(h).width();
        o.find('thead.header-copy> tr > th:eq(' + i + ')').width(w);
      });
      $head.css({
        margin: '0 auto',
        width: o.width(),
        'background-color': config.bgColor
      });
      processScroll();
    });
  };

})(jQuery);
$(document).ready(function(){
  // jQuery plugin to prevent double submission of forms
  jQuery.fn.preventDoubleSubmission = function() {
    $(this).on('submit',function(e){
      var $form = $(this);

      if ($form.data('submitted') === true) {
        // Previously submitted - don't submit again
        e.preventDefault();
      } else {
        // Mark it so that the next submit can be ignored
        $form.data('submitted', true);
      }
    });

    // Keep chainability
    return this;
  };
  $('form').preventDoubleSubmission();
});