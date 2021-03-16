<?php
$controller = $this->uri->segment(1);

$enam  =  $this->UserPermissions->ProfileColor;
$enambg  =  $this->UserPermissions->ProfileBackground;
$sidebarbg  =  $this->UserPermissions->SidebarBackground;
$SidebarActive  =  $this->UserPermissions->SidebarActive;
$activeProfileColor = '';
if($enam ==1)
{
  $activeProfileColor = "purple";
  $custom_bg_color = '#9c27b0';
}
elseif($enam == 2)
{
  $activeProfileColor = "azure";
  $custom_bg_color = '#00bcd4';
}elseif($enam == 3)
{
  $activeProfileColor = "green";
  $custom_bg_color = '#66bb6a';
}elseif($enam == 4)
{
  $activeProfileColor = "orange";
  $custom_bg_color = '#fb8c00';
}elseif($enam == 5)
{
  $activeProfileColor = "danger";
  $custom_bg_color = '#f44336';
}elseif($enam == 6)
{
  $activeProfileColor = "rose";
  $custom_bg_color = '#e91e63';
}
else
{
 $activeProfileColor = "danger";
 $custom_bg_color = '#f44336';
}

if($enambg ==1)
{
  $custom_color = 'black';
}
elseif($enambg == 2)
{
  $custom_color = 'white';
}elseif($enambg == 3)
{
  $custom_color = 'red';
}
else
{
  $custom_color = 'black';
}


if($sidebarbg ==1)
{
  $sidebarbgcolor = 'assets/img/sidebar-1.jpg';
}elseif($sidebarbg == 2)
{
  $sidebarbgcolor = 'assets/img/sidebar-2.jpg';
}elseif($sidebarbg == 3)
{
  $sidebarbgcolor = 'assets/img/sidebar-3.jpg';
}elseif($sidebarbg == 4)
{
  $sidebarbgcolor = 'assets/img/sidebar-4.jpg';
}else
{
  $sidebarbgcolor = 'assets/img/sidebar-1.jpg';
}

if($SidebarActive == 1) { 
  $SidebarActive = '';
} else {
  $SidebarActive = 'sidebar-mini';
}
?>

<body class="<?php echo $SidebarActive; ?>">
  <svg class="d2tspinner-circular bodyspinner_svg" viewBox="25 25 50 50" style="width:50px;z-index: 999999;"><circle class="d2tspinner-path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/></svg>
  <div class='bodyoverlaydiv'></div>

  <?php 
  //profile image
  $profileimage = base_url() . "assets/img/profile.jpg";

  if (isset($this->UserPermissions->Avatar) && ($this->UserPermissions->Avatar != '')) {

    if (file_exists( FCPATH . $this->UserPermissions->Avatar)) {

      $profileimage = base_url() . $this->UserPermissions->Avatar;
    }

  }

  ?>

  <div class="wrapper">
    <div class="sidebar" data-color="<?php echo $activeProfileColor;  ?>" data-background-color="<?php echo $custom_color;  ?>" data-image="<?php echo base_url().$sidebarbgcolor; ?>"> 
      <div class="logo">
        <a href="<?php echo base_url(); ?>" class="simple-text logo-mini">
          <img alt="DOCTRAC - LOGO" src = "<?php echo base_url(); ?>assets/img/favicon.png" style="height:35px;" />
        </a>
        <a href="<?php echo base_url(); ?>" class="simple-text logo-normal text-center">
          <img alt="DOCTRAC - LOGO" src = "<?php echo base_url(); ?>assets/img/doctrack_wit.png" style="height:35px;" />
        </a>
      </div>
        <div class="sidebar-wrapper">
          <div class="user">
           <div class="photo">
            <img class="imagePreview1" src="<?php echo $profileimage; ?>" />
          </div>
          <div class="user-info">
            <a data-toggle="collapse" href="#collapseExample" class="username">
              <span>
               <?php echo wordwrap($this->UserPermissions->UserName,15,"<br>\n"); ?>
               <b class="caret"></b>
             </span>
           </a>
           <div class="collapse <?php if($controller=="Profile"){echo "show";}?>" id="collapseExample">
            <ul class="nav" id="leftsidebarmenu">
              <li class="nav-item <?php if($controller=="Profile"){echo "active";}?>">
                <a class="nav-link ajaxload" href="<?php echo base_url('Profile'); ?>">
                  <span class="sidebar-mini"> P </span>
                  <span class="sidebar-normal"> Profile </span>
                </a>
              </li>
              <!-- <li class="nav-item <?php if($controller=="Help"){echo "active";}?>">
                <a class="nav-link ajaxload" href="<?php echo base_url(); ?>Help">
                  <span class="sidebar-mini"> H </span>
                  <span class="sidebar-normal"> Help </span>
                </a>
              </li> -->
              <li class="nav-item">
                <a class="nav-link ajaxload" href="<?php echo base_url(); ?>Login/changepasswordpage">
                  <span class="sidebar-mini"> CP </span>
                  <span class="sidebar-normal"> Change Password </span>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="<?php echo base_url(); ?>Login/Logout">
                  <span class="sidebar-mini"> L </span>
                  <span class="sidebar-normal"> Logout </span>
                </a>
              </li>
            </ul>
          </div>
        </div>
      </div>
      <ul class="nav">

<!--        <li class="nav-item">
        <a class="nav-link inner_nav_a ajaxload sidebarlink-PreScreen_Orders" href="http://localhost/stacx/PreScreen_Orders">
          <span class="sidebar-mini side_new_icon"> PS <span id="PreScreen" class="badge badge-warning pull-right sidebar-mini side_new_icon_count">3000</span></span>
          <i style="display: none;" class="icon-file-eye side_new_i"></i>
          <p class="side_new_p" style="display: none;"> Pre-Screen&nbsp;<span id="PreScreen" class="badge badge-warning pull-right sidebar-mini" style="background-color: transparent;border: 1px solid #fff;margin-top: 5pt;position: absolute;right: 0px;">3</span></p>
        </a>
      </li> -->

       <?php $MenuLinks = $this->Common_Model->get_definedleftDynamicMenu_options(['common','sidebar']); ?>
       <?php foreach ($MenuLinks as $key => $value) {
         if(($value->MenuBarType=='common' || $value->MenuBarType=='sidebar') && $value->controller!='submenu')
        {

         ?>
         <li class="nav-item <?php if ($this->uri->segment(1) == $value->controller) {echo "active";} ?>">
          <a class="nav-link inner_nav_a ajaxload sidebarlink-<?php echo $value->controller; ?>" href="<?php echo base_url() . $value->controller; ?>">
            <p class="sidebar-mini side_new_icon"> <?php echo initial_name_generator($value->FieldName); ?> <?php echo $value->NotificationElement; ?></p>
            <i style="display: none;" class="<?php echo $value->IconClass; ?> side_new_i"></i>
            <p class="side_new_p" style="display: none;"> <?php echo $value->FieldName; ?>&nbsp;<?php echo $value->NotificationElement; ?></p>
          </a>
        </li>

     <?php

        }

      }
      ?> 
        </ul>              
      </div>
    </div>

    <style type="text/css">
      .inner_nav_a{
        margin: 5px 5px 0!important;
        padding-right: 4px!important;
        padding-left: 6px!important;
      }
      .inner_nav_a .side_new_icon {
        opacity: 1!important;
        transform: translate3d(0px, 0, 0)!important;
      }
      .inner_nav_a .side_new_icon .side_new_icon_count{
        background-color: transparent;
        border: 1px solid #fff;
        margin-top: 3pt;
        margin-right:3px;
        right: 0px;
        padding: 3px 3px;
      }
      .visible-on-sidebar-mini, .visible-on-sidebar-regular{
        cursor:pointer;
        z-index:1;
      }
    </style>


