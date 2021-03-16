
<style>

	#cropImagePop .modal-dialog{
		max-width: 310px;
	}
	#cropImagePop .modal-content{
		overflow-y: auto !important;
	}
	.avatar-upload {
		position: relative;
		max-width: 140px;
		margin: 0px auto;
	}
	.avatar-upload .avatar-edit input {
		display: none;
	}
	.avatar-upload .avatar-edit input + label {
		display: inline-block;
		width: 25px;
		height: 25px;
		margin-bottom: 0;
		border-radius: 100%;
		background: #FFFFFF;
		border: 1px solid transparent;
		box-shadow: 0px 2px 4px 0px rgba(0, 0, 0, 0.12);
		cursor: pointer;
		font-weight: normal;
		transition: all 0.2s ease-in-out;
	}
	.avatar-upload .avatar-edit input + label:hover {
		background: #f1f1f1;
		border-color: #d6d6d6;
	}
	.avatar-upload .avatar-edit input + label:after {
		content: "\f040";
		font-family: 'FontAwesome';
		color: #757575;
		position: absolute;
		top: 10px;
		left: 0;
		right: 0;
		text-align: center;
		margin: auto;
		line-height: 8px;
	}
	.avatar-upload .avatar-preview {
		width: 75px;
		height: 75px;
		position: relative;
		border-radius: 100%;
		border: 1px solid #F8F8F8;
		box-shadow: 0px 2px 4px 0px rgba(0, 0, 0, 0.1);
		margin : 0 auto;
	}
	.avatar-upload .avatar-preview > div {
		width: 100%;
		height: 100%;
		border-radius: 100%;
		background-size: cover;
		background-repeat: no-repeat;
		background-position: center;
	}
	.imagePreview img{
		height: 75px;
		border-radius: 50%;
	}
	.avatar-upload .avatar-edit {
		position: absolute;
		right: 21px;
		z-index: 1;
		top: 0px;
	}
	.image-size-label {
		margin-top: 10px;
	}
	#result {
		margin-top: 10px;
		width: 900px;
	}
	#result-data {
		display: block;
		overflow: hidden;
		white-space: nowrap;
		text-overflow: ellipsis;
		word-wrap: break-word;
	}
	label.cabinet{
		display: block;
		cursor: pointer;
	}
	label.cabinet input.file{
		position: relative;
		height: 100%;
		width: auto;
		opacity: 0;
		-moz-opacity: 0;
		filter:progid:DXImageTransform.Microsoft.Alpha(opacity=0);
		margin-top:-30px;
	}
	#upload-demo{
		width: 250px;
		height: 250px;
		padding-bottom:25px;
	}
	figure figcaption {
		position: absolute;
		bottom: 0;
		color: #fff;
		width: 100%;
		padding-left: 9px;
		padding-bottom: 5px;
		text-shadow: 0 0 10px #000;
	}

</style>



<link href="<?php echo base_url();?>assets/css/croppie.min.css" rel="stylesheet" type="text/css" />

<div class="modal fade custommodal" id="cropImagePop" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">

				</h4>
			</div>
			<div class="modal-body">
				<div id="upload-demo" class="center-block"></div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="button" id="cropImageBtn" class="btn btn-primary">Crop</button>
			</div>
		</div>
	</div>
</div>





<!-- New Form -->
<form name="frm_customer" id="frm_customer">
	<input type="hidden" name="CustomerUID" id="CustomerUID" value="">
	<div class="mt-0">	
		<h4 class="formdivider"><i class="icon-checkmark4 headericon"></i>Client Info</h4> 
		<div class="row">   
			<div class="col-md-10"> 
				<div class="row">
					<div class="col-md-3">
						<div class="form-group bmd-form-group">
							<label for="CustomerCode" class="bmd-label-floating">Client Number </label>
							<input type="text" class="form-control" id="CustomerCode" name="CustomerCode" />
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group bmd-form-group">
							<label for="CustomerName" class="bmd-label-floating">Client Name <span class="mandatory"></span></label>
							<input type="text" class="form-control" id="CustomerName" name="CustomerName" />
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>



	<div class="mt-10" >
		<h4  class="formdivider"><i class="icon-checkmark4 headericon"></i>Address</h4> 
		<div class="col-md-12 pd-0">
			<div class="formdiv row">
				<div class="col-md-3">
					<div class="form-group bmd-form-group">
						<label for="AddressLine1" class="bmd-label-floating">AddressLine1 <span class="mandatory"></span></label>
						<input type="text" class="form-control" id="AddressLine1" name="AddressLine1" />
					</div>
				</div>


				<div class="col-md-3">
					<div class="form-group bmd-form-group">
						<label for="AddressLine2" class="bmd-label-floating">AddressLine2</label>
						<input type="text" class="form-control" id="AddressLine2" name="AddressLine2" />
					</div>
				</div>

				<div class="col-md-3">
					<div class="form-group bmd-form-group">
						<label for="zipcode" class="bmd-label-floating">ZipCode<span class="mandatory"></span></label>
						<input type="text" class="form-control" id="ZipCode" name="ZipCode" required>
						<span data-modal="zipcode-form" class="label label-success label-zip md-trigger" id="zipcode" style="display: none;">Add Zipcode</span>
					</div> 
				</div>
				<div class="col-md-3">
					<div class="form-group bmd-form-group">
						<label for="username" class="bmd-label-floating">CityName<span class="mandatory"></span></label>
						<input type="text" class="form-control" id="CityName" name="CityName" />
					</div>
				</div>



			</div>
			<div class="row mt-10">

				<div class="col-md-3">
					<div class="form-group bmd-form-group">
						<label for="username" class="bmd-label-floating">StateName<span class="mandatory"></span></label>
						<input type="text" class="form-control" id="StateName" name="StateName" />
					</div>
				</div>

			</div>
		</div>
	</div>

	<div class="mt-10">
		<h4  class="formdivider"><i class="icon-checkmark4 headericon"></i>Contact</h4>
		<div class="col-md-12 pd-0">
			<div class="formdiv row">

				<div class="col-md-3">
					<div class="form-group bmd-form-group">
						<label for="OfficeNo" class="bmd-label-floating">OfficeNo</label>
						<input type="text" class="form-control" id="OfficeNo" name="OfficeNo" />
					</div> 
				</div>

				<div class="col-md-3">
					<div class="form-group bmd-form-group">
						<label for="faxNo" class="bmd-label-floating">Fax Number </label>
						<input type="text" class="form-control" id="FaxNo" name="FaxNo" />
					</div> 
				</div>

				<div class="col-md-3">
					<div class="form-group bmd-form-group">
						<label for="CustomerEmail" class="bmd-label-floating">Email </label>
						<input type="text" class="form-control" id="CustomerEmail" name="CustomerEmail" />
					</div> 
				</div>

			</div>
		</div>
	</div>

	<div class="mt-10">
		<h4  class="formdivider"><i class="icon-checkmark4 headericon"></i> Other</h4>
		<div class="col-md-12 pd-0">
			<div class="formdiv row">
				<div class="col-md-3" >
					<div class="form-group bmd-form-group">
						<label for="ProductUID" class="bmd-label-floating">DefaultChecklistView <span style="color: red"> *</span></label>
						<select class="form-control select2picker"  id="DefaultChecklistView"  name="DefaultChecklistView">
							<option value="Show Problem Identified" selected>Show Issue(s)</option>
							<option value="Show All Checklist">Show All Checklist</option>
						</select>
					</div>
				</div>
				<div class="col-md-3" >
					<div class="form-group bmd-form-group">
						<label for="ProductUID" class="bmd-label-floating">Prescreen Checklist<span style="color: red"> *</span></label>
						<select class="form-control select2picker"  id="PrescreenChecklist"  name="PrescreenChecklist">
							<option value="All">All</option>
							<option value="Prescreen" selected>Prescreen</option>
						</select>
					</div>
				</div>

				<div class="col-md-3">
					<div class="form-group bmd-form-group">
						<label for="LoanNumberValidation" class="bmd-label-floating">Loan Number Validation (in Numbers)</label>
							<input type="number"  onkeyup="if(this.value<0){this.value= this.value * -1}" class="form-control" id="LoanNumberValidation"  value="" name="LoanNumberValidation" />
					</div> 
				</div>

			</div>
		</div>
	</div>


	<div class="mt-10">
		<div class="col-md-12 pd-0">
			<div class="formdiv row">
			
					<div class="col-md-3" >
						<div class="">
							
							<label for="Rest_Date" class="bmd-label-floating">Due Restrict Date</label>

							<input type="text" class="form-control NextPaymentDueRestriction" id="NextPaymentDueRestriction" autocomplete="off" name="NextPaymentDueRestriction" placeholder="Due Restricted Date" readonly>
						</div>
					</div> 


<!--
			<div class="formdiv row">
	-->
				<div class="col-md-3" >
					<div class="togglebutton">
						<label class="label-color"> Enable Work-Up
							<input type="checkbox" id="EnableWorkupOption" name="EnableWorkupOption" class="EnableWorkupOption">
							<span class="toggle"></span>
						</label>
					</div>
				</div>

			</div>

		</div>
	</div>


	<div class="ml-auto text-right">
		<a href="Customer" class="btn btn-fill btn-danger btn-wd btn-back" name="UpdateCustomer"><i class="icon-arrow-left8 pr-10"></i> Back</a>
		<button type="submit" class="btn btn-fill btn-success btn-wd btn-save" name="AddCustomer" id="AddCustomer"><i class="icon-floppy-disk pr-10"></i>Save</button>
	</div>
	<div class="clearfix"></div>
</form>


<script type="text/javascript">


$(document).ready(function() {

    $("#NextPaymentDueRestriction").multiDatesPicker({
	dateFormat: 'mm/dd/yy',
	defaultDate: 'today'
});


$.datepicker._selectDateOverload = $.datepicker._selectDate;
$.datepicker._selectDate = function (id, dateStr) {
	var target = $(id);
	var inst = this._getInst(target[0]);
	if (target[0].multiDatesPicker != null) {
		inst.inline = true;
		$.datepicker._selectDateOverload(id, dateStr);
		inst.inline = false;	        	
		target[0].multiDatesPicker.changed = false;
	} else {
		$.datepicker._selectDateOverload(id, dateStr);
		target.multiDatesPicker.changed = false;
	}
	this._updateDatepicker(inst);
}; 



});



  //]]></script>



<script type="text/javascript" src="<?php echo base_url();?>assets/js/cropiee.js"> </script>
<script type="text/javascript">


	function Check() {
		var isChecked = $("#ParentCompany").is(":checked");
		if (isChecked) {
			$("#ShowParentCompany").css("visibility" , "hidden");
		} else {
			$("#ShowParentCompany").css("visibility" , "visible");
		}
	}

	$(document).ready(function() {


			$(".selectpicker").selectpicker();
			$("select.select2picker").select2({
				//tags: false,
				theme: "bootstrap",
			});



		$("#ShowParentCompany").css("visibility" , "hidden");

		var isChecked = $("#ParentCompany").is(":checked");
		if (isChecked) {
			$("#ShowParentCompany").css("visibility" , "hidden");
		} else {
			$("#ShowParentCompany").css("visibility" , "visible");
		}


    $(document).off('submit','#frm_customer').on('submit','#frm_customer', function(e) {

    	e.preventDefault();
    	e.stopPropagation();
      var formdata = new FormData($('#frm_customer')[0]);
      button = $('#AddCustomer');
      button_val = $('#AddCustomer').val();
      button_text = $('#AddCustomer').html();
      $.ajax({
        type: "POST",
        url: "<?php echo base_url('Customer/AddCustomerDetails'); ?>",
        data: formdata,
        processData: false,
        contentType: false,
        dataType:'json',
        beforeSend: function () {
         button.prop("disabled", true);
         button.html('<i class="fa fa-spin fa-spinner"></i> Loading ...');
         button.val('<i class="fa fa-spin fa-spinner"></i> Saving...');

       },

       success: function (response) {


        if(response.validation_error == 1)
        {
          $.notify(
          {
            icon:"icon-bell-check",
            message:response.message
          },
          {
            type:"danger",
            delay:1000 
          });



          $.each(response, function(k, v) {

            $('#'+k).addClass("is-invalid").closest('.form-group').removeClass('has-success').addClass('has-danger');
            $('#'+ k +'.select2picker').next().find('span.select2-selection').addClass('errordisplay');

          });
        }
        else
        {
          $.notify(
          {
            icon:"icon-bell-check",
            message:response.message
          },
          {
            type:"success",
            delay:1000 
          }); 
          triggerpage('Customer/EditCustomer/'+response.CustomerUID);

        }
        button.html(button_text);
        button.val(button_val);
        button.prop('disabled',false);

      }
    });

    });


     $('#ZipCode').change(function(event) {

    zipcode = $(this).val();

    $.ajax({
      type: "POST",
      url: 'CommonController/GetZipCodeDetails',
      data: {'Zipcode':zipcode}, 
      dataType:'json',
      cache: false,
      success: function(data)
      {
        $('#CityName').empty();
        $('#StateName').empty();

        if(data.success == 1){

          $('#CityName').val(data['City'][0]['CityName']).trigger('change');
          $('#StateName').val(data['State'][0]['StateCode']).trigger('change');

          $('#StateName').parent().addClass('is-dirty');
          $('#CityName').parent().addClass('is-dirty');

        }
        callselect2();

      },
      error: function (jqXHR, textStatus, errorThrown) {

        console.log(errorThrown);

      },
      failure: function (jqXHR, textStatus, errorThrown) {

        console.log(errorThrown);

      },
    });
  });


		function readURL(input) {
			if (input.files && input.files[0]) {
				var reader = new FileReader();
				reader.onload = function(e) {
					$('.imagePreview').css('background-image', 'url('+e.target.result +')');
					$('.imagePreview').hide();
					$('.imagePreview').fadeIn(650);
				}
				reader.readAsDataURL(input.files[0]);
			}
		}
		$("#imageUpload").change(function() {
			readURL(this);
		});


		var $uploadCrop,
		tempFilename,
		rawImg,
		imageId;
		function readFile(input) {
			if (input.files && input.files[0]) {
				var reader = new FileReader();
				reader.onload = function (e) {
					$('.upload-demo').addClass('ready');
					$('#cropImagePop').modal('show');
					rawImg = e.target.result;
				}
				reader.readAsDataURL(input.files[0]);
			}
			else {
				swal("Sorry - you're browser doesn't support the FileReader API");
			}
		}

		$uploadCrop = $('#upload-demo').croppie({
			viewport: {
				width: 100,
				height: 100,
			},
			enforceBoundary: false,
			enableExif: true
		});
		$('#cropImagePop').on('shown.bs.modal', function(){
							// alert('Shown pop');
							$uploadCrop.croppie('bind', {
								url: rawImg
							}).then(function(){
								//console.log('jQuery bind complete');
							});
						});

		$('.item-img').on('change', function () { imageId = $(this).data('id'); tempFilename = $(this).val();
			$('#cancelCropBtn').data('id', imageId); readFile(this); });



		$('#cropImageBtn').on('click', function (ev) {
			$uploadCrop.croppie('result', {
				type: 'base64',
				format: 'jpeg',
				size: {width: 100, height:100}
			}).then(function (resp) {
				$('#item-img-output').attr('src', resp);
			//$("#file_photo").val(resp);
			$('#cropImagePop').modal('hide');
		});
		});




	});
</script>
