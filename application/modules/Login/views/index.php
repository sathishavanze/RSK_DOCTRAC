<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <link rel="apple-touch-icon" sizes="76x76" href="assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="<?php echo base_url(); ?>assets/img/favicon.png">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <title>Login | Doctrac</title>
  <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
  <link href="<?php echo base_url(); ?>assets/css/material-dashboard.min.css?<?php echo md5(time()); ?>" rel="stylesheet" />
  <link href="<?php echo base_url(); ?>assets/demo/demo.css" rel="stylesheet" />
  <link rel="stylesheet" type="text/css"  href="<?php echo base_url(); ?>assets/icon/css/ionicons.css" />
  <link href="<?php echo base_url(); ?>assets/css/icomoon.css" rel="stylesheet" />

  <style>
  .off-canvas-sidebar .navbar .navbar-collapse .navbar-nav .nav-item .nav-link{
        font-weight: 400 !important;
  }
  .card .card-footer{
    display: block !important;
        margin: 0 15px 0px !important;     
  }
    .navbar.navbar-transparent{
     background-color: transparent !important; 
     box-shadow: none;
     border-bottom: none !important; 
   }
   .navbar{
    background-color: transparent !important;
  }
  .card-header-default{
    background: #f44336 !important;
    box-shadow: 0 4px 20px 0px rgba(0, 0, 0, 0.14), 0 7px 10px -5px rgba(30, 74, 233, 0.4) !important;
  }
  .pr-10{
    padding-right:6px;
  }
  .borderleft{
border-left: 1px solid #ddd !important;
  }
  .borderright{
    border-right: 1px solid #ddd !important;
  }
  .bordertop{
    border-top:1px solid #ddd !important;
  }
  .pd-20{
    padding: 5px
  }
  .mt-20{
margin-top:20px;
  }
  .font14{
    font-size: 18px;
  }

  .alert.alert-with-icon i[data-notify=icon]{
top :20px !important;
}
.Signin{
  background: #f44336 !important;
}
.navbar-brand {
    padding: 5px 0;
    font-size: 22px;
    line-height: 20px;
    font-weight: 300;
    text-transform: uppercase;
    letter-spacing: .2em;
    color: #000000;
    font-family: "Roboto","Helvetica Neue",Helvetica,Arial,sans-serif;
}
.navbar-brand .logo {
    display: inline-block;
    margin-right: 10px;
    width: 40px;
    height: 40px;
    font-size: 25px;
    line-height: 40px;
    text-align: center;
    letter-spacing: normal;
    color: #f5f5f5;
    font-weight: 400;
    background-color: #e94441;
    border-radius: 50%;
}

</style>

</head>

<body class="off-canvas-sidebar">
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-transparent navbar-absolute fixed-top text-white" id="navigation-example">
    <div class="container">
      <div class="navbar-wrapper">
        <a class="navbar-brand" href="javascript:void(0)"><img src=""  class="img-responsive" style="filter: brightness(0) invert(1);height:40px"/></a>
      </div>
      <button class="navbar-toggler" type="button" data-toggle="collapse" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation" data-target="#navigation-example">
        <span class="sr-only">Toggle navigation</span>
        <span class="navbar-toggler-icon icon-bar"></span>
        <span class="navbar-toggler-icon icon-bar"></span>
        <span class="navbar-toggler-icon icon-bar"></span>
      </button>
      <div class="collapse navbar-collapse justify-content-end">
      </div>
    </div>
  </nav>
  <!-- End Navbar -->
<div class="wrapper wrapper-full-page">
   <div class="page-header login-page header-filter" filter-color="black" style="background-image: url('assets/img/bookstore-bg.jpg'); background-size: cover; background-position: top center;">
      <div class="container">
         <div class="col-lg-4 col-md-6 col-sm-6 ml-auto mr-auto">
            <form class="form" id="Signin">
               <div class="card card-login card-hidden">
                  <div class="card-header card-header-default text-center">
                     <h4 class="card-title">Log in</h4>
                     <div class="social-line">
                     </div>

                  </div>
                  <div class="card-body ">
                    <div class="logo text-center">
                        <a href="javascript:void(0)" class="simple-text logo-mini" style="display:none;">
                        Doctrac
                        </a>
                        <img src="<?php echo base_url(); ?>/assets/img/doctrack_00000.png" style="width: 190px;" />
                        <a href="javascript:void(0)" class="simple-text logo-normal text-center" style="display: none;">
                        <img alt="Doctrac" src="<?php echo base_url(); ?>/assets/img/findocs.png" style="height:50px;">
                        </a>
                     </div>

                     <div class="form-group has-default">
                        <div class="input-group">
                           <div class="input-group-prepend">
                              <span class="input-group-text">
                              <i class="icon-user font14"></i>
                              </span>
                           </div>
                           <input type="text" class="form-control"  id="Username" name="Username" placeholder="Login ID *">
                        </div>
                     </div>
                     <div class="form-group has-default">
                        <div class="input-group">
                           <div class="input-group-prepend">
                              <span class="input-group-text">
                              <i class="icon-lock5 font14"></i>
                              </span>
                           </div>
                           <input type="password" placeholder="Password *" class="form-control" id="Password" name="Password">
                        </div>
                     </div>
                  </div>
                  <div class="card-footer justify-content-center">
                     <div class="text-center" style="display: block">
                        <button type="submit" class="btn btn-tumblr btn-round mt-10 Signin" >Sign in</button>
                     </div>
                     <div class="form-group">
                        <div class="text-right">
                           <a href="<?php echo base_url('Login/forgotpassword');?>" class="btn  btn-link btn-tumblr" style="padding: 5px 12px !important;color: #000;">Forgot Password ?</a>
                        </div>
                     </div>
                    <div class="form-group">
                      <div class="text-center">
                        <a href="https://www.avanzegroup.com" target="_blank">
                          <h3 style="color: #37474f; margin-top: 0px !important; font-size: 1rem;">Powered by Avanze Tech Labs Inc.</h3>
                        </a>
                     </div>
                     </div>
                     <div class="row bordertop">
                        <div class="col-md-4 text-center pd-20">
                           <i class="icon-chrome pr-10"></i> <br> <?php echo $this->config->item('BROWSER_DEFAULT_VERSION')['Chrome']; ?>+
                        </div>
                        <div class="col-md-4 borderleft borderright  text-center pd-20">
                           <i class="icon-firefox pr-10"></i> <br> <?php echo $this->config->item('BROWSER_DEFAULT_VERSION')['Mozilla']; ?>+
                        </div>
                        <div class="col-md-4  text-center pd-20">
                           <i class="icon-IE pr-10"></i> <br>  <?php echo $this->config->item('BROWSER_DEFAULT_VERSION')['IE']; ?>+
                        </div>
                     </div>
                  </div>
               </div>
               <?php if (!empty($message)) { ?>
               <div class="card card-login card-hidden">
                 <div class="card-body" style="margin-top: 10px;">
                   <div class="row">
                     <div class="col-md-12">
                      <p class="text-center">
                        <span class="icon-warning22" style="font-size:32px; color:#ff9a05;"></span> 
                      </p>
                      <h6 class="heading2 text-center" style="font-size:0.8em;"><?php echo $message; ?> &nbsp;</h6>
                      <h6 class="heading2 text-center" style="font-size:0.8em;">Download Latest Version &nbsp;<a target="_blank" href="<?php echo $link; ?>" style="color: blue; text-decoration: underline;">click here</a>.</h6>


                     </div>
                   </div>
                 </div>
               </div>
               <?php } ?>
            </form>
         </div>
      </div>
   </div>
</div>
<!--   Core JS Files   -->

<script src="assets/js/core/jquery.min.js" type="text/javascript"></script>
<script src="assets/js/core/popper.min.js" type="text/javascript"></script>
<script src="assets/js/core/bootstrap-material-design.min.js" type="text/javascript"></script>
<script src="assets/js/plugins/perfect-scrollbar.jquery.min.js"></script>
<!-- Plugin for the momentJs  -->
<script src="assets/js/plugins/moment.min.js"></script>
<!--  Plugin for Sweet Alert -->
<script src="assets/js/plugins/sweetalert2.js"></script>
<!--  Plugin for Tags, full documentation here: https://github.com/bootstrap-tagsinput/bootstrap-tagsinputs  -->
<script src="assets/js/plugins/bootstrap-tagsinput.js"></script>
<!-- Plugin for Fileupload, full documentation here: http://www.jasny.net/bootstrap/javascript/#fileinput -->
<script src="assets/js/plugins/jasny-bootstrap.min.js"></script>
<script src="assets/js/plugins/arrive.min.js"></script>
<!--  Notifications Plugin    -->
<script src="assets/js/plugins/bootstrap-notify.js"></script>
<!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
<script src="assets/js/material-dashboard.min.js" type="text/javascript"></script>
<!-- Material Dashboard DEMO methods, don't include it in your project! -->
<script src="assets/demo/demo.js"></script>
<script>
  
  $(document).ready(function() {
    $().ready(function() {
      $sidebar = $('.sidebar');

      $sidebar_img_container = $sidebar.find('.sidebar-background');

      $full_page = $('.full-page');

      $sidebar_responsive = $('body > .navbar-collapse');

      window_width = $(window).width();

      fixed_plugin_open = $('.sidebar .sidebar-wrapper .nav li.active a p').html();

      if (window_width > 767 && fixed_plugin_open == 'Dashboard') {
        if ($('.fixed-plugin .dropdown').hasClass('show-dropdown')) {
          $('.fixed-plugin .dropdown').addClass('open');
        }

      }


      // $("#Username").change(function(event){        
      //    event.stopPropagation();
      //   alert();
      // })

      $('.fixed-plugin a').click(function(event) {
          // Alex if we click on switch, stop propagation of the event, so the dropdown will not be hide, otherwise we set the  section active
          if ($(this).hasClass('switch-trigger')) {
            if (event.stopPropagation) {
              event.stopPropagation();
            } else if (window.event) {
              window.event.cancelBubble = true;
            }
          }
        });

      $('.fixed-plugin .active-color span').click(function() {

        $full_page_background = $('.full-page-background');

        $(this).siblings().removeClass('active');
        $(this).addClass('active');

        var new_color = $(this).data('color');


        if ($sidebar.length != 0) {
          $sidebar.attr('data-color', new_color);
        }

        if ($full_page.length != 0) {
          $full_page.attr('filter-color', new_color);
        }

        if ($sidebar_responsive.length != 0) {
          $sidebar_responsive.attr('data-color', new_color);
        }
      });

      $('.fixed-plugin .background-color .badge').click(function() {
        $(this).siblings().removeClass('active');
        $(this).addClass('active');

        var new_color = $(this).data('background-color');


        if ($sidebar.length != 0) {
          $sidebar.attr('data-background-color', new_color);
        }
      });

      $('.fixed-plugin .img-holder').click(function() {
        $full_page_background = $('.full-page-background');

        $(this).parent('li').siblings().removeClass('active');
        $(this).parent('li').addClass('active');


        var new_image = $(this).find("img").attr('src');

        if ($sidebar_img_container.length != 0 && $('.switch-sidebar-image input:checked').length != 0) {
          $sidebar_img_container.fadeOut('fast', function() {
            $sidebar_img_container.css('background-image', 'url("' + new_image + '")');
            $sidebar_img_container.fadeIn('fast');
          });
        }

        if ($full_page_background.length != 0 && $('.switch-sidebar-image input:checked').length != 0) {
          var new_image_full_page = $('.fixed-plugin li.active .img-holder').find('img').data('src');

          $full_page_background.fadeOut('fast', function() {
            $full_page_background.css('background-image', 'url("' + new_image_full_page + '")');
            $full_page_background.fadeIn('fast');
          });
        }

        if ($('.switch-sidebar-image input:checked').length == 0) {
          var new_image = $('.fixed-plugin li.active .img-holder').find("img").attr('src');
          var new_image_full_page = $('.fixed-plugin li.active .img-holder').find('img').data('src');

          $sidebar_img_container.css('background-image', 'url("' + new_image + '")');
          $full_page_background.css('background-image', 'url("' + new_image_full_page + '")');
        }

        if ($sidebar_responsive.length != 0) {
          $sidebar_responsive.css('background-image', 'url("' + new_image + '")');
        }
      });

      $('.switch-sidebar-image input').change(function() {
        $full_page_background = $('.full-page-background');

        $input = $(this);

        if ($input.is(':checked')) {
          if ($sidebar_img_container.length != 0) {
            $sidebar_img_container.fadeIn('fast');
            $sidebar.attr('data-image', '#');
          }

          if ($full_page_background.length != 0) {
            $full_page_background.fadeIn('fast');
            $full_page.attr('data-image', '#');
          }

          background_image = true;
        } else {
          if ($sidebar_img_container.length != 0) {
            $sidebar.removeAttr('data-image');
            $sidebar_img_container.fadeOut('fast');
          }

          if ($full_page_background.length != 0) {
            $full_page.removeAttr('data-image', '#');
            $full_page_background.fadeOut('fast');
          }

          background_image = false;
        }
      });

      $('.switch-sidebar-mini input').change(function() {
        $body = $('body');

        $input = $(this);

        if (md.misc.sidebar_mini_active == true) {
          $('body').removeClass('sidebar-mini');
          md.misc.sidebar_mini_active = false;

          $('.sidebar .sidebar-wrapper, .main-panel').perfectScrollbar();

        } else {

          $('.sidebar .sidebar-wrapper, .main-panel').perfectScrollbar('destroy');

          setTimeout(function() {
            $('body').addClass('sidebar-mini');

            md.misc.sidebar_mini_active = true;
          }, 300);
        }

          // we simulate the window Resize so the charts will get updated in realtime.
          var simulateWindowResize = setInterval(function() {
            window.dispatchEvent(new Event('resize'));
          }, 180);

          // we stop the simulation of Window Resize after the animations are completed
          setTimeout(function() {
            clearInterval(simulateWindowResize);
          }, 1000);

        });
    });
});
</script>
<script>
  $(document).ready(function() {
        
    demo.checkFullPageBackgroundImage();
    setTimeout(function() {
        
        $('.card').removeClass('card-hidden');
      }, 700);

    $('.Signin').on('click',function(e){

       var theUrl = getUrlParameter('url');

        e.preventDefault();
        var data = $('#Signin').serialize() +'&'+ $.param({'theUrl':theUrl});

        $.ajax({
          url:'<?php echo base_url();?>Login/LoginSubmit',
          cache:false,
          type:'POST',
          data:data,
          dataType:'json',
          success:function(data)
          {
            
            if(data.validation_error == 1)
            {

              if(data.Redirect == 'verifyotp')
            {
              $.notify(
              {
                icon:"icon-bell-check",
                message:data.message
              },
              {
                type:"success",
                delay:1000
              });

              setTimeout ("window.location.href='<?php echo base_url();?>Login/Otp'", 3500);

            }
              else if(data.Redirect == 'ChangePassword')
              {
                window.location.replace("<?php echo base_url('Login/firstloginchangepasswordpage');?>");
              }else if(data.Redirect == 'theUrl')
              {
                 window.location.replace(data.URL);
              }
              else
              {
                $('.Signin').attr('disabled',true); 
                 window.location.replace("<?php echo base_url();?>"+data.Redirect);
              }

           }
           else
           {
            $.notify(
              {
                icon:"icon-bell-check",
                message:data.message
              },
              {
                type:"danger",
                delay:1000 
              });
              $('.Signin').attr('disabled',false); 
              $('#Password').val('');
           }
          },
          error:function(jqXHR, textStatus, errorThrown)
         {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
         } 

        });

    });

  });

    var getUrlParameter = function getUrlParameter(sParam) {
    var sPageURL = window.location.search.substring(1),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
        }
    }
};

</script>
</body>

</html>

