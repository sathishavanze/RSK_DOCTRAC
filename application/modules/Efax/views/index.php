<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/lib/select2/css/select2-bootstrap4.css"/>

<!--BEGIN CONTENT-->
<div class="be-content">
  <div class="main-content container-fluid">
    <div class="panel panel-default">
      <div class="panel-body panel-border-color panel-border-color-primary">
        <div class="tab-container spinnerclass be-loading">
          <div class="be-spinner">
            <svg width="40px" height="40px" viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg">
              <circle fill="none" stroke-width="4" stroke-linecap="round" cx="33" cy="33" r="30" class="circle"></circle>
            </svg>
          </div>
          <div class="tools">
            <span onclick="fn_custom_sort();" class="icon" title="Custom Sort">
              <i class="mdi mdi-sort-amount-desc" aria-hidden="true"  ></i><i class="mdi mdi-sort-amount-asc" aria-hidden="true"></i>
            </span>
          </div>
          <div class="tab-content row">

          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="<?php echo base_url();?>assets/lib/jquery.niftymodals/dist/jquery.niftymodals.js" type="text/javascript"></script>

<script type="text/javascript">
  $.fn.niftyModal('setDefaults',{
    overlaySelector: '.modal-overlay',
    closeSelector: '.modal-close',
    classAddAfterOpen: 'modal-show',
  });
</script>