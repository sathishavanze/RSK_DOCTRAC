
<link href="<?php echo base_url(); ?>assets/css/progresccircle.css" rel="stylesheet" type="text/css" />
<style>
  .abstractorlist{
    list-style-type: none;
    text-transform: capitalize;
  }
  .abstractorlist li{
    line-height: 25px;
    font-size: 12px;
  }
  .DTFC_RightBodyWrapper{
    z-index: 9;
  }
  .onboardedtxt{
    padding: 20px 13px;
    border-radius: 50%;
    background: #cb1f5d;
    color: #fff;
  }
  .labelinfo{
    color: #3e3e3e;
    font-weight: 600;
  }
  #customerviewmodal .customerbasicdts {
    text-align: center;
    line-height: 10px;
    background: #e2145a;
    color: #ffffff;
    padding-top: 10px;
    padding-bottom: 10px;
    border-bottom: 1px solid #ececec;
  }
  #customerviewmodal .customerbasicdts p {
    text-align: left;
    line-height: 38px;
    padding: 0;
    margin: 0px;
    font-weight: 600;
  }
  .customerproductdts{
    height: 350px;
    overflow-y: scroll;
    padding: 10px;
  }

table.dataTable thead .sorting:after, table.dataTable thead .sorting_asc:after, table.dataTable thead .sorting_asc_disabled:after, table.dataTable thead .sorting_desc:after, table.dataTable thead .sorting_desc_disabled:after {
    bottom: 0px;
    right: 3em;
    content: "\2193";
}

table.dataTable thead .sorting:before, table.dataTable thead .sorting_asc:before, table.dataTable thead .sorting_asc_disabled:before, table.dataTable thead .sorting_desc:before, table.dataTable thead .sorting_desc_disabled:before {
    bottom: 0px;
    right: 2em;
    content: "\2191";
}

  
</style>

<div class="modal fade modal-lg custommodal" id="customerviewmodal" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="myModalLabel" >
  <div class="modal-dialog">
    <div class="modal-content">   
      <div class="modal-body pd-0">
        <div class="customerbasicdts">
          <div class="col-md-12">
            <div class="row">
              <div class="col-md-3">
                <img src="https://png.icons8.com/color/1600/circled-user-male-skin-type-4.png" style="width:100px;" class="img-responsive">
              </div>
              <div class="col-md-4 pad-20">
                <p><i class="icon-address-book pr-1"></i><span id="CustomerAddress"></span></p>
                <p><i class="icon-user pr-1"></i><span id="CustomerName"></span></p>
                <p><i class="icon-location3 pr-1"></i><span id="Customerlocation"></span></p>
              </div>
              <div class="col-md-4 pad-20">
                <p><i class="icon-phone2 pr-1"></i><span id="CustomerMobile"></span></p> 
                <p><i class="icon-mailbox pr-1"></i><span id="CustomerEmail"></span></p>
                <p><i class="icon-sphere pr-1"></i><span id="CustomerWebsite"></span></p>
              </div>
            </div>
          </div>
        </div>

        <div class="customerproductdts" style="padding: 10px;">
          <h5 style="margin-bottom: 5px;"> Customer Products</h5>
          <div class="material-dataTables">
           <table id="customerproductlist" class="table table-striped table-no-bordered table-hover" cellspacing="0" width="100%" style="width:100%">
             <thead>
               <tr>
                 <th>Product</th>
                 <th>Sub Product</th>
                 <th>Api</th>
               </tr>
             </thead>
             <tbody id="appendproductdata">

             </tbody>
           </table>
         </div>

         <h5 class="mt-10" style="margin-bottom: 5px;">Workflows & Templates</h5>
         <div class="material-dataTables">
           <table id="customerworkflow" class="table table-striped table-no-bordered table-hover" cellspacing="0" width="100%" style="width:100%">
             <thead>
               <tr>
                 <th>Product / Subproduct</th>
                 <th>Workflows</th>
                 <th>Optional Workflows </th>
                 <th>Templates</th>
               </tr>
             </thead>
             <tbody id="appendworkflowdata">

             </tbody>
           </table>
         </div>
         <div class="col-md-12">
          <div class="row">
            <div class="col-md-6 pd-0">
              <h5 class="mt-10" style="margin-bottom: 5px;">Private Abstractor</h5>        
              <ul id="privateabstractorlist" class="abstractorlist">                              
              </ul>
            </div>
            <div class="col-md-6  pd-0">
              <h5 class="mt-10" style="margin-bottom: 5px;">Exclude Abstractor</h5>          
              <ul  id="excludeabstractorlist" class="abstractorlist">             
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal"><i class="icon-x pr-1"></i>Close</button>
    </div>
  </div>
</div>
</div>




<div class="card mt-40 customcardbody">
  <div class="card-header card-header-danger card-header-icon card_theme_color">
    <div class="card-icon">
      <i class="icon-file-text"></i>
    </div>
    <div class="row">
      <div class="col-md-6">
        <h4 class="card-title">Client List</h4>
      </div>

      <?php if(in_array($this->RoleType, $this->config->item('Super Admin'))) { ?>
      <div class="col-md-6 text-right">
        <a href="<?php echo base_url(); ?>Customer/add" style="padding:0px 10px !important" class="btn btn-success ajaxload btn-sm mt-10 cardaddinfobtn" ><i class="pr-10 icon-plus22
        "></i>Add Client</a>
      </div>
      <?php } ?>
    </div>
  </div>
  <div class="card-body ">
    <div class="col-md-12 col-xs-12">
      <div class="material-datatables">
        <table id="customerlisttable" style="border:1px solid #ddd; white-space:nowrap;width: 100%;" class="table table-striped">
         <thead>
          <tr>
            <th  class="text-left" style="width:30px;" >S.No</th>
            <th  class="text-left">Client Name</th>
            <th  class="text-left">Address Line1</th>
            <th  class="text-left">CityName</th>
            <th  class="text-left">StateName</th>
            <th  class="text-left">Zipcode</th>
            <th  class="text-left" style="width:7%;">Action</th>
          </tr>

        </tr>
      </thead>
      <tbody>
        <?php $i=1;foreach($CustomerDetails as $row): ?>
        <tr>
          <td style="text-align: left;"><?php echo $i; ?></td>
          <td style="text-align: left;"><?php echo $row->CustomerName; ?></td>
          <td style="text-align: left;"><?php echo $row->AddressLine1.' '.$row->AddressLine2; ?></td>
          <td style="text-align: left;"><?php echo $row->CityName; ?></td>
          <td style="text-align: left;"><?php echo $row->StateName; ?></td>
          <td style="text-align: left;"><?php echo $row->ZipCode; ?></td>

          <td style="text-align: left"> 
            <span style="text-align: left;width:100%;">
              <a href="<?php echo base_url('Customer/EditCustomer/'.$row->CustomerUID);?>" class="btn btn-link btn-info btn-just-icon btn-xs ajaxload"><i class="icon-pencil"></i></a>

            </span>
          </td>
        </tr>

        <?php 
        $i++;
      endforeach; ?>
    </tbody>
  </table>
</div>
</div>
</div>
</div>




<script type="text/javascript">
  $(document).ready(function(){


   $('#customerlisttable').dataTable({   
    responsive: true,
    dom: 'Bfrtip',
    buttons: [
    'excel', 'pdf'
    ],
  });
 });

  $(document).off('change', '.status').on('change', '.status', function (e) {
    var custid = $(this).attr('data-type');
    var status = $(this).is( ':checked' ) ? 1: 0;
    $.ajax({
      type: "POST",
      url: "<?php echo base_url()?>Customer/ajax_changestatus",
      dataType: "JSON",
      data: {'custid':custid,'status':status}, 
      cache: false,
      success: function(data)
      {
        if(data['validation_error']==0)
        {
          $.notify({icon:"icon-bell-check",message:data.message},{type:"success",delay:1000 });

        } else {
          $.notify({icon:"icon-bell-check",message:data.message},{type:"danger",delay:1000 });
        }
      }
    });
  });

  $(document).off('click', '.viewinfo').on('click', '.viewinfo', function (e) {
    var CustomerUID = $(this).attr('data-CustomerUID');
    $.ajax({
      type: "POST",
      url: "<?php echo base_url()?>Customer/viewcustomer_info",
      dataType: "json",
      data: {'CustomerUID':CustomerUID}, 
      cache: false,
      success: function(data)
      {
        console.log(data)
        $('#CustomerEmail').text(data.Info['CustomerPContactEmailID']);
        $('#CustomerAddress').text(data.Info["CustomerAddress"]);
        $('#Customerlocation').text(data.Info['Customerlocation']);
        $('#CustomerName').text(data.Info['CustomerName']);
        $('#CustomerMobile').text(data.Info['CustomerPContactMobileNo']);
        $('#CustomerWebsite').text(data.Info['CustomerWebsite']);
        $('#customerviewmodal').modal('show');

        $.each(data.Products, function(index, values) {
          html = '<tr><td>'+values.ProductName+'</td><td>'+values.SubProductName+'</td><td>'+values.OrderSourceName+'</td></tr>';
          $('#appendproductdata').append(html);
        });

        $.each(data.Workflows, function(index, values) {
          html = '<tr><td>'+values.Products+'</td><td>'+values.Workflows+'</td><td>'+values.OptionalWorkflows+'</td><td>'+values.Templates+'</td></tr>';
          $('#appendworkflowdata').append(html);
        });

        $.each(data.Abstractors, function(index, values) {
          html = '<li><i class="icon-checkmark2 text-success"></i>'+values.AbstractorNames+'</li>';
          if(values.ExcludeAbstractor == 1){
            $('#excludeabstractorlist').append(html);

          }else{
            $('#privateabstractorlist').append(html);

          }
        });
      }
    });
  });
</script>







