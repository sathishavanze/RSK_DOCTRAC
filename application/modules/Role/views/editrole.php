<style>
  .progress {
    height: 6px;
    margin-bottom: 5px !important;
  }
  .viewproduct   #accordion p{
    font-size: 13px !important;
    margin-bottom: 0rem  !important;
  }
  .viewproduct  .card-collapse .card-header {
    border-bottom: none !important;
    padding: 7px 10px 5px 0 !important;
  }
  .viewproduct  .addproduct{
    border:1px solid #ddd;
    padding:20px;
  }
  .viewproduct  .icon-pencil{
    cursor: pointer;
  }
  .viewproduct  .icon-close2{
    cursor: pointer;
  }
  .viewproduct .buttons-excel{
    background: #3b5998 !important;
    color: #fff !important;
  }
  .viewproduct  .buttons-pdf{
    background: #6f6f6f !important;
    color: #fff !important;
  }
  .viewproduct  .pagination>.page-item>.page-link, .pagination>.page-item>span {
    border-radius: 5px!important;
  }

  .viewproduct  .navdiv{
    padding: 5px 20px;
    border: 1px solid #e6e6e6;
    background: #fff;
  }
  .viewproduct  .breadcrumb {
    display: flex;
    flex-wrap: wrap;
    padding: 5px 0px;   
    background-color: #fff;
    margin-bottom: 0px;
  }
  .viewproduct  .breadcrumb>li>a {
    color: #333;
    font-size: 14px;
    font-weight: 400;
  }
  .viewproduct  .breadcrumb-item.active {
    color: #6c757d;
    font-size: 13px;
    font-weight: 400;
    padding-top: 2px;
  }
  .viewproduct .card{
    border-radius: 0px !important;
  }


  .showfilename{
    font-weight: 400;
    font-size:13px;
    margin-bottom: 0px !important;
    text-align: right;
  }
  .headerdiv{
    border-bottom: 1px solid #ddd;
    background: #f3f3f3;
    padding: 10px;
  }
  .instructiondivider{
    border-bottom: 1px solid #ddd;
    padding: 15px;
    margin-bottom: 20px;
  }
  .fileinput-button-upload{
    background: #fff !important;
    border: 2px dashed #ddd;
    color: black !important;
    width: 100%;
    height: 40px;    
  } 
  .fileinput-button i{
    display: block;
    font-size: 45px;
    color: #3d5b99;
  }
  .fileinput-button-upload input{
    position: absolute;
    top: 0;
    right: 0;
    margin: 0;
    opacity: 0;
    -ms-filter: 'alpha(opacity=0)';
    font-size: 20px;
    direction: ltr;
    height: 20px;
  }
  .fileinput-button input{
    position: absolute;
    top: 0;
    right: 0;
    margin: 0;
    opacity: 0;
    -ms-filter: 'alpha(opacity=0)';
    font-size: 200px;
    direction: ltr;
    height: 200px;
  }
  .fileinput-button{
    background: #fff !important;
    border: 2px dashed #ddd;
    color: black !important;
    width: 100%;
    height: 230px; 
    line-height: 80px;
  }
  .abstractordiv h5 , .excludeabstractordiv h5{
    border-bottom: 1px solid #ddd;
    padding: 10px;
    font-size: 14px;
    margin: 0px;
    font-weight: 400; 
    color: #3b5998;
    font-weight: 500; 
  }
  .headericon{
    padding: 4px;
    border: 2px solid #8e8c8c;
    border-radius: 50%;
    margin-right: 10px;
    color: #8e8c8c;
    font-size: 10px;
  }
  .selectproductdiv{  
    border-right: 1px solid #ddd;
  }
  .selectedproductheader{
    border-bottom: 1px solid #ddd;
    padding: 10px;
    margin-bottom: 20px;
  } 
  .addmastersetups{
    cursor: pointer;
    color: #11b8cc;
  }
  .viewmastersetups{
    cursor: pointer;
    color: #e66b24;
  }
  .tab{
    display: none; 
    width: 100%;
    height: 50%;
    margin: 0px auto;
  }
  .current{
    display: block;
  }
  .step {
    height: 30px;
    width: 30px; 
    cursor: pointer;
    margin: 0 2px;
    color: #fff;
    background-color: #bbbbbb;
    border: none; 
    border-radius: 50%; 
    display: block; 
    opacity: 0.8;
    padding: 5px; 
    margin-top: 15px;
  }
  .step.active {
    opacity: 1;
    background-color: #69c769;
  }
  .step.finish {
    background-color: #4CAF50; 
  }
  .error {
    color: #f00;
  }
  #myForm{
    width:100%;
  }
  .card #pricingtable tr td{
    width: 160px;
  }
  .iconverify{
    padding: 10px;
    color: #00717f;
    border-radius: 50%;
    font-size: 26px;
  }
  .pswverify{
    font-size: 20px;
    font-weight: 500;
  }
  .is-invalid {
    background-size: 100% 100%, 100% 100%;
    transition-duration: .3s;
    box-shadow: none;
    background-image: linear-gradient(to top, #f44336 2px, rgba(244, 67, 54, 0) 2px), linear-gradient(to top, #d2d2d2 1px, rgba(210, 210, 210, 0) 1px);
  }
  .hide{
    display: none;
  }

  .headericon {
   position: absolute;
   left: 13px;
 }
 .sidetext {
   font-weight: 400;
   padding-bottom: 8px;
   color: #797979;
 }
 .float-right
 {
  margin-top: 15px;
}
#IconHead
{
  border: 1px solid #999;
  padding: 6px;
  border-radius: 50%;
}
.togglebutton label input[type=checkbox]:checked+.toggle {
  background-color: rgb(233, 114, 30);
}

.ChkBorder {
  flex: 0 0 100%;
  max-width: 100%;
  border-bottom: 1px solid #d9d9d9 !important;
}
.checklist{
  box-shadow: none;
}
</style>
<?php $fieldSection = $this->Role_Model->GetFieldSection(); ?>
<div class="card mt-40" id="Orderentrycard">
  <div class="card-header card-header-danger card-header-icon">
    <div class="card-icon">Update Role
    </div>
  </div>

  <!-- <div class="card-header text-center">    
    <div class="row">
      <div class="col-md-8">  
        <h4 class="card-title">
          <span style="font-size:15px;font-weight:600;"></span>
        </h4>
        <h5 class="card-description"></h5>
      </div>

    </div>
  </div> -->
  <div class="card-body">
    <div class="tab-content">
      <div class="tab-pane active" id="info">

        <form name="frm_role" id="frm_role" class="frm_role">

          <input type="hidden" name="RoleUID" id="RoleUID" value="<?php echo $RoleDetails->RoleUID;?>" ?> 
          <div class="row">    
            <div class="col-md-3">  
              <div class="form-group">
                <label for="RoleName" class="bmd-label-floating">Role Name</label>
                <input type="text" class="form-control" id="RoleName" name="RoleName" value="<?php echo $RoleDetails->RoleName;?>" />
              </div>
            </div>

            <div class="col-md-3">  
              <div class="form-group  bmd-form-group">
                <label for="RoleType" class="bmd-label-floating">Role Type</label>
                <select class="select2picker form-control"  id="RoleTypeUID"  name="RoleTypeUID" style="width: 100%;">
                  <option value=""></option>
                  <?php foreach ($RoleTypeDetails as $row)  {
                   if($row->RoleTypeUID == $RoleDetails->RoleTypeUID)
                    { ?>  
                      <option value="<?php echo $row->RoleTypeUID; ?>" selected><?php echo $row->RoleTypeName; ?></option>
                    <?php }else{ ?>
                      <option value="<?php echo $row->RoleTypeUID; ?>">
                        <?php echo $row->RoleTypeName; ?></option>
                      <?php }
                      ?>
                    <?php } ?> 
                  </select>
                </div>          
              </div>
              <div class="col-md-3">  
                <div class="form-group  bmd-form-group">
                 <label for="DefaultScreen" class="bmd-label-floating">Default Screen</label>
                 <select class="select2picker form-control"  id="DefaultScreen"  name="DefaultScreen" style="width: 100%;">
                  <option value=""></option>
                  <?php foreach ($Resources as $row)  {
                   if($row->controller == $RoleDetails->DefaultScreen)
                    { ?>  
                      <option value="<?php echo $row->controller; ?>" selected><?php echo $row->FieldName; ?></option>
                    <?php }else{ ?>
                      <option value="<?php echo $row->controller; ?>">
                        <?php echo $row->FieldName; ?></option>
                      <?php }
                      ?>
                    <?php } ?> 
                  </select>
                </div>          
              </div>

              <?php if(in_array($this->RoleType, $this->config->item('Super Admin'))) { ?>
                <div class="col-md-2">  
                  <div class="form-group  bmd-form-group">
                   <label for="CustomerUID" class="bmd-label-floating">Client</label>
                   <select class="select2picker form-control"  id="CustomerUID"  name="CustomerUID" style="width: 100%;">
                    <option value=""></option>
                    <?php foreach ($CustomerDetails as $row)  {
                     if($row->CustomerUID == $RoleDetails->CustomerUID)
                      { ?>  
                        <option value="<?php echo $row->CustomerUID; ?>" selected><?php echo $row->CustomerName; ?></option>
                      <?php }else{ ?>
                        <option value="<?php echo $row->CustomerUID; ?>">
                          <?php echo $row->CustomerName; ?></option>
                        <?php }
                        ?>
                      <?php } ?> 
                    </select>
                  </div>          
                </div>
              <?php } ?>

              <div class="col-md-3">
                <div class="togglebutton">
                  <label class="label-color" style="margin-top: 14pt"> Active
                    <input type="checkbox" id="Active" name="Active" class="Active" <?php if($RoleDetails->Active == 1){ echo "checked"; } ?>>
                    <span class="toggle"></span>
                  </label>
                </div>
              </div> 

            </div>
            <div class="mt-30 checklist" style="margin-left: 17px !important;">
              <?php                   
              foreach ($fieldSection as $key => $field) 
              {
                $Resource = $this->Role_Model->GetResource($field->FieldSection); 
                $spcFieldSec = preg_replace('/\s+/', '', $field->FieldSection);

                $Incremented = 0;
                foreach ($Resource as $r) 
                {                                            

                  $checked = $this->Role_Model->GetRoleResourceByRoleUID_ResourceUID($RoleDetails->RoleUID, $r->ResourceUID);
                  if($checked == 'checked')
                  {
                    $Incremented++;
                  }

                }

                $countRsc = count($Resource);

                $chk = '';
                if($Incremented == $countRsc)
                {
                  $chk = "checked";
                }
                ?>
                <div class="row">
                  <div class = "col-md-12">
                    <div class="form-check">
                      <label class="form-check-label">
                        <input class="form-check-input" type="checkbox" id="<?php echo $spcFieldSec; ?>Permission" data-incre="<?php echo $spcFieldSec; ?>" <?php echo $chk; ?>> 
                        <span class="form-check-sign">
                          <span class="check"></span>
                        </span>
                        <h6 style="font-weight: 300; padding-bottom: 5px;color:#403e3e;font-weight: bold;"><?php echo $field->FieldSection; ?></h6>
                      </label>
                    </div>
                  </div>
                </div> 
                <div class = "row" style="margin-top: -10px !important; padding-bottom: 10px; margin-bottom: 20px; border-bottom: 1px dashed #cccccc;">
                  <?php 
                  foreach($Resource as $value)
                  {
                    $checked = $this->Role_Model->GetRoleResourceByRoleUID_ResourceUID($RoleDetails->RoleUID, $value->ResourceUID);
                    ?>
                    <div class="col-sm-4"> 
                      <div class="form-check dynamicCheckedList">
                        <label class="form-check-label Dashboardlable" for="Workflow<?php echo $value->ResourceUID; ?>" style="color: teal">
                          <input class="form-check-input allworkflow <?php echo $spcFieldSec; ?>" id="Workflow<?php echo $value->ResourceUID; ?>" type="checkbox" value="<?php echo $value->ResourceUID; ?>" name="WorkflowPermission[<?php echo $value->ResourceUID; ?>]"  data-attr="<?php echo $spcFieldSec; ?>" <?php echo $checked; ?>> <?php echo $value->FieldName; ?>
                          <span class="form-check-sign">
                            <span class="check"></span>
                          </span>
                        </label>
                      </div>
                    </div>
                    <?php 
                  }
                  ?>
                </div>
                <?php
              }
              ?>
            </div>

            <h5 class="rowheading">Queue</h5>

            <div class="row">
              <div class="col-md-3">
                <div class="form-group bmd-form-group">
                  <label for="OrderQueue" class="bmd-label-floating"></label>
                  <select class="select2picker form-control OrderQueue"  id="OrderQueue" name="OrderQueue">
                    <option value="1" <?php if($RoleDetails->OrderQueue == 1){ echo "selected"; }?>>All Orders</option>
                    <option value="2" <?php if($RoleDetails->OrderQueue == 2){ echo "selected"; }?>>My Orders</option>
                  </select>
                </div> 
              </div>
            </div>

            <div class="roleborder"></div>

            <h5 class="rowheading">Other</h5>

            <div class="row col-md-12">
              <div class="col-sm-4">
                <div class="form-check dynamicCheckedList">
                  <label class="form-check-label">
                    <input class="form-check-input inputcheckbox" type="checkbox" name="IsAssigned" id="IsAssigned" data-incre="IsAssigned" value ="<?php echo $RoleDetails->IsAssigned; ?>" <?php if($RoleDetails->IsAssigned == 1){ echo "checked"; } ?>> 
                    <span class="form-check-sign">
                      <span class="check"></span>
                    </span>
                    <h6 style="font-weight: 300; padding-bottom: 5px;color:#403e3e;font-weight: bold;">Assign/Re-Assign</h6>
                  </label>
                </div>
              </div>

              <div class="col-sm-4">

                <div class="form-check dynamicCheckedList">
                  <label class="form-check-label">
                    <input class="form-check-input inputcheckbox" type="checkbox" name="IsReverseEnabled" id="IsReverseEnabled" data-incre="IsReverseEnabled" value ="<?php echo $RoleDetails->IsReverseEnabled; ?>" <?php if($RoleDetails->IsReverseEnabled == 1){ echo "checked"; } ?>> 
                    <span class="form-check-sign">
                      <span class="check"></span>
                    </span>
                    <h6 style="font-weight: 300; padding-bottom: 5px;color:#403e3e;font-weight: bold;">Reverse</h6>
                  </label>
                </div>
              </div>

              <!-- restrict self assign -->
              <div class="col-sm-4">
                <div class="form-check">
                  <label class="form-check-label">
                    <input class="form-check-input inputcheckbox" type="checkbox" name="IsSelfAssignEnabled" id="IsSelfAssignEnabled" data-incre="IsSelfAssignEnabled" value ="<?php echo $RoleDetails->IsSelfAssignEnabled; ?>" <?php if($RoleDetails->IsSelfAssignEnabled == 1){ echo "checked"; } ?>> 
                    <span class="form-check-sign">
                      <span class="check"></span>
                    </span>
                    <h6 style="font-weight: 300; padding-bottom: 5px;color:#403e3e;font-weight: bold;">Self Assign</h6>
                  </label>
                </div>
              </div>
              <!-- restrict self assign end -->

              <!-- Lock Expiration -->
              <div class="col-sm-4">
                <div class="form-check">
                  <label class="form-check-label">
                    <input class="form-check-input inputcheckbox" type="checkbox" name="IsLockExpirationRestricted" id="IsLockExpirationRestricted" data-incre="IsLockExpirationRestricted" value ="<?php echo $RoleDetails->IsLockExpirationRestricted; ?>" <?php if($RoleDetails->IsLockExpirationRestricted == 1){ echo "checked"; } ?>> 
                    <span class="form-check-sign">
                      <span class="check"></span>
                    </span>
                    <h6 style="font-weight: 300; padding-bottom: 5px;color:#403e3e;font-weight: bold;">Lock Expiration & Milestone Restriction</h6>
                  </label>
                </div>
              </div>
              <!-- Lock Expiration end -->

               <!-- GET NEXT ORDER Assign Button  -->
              <div class="col-sm-4">
                <div class="form-check">
                  <label class="form-check-label">
                    <input class="form-check-input inputcheckbox" type="checkbox" name="AssignGetNextOrder" id="AssignGetNextOrder" data-incre="AssignGetNextOrder" value ="<?php echo $RoleDetails->AssignGetNextOrder; ?>" <?php if($RoleDetails->AssignGetNextOrder == 1){ echo "checked"; } ?>> 
                    <span class="form-check-sign">
                      <span class="check"></span>
                    </span>
                    <h6 style="font-weight: 300; padding-bottom: 5px;color:#403e3e;font-weight: bold;">GET NEXT ORDER ASSIGN</h6>
                  </label>
                </div>
              </div>
              <!-- restrict self assign end -->

            </div>



            <div class="roleborder"></div>




           
            <div class="ml-auto text-right">
              <button type="submit" class="btn btn-fill btn-back btn-wd Back" name="Back" id="Back"><i class="icon-arrow-left15 pr-10 Back"></i>Back</button>
              <button type="submit" class="btn btn-fill btn-update btn-wd BtnSaveRole" name="BtnSaveRole" id="BtnSaveRole"><i class="icon-floppy-disk pr-10"></i>Update Role</button>
            </div>
            <div class="clearfix"></div>
          </form>

        </div>
      </div>
    </div>
  </div>

  <script src="<?php echo base_url(); ?>assets/js/multi-form.js" type="text/javascript"></script>
  <script src="<?php echo base_url(); ?>assets/js/plugins/jquery.bootstrap-wizard.js"></script>
  <script src="<?php echo base_url(); ?>assets/js/plugins/sweetalert2.js"></script>
  <script src="<?php echo base_url(); ?>assets/js/customer.js" type="text/javascript"></script>

  <script type="text/javascript">

    $(document).ready(function(){ 

      $('.inputcheckbox').on('click',function(){  
        if(this.checked){
          $(this).val(1);
          this.checked = true;    
        }else{
          $(this).val(0);
          this.checked = false;
        }
      });

      <?php foreach ($fieldSection as $value) { 
        $pregTrim = '';
        $explodeIDFields = preg_replace('/\s+/', '', $value->FieldSection).'Permission';
        $explodeClassFields = preg_replace('/\s+/', '',$value->FieldSection);
        ?>
        $('#<?php echo $explodeIDFields; ?>').on('click',function(){  
          if(this.checked){
            $('.<?php echo $explodeClassFields; ?>').each(function(){ 
              this.checked = true;
            });
          }else{
           $('.<?php echo $explodeClassFields; ?>').each(function(){ 
            this.checked = false;
          });
         }
       });

        $('.<?php echo $explodeClassFields; ?>').on('click',function(){ 
          var totalelements = $('.<?php echo $explodeClassFields; ?>').length;
          var totalelementscheecked = $('.<?php echo $explodeClassFields; ?>:checked').length;
          if (totalelements == totalelementscheecked) {
            if(this.checked){
              $('#<?php echo $explodeIDFields; ?>').each(function(){ 
                this.checked = true;
              });
            }
          }
          else{
           $('#<?php echo $explodeIDFields; ?>').each(function(){ 
            this.checked = false;
          });
         }
       });

      <?php } ?>

      /*  $(".checklist input[type='checkbox']").click(function(){   
        var wclass  = $(this).attr("data-incre");
        //if(wclass == 'USER SETUP'){
          var spaceSplit = wclass.split(' '); 
          wclass = spaceSplit[0];
          //}
          if ($(this).prop('checked')==true){ 
           $("."+ wclass).attr("checked" , true);
         }
         else{
           $("."+ wclass).attr("checked" , false);
         }
       });*/

       $(document).on('click','.Back',function()
       {
        setTimeout(function(){ 

          triggerpage('<?php echo base_url();?>Role');

        },50);
      });

       $(document).off('click','.BtnSaveRole').on('click','.BtnSaveRole', function(e) {
        var formdata = $('#frm_role').serialize();
        button = $(this);
        button_val = $(this).val();
        button_text = $(this).html();
        $.ajax({
          type: "POST",
          url: "<?php echo base_url('Role/UpdateRole'); ?>",
          data: formdata,
          dataType:'json',
          beforeSend: function () {
            button.prop("disabled", true);
            button.html('Loading ...');
            button.val('<i class="fa fa-spin fa-spinner"></i> Saving...');
          },
          success: function (response) {
            if(response.Status == 0)
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
             setTimeout(function(){ 

              triggerpage('<?php echo base_url();?>Role');

            }, 3000);
           }
           else
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
              console.log(k);
              $('#'+k).addClass("is-invalid").closest('.form-group').removeClass('has-success').addClass('has-danger');
              $('#'+ k +'.select2picker').next().find('span.select2-selection').addClass('errordisplay');

            });
          }
          button.html(button_text);
          button.val(button_val);
          button.prop('disabled',false);
        }
      });
      });

     });
   </script>







