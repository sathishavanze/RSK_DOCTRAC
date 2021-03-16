
<link href="<?php echo base_url(); ?>assets/plugins/dropify/css/dropify.css" rel="stylesheet" />
<!-- <link rel="stylesheet" type="text/css" href="jquery.datetextentry.css"> -->
<style type="text/css">
 

.bmd-form-group .bmd-label-floating, .bmd-form-group .bmd-label-placeholder {
  top: -12px;
}
.custom_note_timeline{
  padding:0;
}
.custom_note_timeline>li>.timeline-badge{
  left: 10px;
  top: 15px;
}
.custom_note_timeline>li>.timeline-panel {
  width: 99%;
    box-shadow: none;
}
.custom_note_timeline>li>.timeline-panel:before{
  display:none;
}

.custom_note_timeline>li.timeline-inverted>.timeline-panel:after{
  display:none;
}

.custom_note_timeline>li>.timeline-badge {
  color: #fff;
  width: 15px;
  background: #fff!important;
  height: 15px;
  border: 1px solid #f6c163;
  line-height: 15px;
}

.custom_note_timeline:before {
  background-color: #f8d65e;
  content: "";
  position: absolute;
  left: 5px;
  top: 0px;
  height: 100%;
  width: 1px;
}    

.custom_note_timeline:after {
  background-color: #f6c163;
  content: "" !important;
  position: absolute !important;
  left: 1px !important;
  bottom: -2px !important;
  height: 8px !important;
  width: 8px;
  border-radius: 50%;
  border: 0 !important;
}

.custom_note_timeline .badge-pill{

  color: #404040;

}
.custom_note_timeline{
  margin-top:0!important;
}
.custom_note_timeline .timeline-body p{
  color: #676767;
  font-size: 12px;
  width:85%;
  margin-left : 10px;
  font-weight:bold;
}
.custom_note_timeline .timeline-heading {
  margin-bottom: 7px;
}
.custom_note_timeline .timeline-time{
  font-size: 11px;
  color: #757575;
  float:right;
  margin-right: 40px;
}
.custom_note_timeline .timeline-time i{
  color: #a70000;
  padding-left: 3px;
  font-size: 10px;
  cursor:pointer;
}

.custom_note_timeline .timeline-time i:hover{
  color:red;
}
.custom_note_timeline>li>.timeline-panel{
  margin:0;
   padding-bottom: 0!important;
}
.custom_note_timeline .timeline-body{

width: 100%;
float:left;
}
.scroll_note_overflow{
  max-height:350px;
  overflow:auto;
  margin-top:25px;
}


</style>

<div class="col-md-12 pd-0" >
 <div class="card mt-0">
  <div class="card-header tabheader" id="">
   <div class="col-md-12 pd-0">
    <div id="headers" style="color: #ffffff;">
     <!-- Order Info Header View -->  
     <?php $this->load->view('orderinfoheader/orderinfo'); ?>

   </div>
 </div>
</div>
<div class="card-body pd-0">
 <!-- Workflow Header View -->  
 <?php $this->load->view('orderinfoheader/workflowheader'); ?>
 <!-- <div class="expand_icon">
  <i class="fa fa-chevron-circle-down order_expand" aria-hidden="true"></i>
</div> -->
<div class="tab-content tab-space" style="padding-top: 0!important;">
  <div class="tab-pane active" id="summary">
    <input type="hidden" name="OrderUID" id="OrderUID" value="<?php echo $OrderSummary->OrderUID; ?>">
    <div class="col-md-12 scroll_note_overflow">
      <ul class="timeline custom_note_timeline">
        <?php foreach ($Notes as $key => $value) { ?>
          <li class="timeline-inverted">
            <div class="timeline-badge default">
            </div>
            <div class="timeline-panel">
              <div class="timeline-heading">
                <span style="font-size: 13px; margin-left : 10px; color: #1A73E8;float:left;"><?php echo $value->Module; ?> </span>
                <span class="timeline-time"><span class="" style="margin-right:5px;font-weight:bold;"><?php echo $value->UserName; ?></span><?php echo date('m/d/Y H:i A',strtotime($value->CreateDateTime)); ?>  </span>
              </div>
              <div class="timeline-body">
                <p><?php echo nl2br($value->Description); ?></p>
              </div>
            </div>
          </li>
        <?php } ?>
      </ul>
    </div>

    <form name="notesform" id="notesform">
      <div class="col-md-12" style="margin-top:40px;">
        <div class="row">
          <div class="col-md-3">
            <div class="form-group bmd-form-group">
              <label for="Single-note_sec" class="bmd-label-floating">Note Type</label>
              <select class="select2picker form-control note_sec"  id="Single-note_sec" name="Module" >
                <?php foreach ($WorkflowArrays as $key => $value) {
                  echo "<option value='".$value."'>".$key."</option>";
                }?>
                <option value="Parking">Parking</option>
                <option value="Others">Others</option>
              </select>
            </div> 
          </div>

          <div class="col-md-8">
            <div class="form-group">
              <textarea placeholder="Enter Notes" class="form-control Description" id="Description" name="Description" rows="1"></textarea>
            </div>
          </div>
          <div class="col-md-1">
            <div class="form-group">
              <button type="button" class="btn btn-space btn-sm btn-color btn-success save_notes_DETAILS pull-left" value="1">Save</button>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

</div>
</div>

<?php if (!empty($ExceptionList)) { ?>


 <div class="card mt-10">
   <div class="card-body">
</div>
</div>
<?php } ?>
</div>

<?php $this->load->view('orderinfoheader/workflowpopover'); ?>

<!-- Delete Document Modal -->
<div class="modal fade modal-mini modal-primary custommodal" id="deletedocument" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-small">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon-cancel-circle2"></i></button>
      </div>
      <div class="modal-body">
        <p>Are you sure want to delete this ?</p>
      </div>
      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-link No" data-dismiss="modal">No</button>
        <button type="button" class="btn btn-success btn-link Yes">Yes
          <div class="ripple-container"></div>
        </button>
      </div>
    </div>
  </div>
</div>
<!-- Delete Document Modal -->

<script src="<?php echo base_url(); ?>assets/plugins/dropify/js/dropify.js" type="text/javascript"></script>
<!-- <script src="<?php echo base_url(); ?>assets/datepicker/jquery/1.10.2/jquery.min.js"></script>

  <script src="<?php echo base_url(); ?>assets/datepicker/jquery.datetextentry.js"></script> -->
  <script src="<?php echo base_url(); ?>assets/plugins/EditableDatePicker.js"></script>
  <script src="<?php echo base_url(); ?>assets/js/formatcurrency.js?reload=1.0.1"></script>
  <script src="<?php echo base_url(); ?>assets/js/plugins/bootstrap-notify.js"></script>

  <script type="text/javascript">
   
function callselect2 () {
  $("select.select2picker").select2({
    theme: "bootstrap",
  }).focus(function () { $(this).select2('open'); });;
}

$(document).ready(function(){
  $(document).off('click', '.save_notes_DETAILS').on('click', '.save_notes_DETAILS', function (e) {
    var Description = $('.Description').val();
    var desVal = $.trim(Description);
    var form = $('#notesform')[0];
    var data = new FormData(form);
    data.append('OrderUID','<?php echo $this->uri->segment(3); ?>');
    var button =$(this); 
    if(desVal != ''){
      $.ajax({
        type: "POST",
        url: base_url + 'Notes/AddNotes',
        data: data,
        cache: false,
        processData: false,
        contentType: false,
        beforeSend: function () {
          button.attr("disabled", true);
          button.html('<i class=""fa fa-spin fa-spinner"></i> Loading ...');

        },
        success : function(data){
          $.notify({message: 'Notes Inserted'}, {type: "success",delay: 1000});
          setTimeout(function(){ 
                         location.reload();
                     }, 3000);
         
          console.log(data);
        }
      });
    } else {
      $.notify({message: 'Comment Required'}, {type: "danger",delay: 1000});
    }
  });
});


$(document).ready(function() { 
  $(window).on("load", function () {
    $(".scroll_note_overflow").animate({ 
      scrollTop: $( 
        '.scroll_note_overflow').get(0).scrollHeight 
    }, 100); 
  }); 
}); 
</script> 

