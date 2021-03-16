<style type="text/css">
.pd-btm-0{
	padding-bottom: 0px;
}

.margin-minus8{
	margin: -8px;
}

.mt--15{
	margin-top: -15px;
}

.bulk-notes
{
	list-style-type: none
}
.bulk-notes li:before
{
	content: "*  ";
	color: red;
	font-size: 15px;
}

.nowrap{
	white-space: nowrap
}

.table-format > thead > tr > th{
	font-size: 12px;
}
</style>
<div class="card" id="Orderentrycard">
	<div class="card-header card-header-danger card-header-icon">
		<div class="card-icon">State
		</div>
	</div>
	<div class="card-body">
				<div class="text-right">
	           		<a href="javascript:void(0);" class="btn btn-primary" data-toggle="modal" data-target="#Modal_Add"><span class="fa fa-plus"></span> Add New</a></div>
				</div>

   <div class="material-datatables">
      <table id="MaritalTableList" style="border:1px solid #ddd; white-space:nowrap;width: 100%;" class="table table-striped table-bordered table-hover order-column">
         <thead>
            <tr>
               <th  class="text-center">StateCode</th>
               <th  class="text-center">StateName </th>
               <th  class="text-center">FIPSCode</th>
               <th  class="text-center">StateEmail</th>
               <th  class="text-center">StateWebsite</th>
                <th  class="text-center">StatePhoneNumber</th>
                 <th  class="text-center">Action</th>

            </tr>
         </thead>
         <tbody id="show_data">
            
         </tbody>
      </table>
   </div>

	</div>
</div>
 <form>
            <div class="modal fade custommodal" id="Modal_Add" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add New State</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label">State Code</label>
                            <div class="col-md-10">
                              <input type="text" name="StateCode" id="StateCode" class="form-control" placeholder="StateCode">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label">State Name</label>
                            <div class="col-md-10">
                              <input type="text" name="StateName" id="StateName" class="form-control" placeholder="StateName">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label">FIP Code</label>
                            <div class="col-md-10">
                              <input type="text" name="FIPSCode" id="FIPSCode" class="form-control" placeholder="FIPSCode">
                            </div>
                        </div>
                 
                  <div class="form-group row">
                            <label class="col-md-2 col-form-label">State Email</label>
                            <div class="col-md-10">
                              <input type="text" name="StateEmail" id="StateEmail" class="form-control" placeholder="StateEmail">
                            </div>
                        </div>
                   <div class="form-group row">
                            <label class="col-md-2 col-form-label">State Website</label>
                            <div class="col-md-10">
                              <input type="text" name="StateWebsite" id="StateWebsite" class="form-control" placeholder="StateWebsite">
                            </div>
                        </div>
                          <div class="form-group row">
                            <label class="col-md-2 col-form-label">StatePhoneNumber</label>
                            <div class="col-md-10">
                              <input type="text" name="StatePhoneNumber" id="StatePhoneNumber" class="form-control" placeholder="StatePhoneNumber">
                            </div>
                        </div>
                 
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" type="submit" id="btn_save" class="btn btn-primary">Save</button>
                  </div>
                </div>
              </div>
            </div>
            </form>
               <form>
            <div class="modal fade" id="Modal_Edit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit State</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label">StateCode</label>
                            <div class="col-md-10">
                              <input type="text" name="StateCode_edit" id="StateCode_edit" class="form-control" placeholder="StateCode" readonly>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label">StateName</label>
                            <div class="col-md-10">
                              <input type="text" name="StateName_edit" id="StateName_edit" class="form-control" placeholder="Product Name">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label">FIPSCode</label>
                            <div class="col-md-10">
                              <input type="text" name="FIPSCode_edit" id="FIPSCode_edit" class="form-control" placeholder="FIPSCode">
                            </div>
                        </div>
                           <div class="form-group row">
                            <label class="col-md-2 col-form-label">StateEmail</label>
                            <div class="col-md-10">
                              <input type="text" name="StateEmail_edit" id="StateEmail_edit" class="form-control" placeholder="StateEmail">
                            </div>
                        </div>
                         <div class="form-group row">
                            <label class="col-md-2 col-form-label">StateWebsite</label>
                            <div class="col-md-10">
                              <input type="text" name="StateWebsite_edit" id="StateWebsite_edit" class="form-control" placeholder="StateWebsite">
                            </div>
                        </div>
                         <div class="form-group row">
                            <label class="col-md-2 col-form-label">StateWebsite</label>
                            <div class="col-md-10">
                              <input type="text" name="StateWebsite_edit" id="StateWebsite_edit" class="form-control" placeholder="StateWebsite">
                            </div>
                        </div>
                          <div class="form-group row">
                            <label class="col-md-2 col-form-label">StatePhoneNumber</label>
                            <div class="col-md-10">
                              <input type="text" name="StatePhoneNumber_edit" id="StatePhoneNumber_edit" class="form-control" placeholder="StatePhoneNumber">
                            </div>
                        </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" type="submit" id="btn_update" class="btn btn-primary">Update</button>
                  </div>
                </div>
              </div>
            </div>
            </form>





<script src="<?php echo base_url(); ?>assets/js/CommonAjax.js" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo base_url().'assets/js/jquery-3.2.1.js'?>"></script>
<script type="text/javascript" src="<?php echo base_url().'assets/js/bootstrap.js'?>"></script>
<script type="text/javascript" src="<?php echo base_url().'assets/js/jquery.dataTables.js'?>"></script>
<script type="text/javascript" src="<?php echo base_url().'assets/js/dataTables.bootstrap4.js'?>"></script>

<script type="text/javascript">
  $(document).ready(function(){
    show_product(); //call function show all product
    
    $('#mydata').dataTable();
     
    //function show all product
    function show_product(){
        $.ajax({
            type  : 'ajax',
            url   : '<?php echo site_url('state/product_data')?>',
            async : false,
            dataType : 'json',
            success : function(data){
                var html = '';
                var i;
                for(i=0; i<data.length; i++){
                    html += '<tr>'+
                          '<td>'+data[i].StateCode+'</td>'+
                            '<td>'+data[i].StateName+'</td>'+
                            '<td>'+data[i].FIPSCode+'</td>'+
                             '<td>'+data[i].StateEmail+'</td>'+
                             '<td>'+data[i].StateWebsite+'</td>'+
                             '<td>'+data[i].StatePhoneNumber+'</td>'+
                             '<td>'+data[i].Active+'</td>'+
                            '<td style="text-align:right;">'+
                                    '<a href="javascript:void(0);" class="btn btn-info btn-sm item_edit" data-product_code="'+data[i].StateName+'" data-product_name="'+data[i].StateEmail+'" data-price="'+data[i].StatePhoneNumber+'">Edit</a>'+' '+
                                '</td>'+
                            '</tr>';
                }
                $('#show_data').html(html);
            }

        });
    }

        //Save product
        $('#btn_save').on('click',function(){
            var StateCode = $('#StateCode').val();
            var StateName = $('#StateName').val();
            var FIPSCode        = $('#FIPSCode').val();
            var StateEmail        = $('#StateEmail').val();
              var StateWebsite        = $('#StateWebsite').val();
               var StatePhoneNumber        = $('#StatePhoneNumber').val();

            $.ajax({
                type : "POST",
                url  : "<?php echo site_url('state/save')?>",
                dataType : "JSON",
                data : {product_code:product_code , product_name:product_name, price:price},
                success: function(data){
                    $('[name="StateCode"]').val("");
                    $('[name="StateName"]').val("");
                    $('[name="FIPSCode"]').val("");
                      $('[name="StateEmail"]').val("");
                      $('[name="StateWebsite"]').val("");
                       $('[name="StatePhoneNumber"]').val("");
                    $('#Modal_Add').modal('hide');
                    show_product();
                }
            });
            return false;
        });

        //get data for update record
        $('#show_data').on('click','.item_edit',function(){
            var StateCode = $(this).data('StateCode');
            var StateName = $(this).data('StateName');
            var FIPSCode  = $(this).data('FIPSCode');
            var StateEmail  = $(this).data('StateEmail');
            var StateWebsite  = $(this).data('StateWebsite');
              var StatePhoneNumber  = $(this).data('StatePhoneNumber');
            $('#Modal_Edit').modal('show');
            $('[name="StateCode_edit"]').val(StateCode);
            $('[name="FIPSCodee_edit"]').val(StateName);
            $('[name="StateEmail_edit"]').val(FIPSCode);
           $('[name=" StateWebsite_edit"]').val(StateWebsite);
            $('[name="StatePhoneNumber_edit"]').val(StatePhoneNumber);
        });

        //update record to database
         $('#btn_update').on('click',function(){
            var StateCode = $('#StateCode_edit').val();
            var StateName = $('#StateName_edit').val();
            var FIPSCode = $('#FIPSCode_edit').val();
            var StateEmail = $('#StateEmail_edit').val();
            var StateWebsite= $('#StateWebsite_edit').val();
            var StatePhoneNumber= $('#StatePhoneNumber_edit').val();
            $.ajax({
                type : "POST",
                url  : "<?php echo site_url('state/update')?>",
                dataType : "JSON",
                data : {StateCode:StateCode , StateName:StateName, FIPSCode:FIPSCode,StateEmail:StateEmail,StateWebsite:StateWebsite,StatePhoneNumber:StatePhoneNumber},
                success: function(data){
                    $('[name="StateCode_edit"]').val("");
                    $('[name="StateName_edit"]').val("");
                    $('[name="FIPSCode_edit"]').val("");
                     $('[name="StateEmail_edit"]').val("");
                      $('[name="StateWebsite_edit"]').val("");
                      $('[name="StatePhoneNumber_edit"]').val("");
                    $('#Modal_Edit').modal('hide');
                    show_product();
                }
            });
            return false;
        });

        //get data for delete record
        /*$('#show_data').on('click','.item_delete',function(){
            var product_code = $(this).data('product_code');
            
            $('#Modal_Delete').modal('show');
            $('[name="product_code_delete"]').val(product_code);
        });
*/
  });

</script>
</body>
</html>








