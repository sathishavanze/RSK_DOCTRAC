<script src="<?php echo base_url(); ?>assets/js/plugins/bootstrap-notify.js"></script>
<script src="assets/plugins/EditableDatePicker.js" type="text/javascript"></script> 

<style type="text/css">


</style>

</div>
</div>

</div>
</div>

</div>
</div>

</div>


<div class="card hide" id="uploadPane-Card" style="position: fixed;bottom: 0%;right: 2%;width: 30%;z-index: 999999;">
  <div class="card-header " style="background-color: black;  color: #fff;">Uploads Pane
    <span id="uploadPaneClose" class="icon-close2 pull-right" style="font-size: 18px; margin-top: 5px; padding-right: 3px; padding-left: 3px;"></span>
    <span id="uploadPaneToggle" class="icon-move-down pull-right" style="font-size: 18px; margin-top: 5px; padding-right: 3px;"></span>
  </div>
  <div class="card-body" id="uploadPane-CardBody" style="max-height: 250px;overflow: auto;">
<!--     <li data-hash="2140438327" style="list-style-type: none;">
      <img src="http://localhost:2018/phpmyadmin/themes/pmahomme/img/s_error.png" title="finished" class="icon ic_s_error"> morderpriority.sql
      <span class="filesize pull-right" data-filename="morderpriority.sql">
        <span hash="2140438327" class="pma_drop_file_status" task="info">
          <span class="underline">Failed</span>
        </span>
      </span>
      <br>
      <progress max="100" value="10" style="/* display: none; */"></progress>
    </li> -->
  </div>
</div>
<!--   <nav id="menu-2" class="offside">
    <div class="col-md-12 text-left headertop">
      <div class="row">
        <div class="col-md-8">
         <h6>Logs History</h6>
       </div>

       <div class="col-md-4 text-right">
        <a href="#" class="icon icon--cross menu-btn-2--close h--left">
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </a> 
      </div>
    </div>
  </div>

</nav> -->




<script type="text/javascript">

  $(function () {
    $('#uploadPaneToggle').click(function (e) {
      e.preventDefault();

      $("#uploadPane-CardBody").toggle();
      $(this).toggleClass("icon-move-down icon-move-up");

    });

    $('#uploadPaneClose').click(function (e) {
      e.preventDefault();
      $("#uploadPane-CardBody").html("");
      $('#uploadPane-Card').addClass('hide');
    });


    
    // var offsideMenu2 = offside( '#menu-2', {

    //   slidingElementsSelector: '#containerstart, #results',
    //   debug: true, 
    //   buttonsSelector: '.menu-btn-2, .menu-btn-2--close',
    //   slidingSide: 'right',
    //   beforeOpen: function(){},
    //   beforeClose: function(){},
    // });



  });
</script>
