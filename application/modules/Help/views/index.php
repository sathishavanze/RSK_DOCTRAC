
<link rel="stylesheet" href="<?php echo base_url('assets/videopopup/css/videopopup.css');?>">
<style type="text/css">
.panel-body img{
  width: 83%;
  margin: 10px auto;
  display: block;
  border: 2px solid #d4cdcd;
  -webkit-box-shadow: -1px 1px 10px 2px #c1c1c1;
  -moz-box-shadow: -1px 1px 10px 2px #c1c1c1;
  box-shadow: -1px 1px 10px 2px #c1c1c1;
  padding: 5px;
}
.panel-body video
{
  width: 83%;
  margin: 10px auto;
  display: block;
  border: 2px solid #d4cdcd;
  -webkit-box-shadow: -1px 1px 10px 2px #c1c1c1;
  -moz-box-shadow: -1px 1px 10px 2px #c1c1c1;
  box-shadow: -1px 1px 10px 2px #c1c1c1;
  padding: 5px;
}
p{
  display: block;
  width: 100%;
} 
.be-booking-promo.be-booking-promo-danger {
    border-left-color: #ea4335;
    background-color: #f2f1f1;
}
.be-booking-promo-big.be-booking-promo-danger .be-booking-desc-title a{
  color: #404040 !important;
  font-weight: 400;
  line-height: 23px;
}
.be-booking-promo {
    position: relative;
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-orient: horizontal;
    -webkit-flex-direction: row;
    -ms-flex-direction: row;
    flex-direction: row;
    -webkit-box-pack: justify;
    -webkit-justify-content: space-between;
    -ms-flex-pack: justify;
    justify-content: space-between;
    border-radius: 6px;
    background-color: #fff;
    border-left: 3px solid #4285f4;
    padding: 23px 20px 20px;
    margin-bottom: 15px;
}
.be-booking-promo, .be-option-button, .table-filters {
    -webkit-box-direction: normal;
}
.be-booking-desc {
    /*-webkit-box-flex: 0;
    -webkit-flex: 0 1 60%;
    -ms-flex: 0 1 60%;
    flex: 0 1 60%;*/
}
.be-booking-desc-title {
  padding-bottom: 10px;
  font-size: 16px;
  margin: 0 0 13px;
  width: 100%;
  border-bottom: 1px solid #ccc;
}
.be-booking-desc-details {
  font-size: 13px;
}
.alert-contrast>.icon, .alert-icon>.icon, .be-booking-promo-price, .btn-big, .dropdown-tools, .nav-tabs>li a, .page-aside.codeditor .compose, .page-aside.codeditor .mail-nav ul li a i, .table tr td.actions, .table tr th.actions {
  text-align: center;
}
.be-booking-promo-amount {
  position: relative;
  margin: 3px 0 45px;
}
.btn.be-booking-btn-price {
  padding: 0 10px;
  line-height: 34px;
}
.be-booking-promo-big .be-promo-big-title {
  position: absolute;
  font-size: 25px;
  color: #fff;
  top: 5px;
  right: 11px;
}
.be-booking-promo-big.be-booking-promo-danger:before {
    border-right-color: #ea4335;
}
.be-booking-promo-big:before {
    position: absolute;
    top: 0;
    right: 0;
    display: block;
    border-color: transparent;
}
.be-booking-promo-big:before, .be-booking-promo.be-booking-promo-soldout:before {
    content: '';
    width: 0;
    height: 0;
    display: none;
    border-style: solid;
    border-width: 0 72px 72px 0;
}
.col-md-4 {
  /*padding-left: 0px;*/
}
.btn.btn-danger {
 color: #ea4335;
 background-color: #ffffff;
} 
.btn{
  margin: 0px;
}
</style>

<div class="card mt-20" id="Orderentrycard">
  <div class="card-header card-header-danger card-header-icon">
    <div class="card-icon"> <i class="icon-help"></i>
    </div>
    <div class="row">
      <div class="col-md-10">
        <h4 class="card-title">HELP</h4>
      </div>
    </div>
  </div>

<div class="card-body">
  <!-- First row -->
  <div class="row">
  <!-- <div class="col-md-12">
      <h4 class="be-booking-desc-title">Client-Project Setup</h4>
    </div> -->
    <div class="col-md-4"> 
      <div class="be-booking-promo be-booking-promo-danger be-booking-promo-big">
        <div class="be-booking-desc">
          <h4 class="be-booking-desc-title"><!-- <a href="http://10.20.30.125/direct2title/help/view_details/1"> -->Client Setup<!-- </a> --></h4>
          <span class="be-booking-desc-details">How to Setup a client?</span>
        </div>
        <div class="be-booking-promo-price">
          <div class="be-booking-promo-amount"></div>
          <a href="javascript:void(0);"  id="video" class="btn btn-danger be-booking-btn-price">Get Started <i class="fa fa-angle-double-right"></i></a>
        </div><span class="be-promo-big-title mdi mdi-assignment"></span>
      </div> 
    </div>   
    
    <div class="col-md-4"> 
      <div class="be-booking-promo be-booking-promo-danger be-booking-promo-big">
        <div class="be-booking-desc">
          <h4 class="be-booking-desc-title">Product Setup</h4>
          <span class="be-booking-desc-details">How to Setup a product?</span>
        </div>
        <div class="be-booking-promo-price">
          <div class="be-booking-promo-amount"></div>
          <a href="javascript:void(0);"  id="" class="btn btn-danger be-booking-btn-price tour">Get Started <i class="fa fa-angle-double-right"></i></a>
        </div><span class="be-promo-big-title mdi mdi-assignment"></span>
      </div> 
    </div>   

    <div class="col-md-4"> 
      <div class="be-booking-promo be-booking-promo-danger be-booking-promo-big">
        <div class="be-booking-desc">
          <h4 class="be-booking-desc-title">Project Setup</h4>
          <span class="be-booking-desc-details">How to Setup a project?</span>
        </div>
        <div class="be-booking-promo-price">
          <div class="be-booking-promo-amount"></div>
          <a href="javascript:void(0);" id=""  class="btn btn-danger be-booking-btn-price">Get Started <i class="fa fa-angle-double-right"></i></a>
        </div><span class="be-promo-big-title mdi mdi-assignment"></span>
      </div> 
    </div>       
  </div>
  <!-- First row ends -->

  <!-- Second Row -->
    <div class="row">
    <!-- <div class="col-md-12">
      <h4 class="be-booking-desc-title">Order Entry</h4>
    </div> -->
    <div class="col-md-4"> 
      <div class="be-booking-promo be-booking-promo-danger be-booking-promo-big">
        <div class="be-booking-desc">
          <h4 class="be-booking-desc-title">Single Order Entry</h4>
          <span class="be-booking-desc-details">How to use Single Order Entry?</span>
        </div>
        <div class="be-booking-promo-price">
          <div class="be-booking-promo-amount"></div>
          <a href="javascript:void(0);"  id="video1" class="btn btn-danger be-booking-btn-price">Get Started <i class="fa fa-angle-double-right"></i></a>
        </div><span class="be-promo-big-title mdi mdi-assignment"></span>
      </div> 
    </div>   
    
    <div class="col-md-4"> 
      <div class="be-booking-promo be-booking-promo-danger be-booking-promo-big">
        <div class="be-booking-desc">
          <h4 class="be-booking-desc-title">Bulk Order Entry</h4>
          <span class="be-booking-desc-details">How to use Bulk Order Entry?</span>
        </div>
        <div class="be-booking-promo-price">
          <div class="be-booking-promo-amount"></div>
          <a href="javascript:void(0);"  id="video2" class="btn btn-danger be-booking-btn-price tour">Get Started <i class="fa fa-angle-double-right"></i></a>
        </div><span class="be-promo-big-title mdi mdi-assignment"></span>
      </div> 
    </div>   

    <div class="col-md-4"> 
      <div class="be-booking-promo be-booking-promo-danger be-booking-promo-big">
        <div class="be-booking-desc">
          <h4 class="be-booking-desc-title">Doc Upload</h4>
          <span class="be-booking-desc-details">How to use Doc Upload?</span>
        </div>
        <div class="be-booking-promo-price">
          <div class="be-booking-promo-amount"></div>
          <a href="javascript:void(0);" id=""  class="btn btn-danger be-booking-btn-price" onclick="video();">Get Started <i class="fa fa-angle-double-right"></i></a>
        </div><span class="be-promo-big-title mdi mdi-assignment"></span>
      </div> 
    </div>       
  </div>
  <!-- Second row ends -->

    <!-- Third Row -->
    <div class="row">
     <!--  <div class="col-md-12">
        <h4 class="">Workflows</h4>
      </div> -->

    <div class="col-md-4"> 
      <div class="be-booking-promo be-booking-promo-danger be-booking-promo-big">
        <div class="be-booking-desc">
          <h4 class="be-booking-desc-title">Document Check-In</h4>
          <span class="be-booking-desc-details">How to use Document Check-In?</span>
        </div>
        <div class="be-booking-promo-price">
          <div class="be-booking-promo-amount"></div>
          <a href="javascript:void(0);"  id="video3" class="btn btn-danger be-booking-btn-price">Get Started <i class="fa fa-angle-double-right"></i></a>
        </div><span class="be-promo-big-title mdi mdi-assignment"></span>
      </div> 
    </div>

    <div class="col-md-4"> 
      <div class="be-booking-promo be-booking-promo-danger be-booking-promo-big">
        <div class="be-booking-desc">
          <h4 class="be-booking-desc-title">Indexing & Stacking</h4>
          <span class="be-booking-desc-details">How to use Indexing & Stacking?</span>
        </div>
        <div class="be-booking-promo-price">
          <div class="be-booking-promo-amount"></div>
          <a href="javascript:void(0);"  id="video4" class="btn btn-danger be-booking-btn-price">Get Started <i class="fa fa-angle-double-right"></i></a>
        </div><span class="be-promo-big-title mdi mdi-assignment"></span>
      </div> 
    </div>   
    
    <div class="col-md-4"> 
      <div class="be-booking-promo be-booking-promo-danger be-booking-promo-big">
        <div class="be-booking-desc">
          <h4 class="be-booking-desc-title">Auditing</h4>
          <span class="be-booking-desc-details">How to use Auditing?</span>
        </div>
        <div class="be-booking-promo-price">
          <div class="be-booking-promo-amount"></div>
          <a href="javascript:void(0);"  id="video5" class="btn btn-danger be-booking-btn-price tour">Get Started <i class="fa fa-angle-double-right"></i></a>
        </div><span class="be-promo-big-title mdi mdi-assignment"></span>
      </div> 
    </div>         
  </div>
  <!-- Third row ends -->


      <!-- Fourth Row -->
    <div class="row">

      <div class="col-md-4"> 
      <div class="be-booking-promo be-booking-promo-danger be-booking-promo-big">
        <div class="be-booking-desc">
          <h4 class="be-booking-desc-title">Review</h4>
          <span class="be-booking-desc-details">How to use Review?</span>
        </div>
        <div class="be-booking-promo-price">
          <div class="be-booking-promo-amount"></div>
          <a href="javascript:void(0);"   class="btn btn-danger be-booking-btn-price" onclick="video();">Get Started <i class="fa fa-angle-double-right"></i></a>
        </div><span class="be-promo-big-title mdi mdi-assignment"></span>
      </div> 
    </div> 
  
    <div class="col-md-4"> 
      <div class="be-booking-promo be-booking-promo-danger be-booking-promo-big">
        <div class="be-booking-desc">
          <h4 class="be-booking-desc-title">Export</h4>
          <span class="be-booking-desc-details">How to use Export?</span>
        </div>
        <div class="be-booking-promo-price">
          <div class="be-booking-promo-amount"></div>
          <a href="javascript:void(0);"  id="video" class="btn btn-danger be-booking-btn-price">Get Started <i class="fa fa-angle-double-right"></i></a>
        </div><span class="be-promo-big-title mdi mdi-assignment"></span>
      </div> 
    </div>   
    
    <div class="col-md-4"> 
      <div class="be-booking-promo be-booking-promo-danger be-booking-promo-big">
        <div class="be-booking-desc">
          <h4 class="be-booking-desc-title">Shipping</h4>
          <span class="be-booking-desc-details">How to use Shipping?</span>
        </div>
        <div class="be-booking-promo-price">
          <div class="be-booking-promo-amount"></div>
          <a href="javascript:void(0);"  id="<?php echo $menu->HelpMenuUID;?>" class="btn btn-danger be-booking-btn-price tour">Get Started <i class="fa fa-angle-double-right"></i></a>
        </div><span class="be-promo-big-title mdi mdi-assignment"></span>
      </div> 
    </div>        
  </div>
  <!-- Fourth row ends -->

  <!-- Fifth Row Starts -->
  <div class="row">
    <div class="col-md-4"> 
      <div class="be-booking-promo be-booking-promo-danger be-booking-promo-big">
        <div class="be-booking-desc">
          <h4 class="be-booking-desc-title">Exception</h4>
          <span class="be-booking-desc-details">How to use Exception?</span>
        </div>
        <div class="be-booking-promo-price">
          <div class="be-booking-promo-amount"></div>
          <a href="javascript:void(0);"   class="btn btn-danger be-booking-btn-price" onclick="video();">Get Started <i class="fa fa-angle-double-right"></i></a>
        </div><span class="be-promo-big-title mdi mdi-assignment"></span>
      </div> 
    </div>  
    
    <div class="col-md-4"> 
      <div class="be-booking-promo be-booking-promo-danger be-booking-promo-big">
        <div class="be-booking-desc">
          <h4 class="be-booking-desc-title">Follow-Up</h4>
          <span class="be-booking-desc-details">How to use Follow-Up?</span>
        </div>
        <div class="be-booking-promo-price">
          <div class="be-booking-promo-amount"></div>
          <a href="javascript:void(0);"  id="video" class="btn btn-danger be-booking-btn-price">Get Started <i class="fa fa-angle-double-right"></i></a>
        </div><span class="be-promo-big-title mdi mdi-assignment"></span>
      </div> 
    </div>  

    <div class="col-md-4"> 
      <div class="be-booking-promo be-booking-promo-danger be-booking-promo-big">
        <div class="be-booking-desc">
          <h4 class="be-booking-desc-title">Missing Docs</h4>
          <span class="be-booking-desc-details">How to read missing docs?</span>
        </div>
        <div class="be-booking-promo-price">
          <div class="be-booking-promo-amount"></div>
          <a href="javascript:void(0);"  id="video6" class="btn btn-danger be-booking-btn-price">Get Started <i class="fa fa-angle-double-right"></i></a>
        </div><span class="be-promo-big-title mdi mdi-assignment"></span>
      </div> 
    </div> 
  </div>
  <!-- Fifth Row Ends -->

  <!-- Sixth Row Starts -->
  <div class="row">
    <div class="col-md-4"> 
      <div class="be-booking-promo be-booking-promo-danger be-booking-promo-big">
        <div class="be-booking-desc">
          <h4 class="be-booking-desc-title">Received Docs</h4>
          <span class="be-booking-desc-details">How to read received docs?</span>
        </div>
        <div class="be-booking-promo-price">
          <div class="be-booking-promo-amount"></div>
          <a href="javascript:void(0);" id="video7"  class="btn btn-danger be-booking-btn-price" onclick="video();">Get Started <i class="fa fa-angle-double-right"></i></a>
        </div><span class="be-promo-big-title mdi mdi-assignment"></span>
      </div> 
    </div>  

    <div class="col-md-4"> 
      <div class="be-booking-promo be-booking-promo-danger be-booking-promo-big">
        <div class="be-booking-desc">
          <h4 class="be-booking-desc-title">Channel Funding</h4>
          <span class="be-booking-desc-details">Read monthly funding by channel?</span>
        </div>
        <div class="be-booking-promo-price">
          <div class="be-booking-promo-amount"></div>
          <a href="javascript:void(0);" id="video8"  class="btn btn-danger be-booking-btn-price" onclick="video();">Get Started <i class="fa fa-angle-double-right"></i></a>
        </div><span class="be-promo-big-title mdi mdi-assignment"></span>
      </div> 
    </div>   

    <div class="col-md-4"> 
      <div class="be-booking-promo be-booking-promo-danger be-booking-promo-big">
        <div class="be-booking-desc">
          <h4 class="be-booking-desc-title">Shipped Docs</h4>
          <span class="be-booking-desc-details">How to read Shipped docs?</span>
        </div>
        <div class="be-booking-promo-price">
          <div class="be-booking-promo-amount"></div>
          <a href="javascript:void(0);" id="video9"  class="btn btn-danger be-booking-btn-price" onclick="video();">Get Started <i class="fa fa-angle-double-right"></i></a>
        </div><span class="be-promo-big-title mdi mdi-assignment"></span>
      </div> 
    </div>   
  </div>
  <!-- Sixth Row Ends -->
  <!-- Seventh Row Starts -->
  <div class="row">
    <div class="col-md-4"> 
      <div class="be-booking-promo be-booking-promo-danger be-booking-promo-big">
        <div class="be-booking-desc">
          <h4 class="be-booking-desc-title">Ageing - Missing Docs</h4>
          <span class="be-booking-desc-details">How to read missed docs?</span>
        </div>
        <div class="be-booking-promo-price">
          <div class="be-booking-promo-amount"></div>
          <a href="javascript:void(0);" id="video10"  class="btn btn-danger be-booking-btn-price" onclick="video();">Get Started <i class="fa fa-angle-double-right"></i></a>
        </div><span class="be-promo-big-title mdi mdi-assignment"></span>
      </div> 
    </div>  

    <div class="col-md-4"> 
      <div class="be-booking-promo be-booking-promo-danger be-booking-promo-big">
        <div class="be-booking-desc">
          <h4 class="be-booking-desc-title">Ageing - Received Docs</h4>
          <span class="be-booking-desc-details">How to read received docs?</span>
        </div>
        <div class="be-booking-promo-price">
          <div class="be-booking-promo-amount"></div>
          <a href="javascript:void(0);" id="video11"  class="btn btn-danger be-booking-btn-price" onclick="video();">Get Started <i class="fa fa-angle-double-right"></i></a>
        </div><span class="be-promo-big-title mdi mdi-assignment"></span>
      </div> 
    </div>   

    <div class="col-md-4"> 
      <div class="be-booking-promo be-booking-promo-danger be-booking-promo-big">
        <div class="be-booking-desc">
          <h4 class="be-booking-desc-title">Ageing - Shipping Docs</h4>
          <span class="be-booking-desc-details">How to read Shipped docs?</span>
        </div>
        <div class="be-booking-promo-price">
          <div class="be-booking-promo-amount"></div>
          <a href="javascript:void(0);" id="video12"  class="btn btn-danger be-booking-btn-price" onclick="video();">Get Started <i class="fa fa-angle-double-right"></i></a>
        </div><span class="be-promo-big-title mdi mdi-assignment"></span>
      </div> 
    </div>   
  </div>
  <!-- Seventh Row Ends -->
  </div>
</div>

<div id="vidBox1">
  <div id="videCont1" class="videCont">
    <video  id="v1" loop controls>
      <source src="<?php echo base_url();?>videos/How_to_upload_order_in_single_entry.mp4" type="video/mp4">
      <source src="<?php echo base_url();?>videos/How_to_upload_order_in_single_entry.ogg" type="video/ogg">
    </video>
  </div>
</div>

<div id="vidBox2">
  <div id="videCont2" class="videCont">
    <video  id="v2" loop controls>
      <source src="<?php echo base_url();?>videos/How_to_upload_order_in_bulk_entry.mp4" type="video/mp4">
      <source src="<?php echo base_url();?>videos/How_to_upload_order_in_bulk_entry.ogg" type="video/ogg">
    </video>
  </div>
</div>

<div id="vidBox3">
  <div id="videCont3" class="videCont">
    <video  id="v3" loop controls>
      <source src="<?php echo base_url();?>videos/Doc check in.mp4" type="video/mp4">
      <source src="<?php echo base_url();?>videos/Doc check in.ogg" type="video/ogg">
    </video>
  </div>
</div>

<div id="vidBox4">
  <div id="videCont4" class="videCont">
    <video id="v4" loop controls>
      <source src="<?php echo base_url();?>videos/How_to_complete_indexing_and_stacking_workflow.mp4" type="video/mp4">
      <source src="<?php echo base_url();?>videos/How_to_complete_indexing_and_stacking_workflow.ogg" type="video/ogg">
    </video>
  </div>
</div>

<div id="vidBox5">
  <div id="videCont5" class="videCont">
    <video  id="v5" loop controls>
      <source src="<?php echo base_url();?>videos/AuditingSample.mp4" type="video/mp4">
      <source src="<?php echo base_url();?>videos/AuditingSample.ogg" type="video/ogg">
    </video>
  </div>
</div>

<div id="vidBox6">
  <div id="videCont6" class="videCont">
    <video id="v6" loop controls>
      <source src="<?php echo base_url();?>videos/Analytics-Missing docs.mp4" type="video/mp4">
      <source src="<?php echo base_url();?>videos/Analytics-Missing docs.ogg" type="video/ogg">
    </video>
  </div>
</div>

<div id="vidBox7">
  <div id="videCont7" class="videCont">
    <video id="v7" loop controls>
      <source src="<?php echo base_url();?>videos/Analytics- Received docs.mp4" type="video/mp4">
      <source src="<?php echo base_url();?>videos/Analytics- Received docs.ogg" type="video/ogg">
    </video>
  </div>
</div>

<div id="vidBox8">
  <div id="videCont8" class="videCont">
    <video id="v8" loop controls>
      <source src="<?php echo base_url();?>videos/Analytics - Monthly fundings by channel.mp4" type="video/mp4">
      <source src="<?php echo base_url();?>videos/Analytics - Monthly fundings by channel.ogg" type="video/ogg">
    </video>
  </div>
</div>

<div id="vidBox9">
  <div id="videCont9" class="videCont">
    <video id="v9" loop controls>
      <source src="<?php echo base_url();?>videos/Analytics - Shipping.mp4" type="video/mp4">
      <source src="<?php echo base_url();?>videos/Analytics - Shipping.ogg" type="video/ogg">
    </video>
  </div>
</div>

<div id="vidBox10">
  <div id="videCont10" class="videCont">
    <video id="v10" loop controls>
      <source src="<?php echo base_url();?>videos/Ageing - Missing docs.mp4" type="video/mp4">
      <source src="<?php echo base_url();?>videos/Ageing - Missing docs.ogg" type="video/ogg">
    </video>
  </div>
</div>

<div id="vidBox11">
  <div id="videCont11" class="videCont">
    <video id="v11" loop controls>
      <source src="<?php echo base_url();?>videos/Ageing-received doc.mp4" type="video/mp4">
      <source src="<?php echo base_url();?>videos/Ageing-received doc.ogg" type="video/ogg">
    </video>
  </div>
</div>

<div id="vidBox12">
  <div id="videCont12" class="videCont">
    <video id="v12" loop controls>
      <source src="<?php echo base_url();?>videos/Ageing- Shipped docs.mp4" type="video/mp4">
      <source src="<?php echo base_url();?>videos/Ageing- Shipped docs.ogg" type="video/ogg">
    </video>
  </div>
</div>


<script type="text/javascript" src="<?php echo base_url('assets/videopopup/js/videopopup.js')?>"></script>

<script type="text/javascript">

 $('#vidBox1').VideoPopUp({
    backgroundColor: "#17212a",
    opener: "video1",
      maxweight: "340",
      idvideo: "v1"
  });

 $('#vidBox2').VideoPopUp({
    backgroundColor: "#17212a",
    opener: "video2",
      maxweight: "340",
      idvideo: "v2"
  });

 $('#vidBox3').VideoPopUp({
    backgroundColor: "#17212a",
    opener: "video3",
      maxweight: "340",
      idvideo: "v3"
  });

 $('#vidBox4').VideoPopUp({
    backgroundColor: "#17212a",
    opener: "video4",
      maxweight: "340",
      idvideo: "v4"
  });

  $('#vidBox6').VideoPopUp({
    backgroundColor: "#17212a",
    opener: "video6",
      maxweight: "340",
      idvideo: "v5"
  });

  $('#vidBox7').VideoPopUp({
    backgroundColor: "#17212a",
    opener: "video7",
      maxweight: "340",
      idvideo: "v7"
  });

  $('#vidBox8').VideoPopUp({
    backgroundColor: "#17212a",
    opener: "video8",
      maxweight: "340",
      idvideo: "v8"
  });

  $('#vidBox9').VideoPopUp({
    backgroundColor: "#17212a",
    opener: "video9",
      maxweight: "340",
      idvideo: "v9"
  });

  $('#vidBox10').VideoPopUp({
    backgroundColor: "#17212a",
    opener: "video10",
      maxweight: "340",
      idvideo: "v10"
  });

  $('#vidBox11').VideoPopUp({
    backgroundColor: "#17212a",
    opener: "video11",
      maxweight: "340",
      idvideo: "v11"
  });

  $('#vidBox12').VideoPopUp({
    backgroundColor: "#17212a",
    opener: "video12",
      maxweight: "340",
      idvideo: "v12"
  });
</script>

