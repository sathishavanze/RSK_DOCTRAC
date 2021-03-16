<!--BEGIN CONTENT-->

<style type="text/css">
	
	.green{
		color: green;
	}

	.red{
		color: red;
	}

	.switch-button  {
                display: inline-block;
                border-radius: 50px;
                background-color: #de6262;
                width: 60px;
                height: 27px;
                padding: 4px;
                position: relative;
                overflow: hidden;
                vertical-align: middle;
                text-align: left;
              }


</style>

<div class="be-content ">
	<div class="main-content container-fluid bg-color">
	    <div class="row ">
		    <div class="col-sm-12 ">
		        <div class="panel panel-default">
					<!-- <div class="panel-heading">
						<div class="caption"><strong>Add User</strong>
						<a class="btn btn-success" href="<?php echo base_url(); ?>user/add_user" style="color: #fff; margin-right: 20px;" name="BtnAddCity" id="BtnAddCity"><span class="mdi mdi-plus"></span></a></div>
					</div> -->

					<div class="panel-heading">
	               		<a href="<?php echo base_url(); ?>user/add_user" class="btn btn-success pull-left" style="margin-bottom: 15px;"> + Add Users</a>
	                </div>


			     	<div class="panel panel-body"><div class="row">
                      <div class="col-sm-12 form-group">
			     		<div class="table-responsive">
							<table id="example" style="border:1px solid #ddd" class="table table-striped table-bordered table-hover">
								<thead>
								<tr>
									<th style="text-align: center;">User Name</th>
									<th style="text-align: center;">Login ID</th>
									<th style="text-align: center;">Employee ID</th>
									<th style="text-align: center;">Role</th>
									<th style="text-align: center;">Email ID</th>
									<th style="text-align: center;">Status</th>
									<th style="text-align: center;width: 50pt;">Action</th>
								</tr>
								</thead>

								<tbody>							
								<?php foreach($UserDetails as $row): ?>
								<tr>
									<td style="text-align: center;"><?php echo $row->UserName; ?></td>

									<td style="text-align: center;"><?php echo $row->LoginID; ?></td>
									<td style="text-align: center;"><?php echo $row->EmployeeUID; ?></td>
									<td style="text-align: center;"><?php echo $row->RoleName; ?></td>
									<td style="text-align: center;"><?php echo $row->UserEmailID; ?></td>

									<td>

									<!-- <span style="text-align: center;width:100%;" class="btn btn-rounded btn-space <?php 
									if($row->Active==1)
										{ echo('btn-success'); } 
									else { echo('btn-warning'); } ?>">
									<?php if($row->Active==1){ echo('Active'); } else { echo('Inactive'); } ?> </span> -->



                                   <span style="text-align: center;width:100%;" class="btn btn-rounded btn-xs btn-space">
                                      <div class="switch-button  switch-button-xs ">
                                          <?php if($row->Active==1): ?>
                                          <input type="checkbox" name="OrderEntry<?php echo $row->UserUID; ?>" id="<?php echo $row->UserUID;?>" class="status" value="1" checked="true">
                                          <?php elseif($row->Active==0): ?>
                                          <input type="checkbox" name="OrderEntry<?php echo $row->UserUID; ?>" id="<?php echo $row->UserUID;?>" class="status" value="0">
                                          <?php endif; ?>
                                          <span><label for="<?php echo $row->UserUID; ?>"></label></span>
                                      </div>
		                           </span>

									</td>

									<td> 

									<span style="text-align: center;width:100%;display:inline;">
									
									<a href="<?php echo base_url()."user/edit_user/".$row->UserUID; ?>" class="btn" style="background-color: #fff;color: #000;" ><span class="glyphicon glyphicon-pencil"></span>
									</a>
						
									<a href="" data-value="<?php echo $row->UserUID;?>" class="btn btnDelete" style="background-color: #fff;color: #000;" ><span class="glyphicon glyphicon-remove"></span>
									</a>


									</span>

									</td>
									<!-- <td><button style="text-align: center;width:100%;" id="edit"><button style="text-align: center;width:100%;" id="save"></button></button>
									</td> -->
								</tr>
								<?php endforeach; ?>
								</tbody>
							</table>
						</div>
			    	</div>
				</div>
			</div>
		</div>	</div>
		</div>
	</div>
</div>
<div class="fixed-plugin">
  <div class="dropdown show-dropdown pd-10">
    <a href="#" data-toggle="dropdown">
      <i class="ion-android-settings" style="color:#fff"> </i>
    </a>
    <ul class="dropdown-menu">
      <li class="header-title"> Sidebar Filters</li>
      <li class="adjustments-line">
        <a href="javascript:void(0)" class="switch-trigger active-color">
          <div class="badge-colors ml-auto mr-auto">
            <span class="badge filter badge-purple" data-color="purple"></span>
            <span class="badge filter badge-azure" data-color="azure"></span>
            <span class="badge filter badge-green" data-color="green"></span>
            <span class="badge filter badge-warning" data-color="orange"></span>
            <span class="badge filter badge-danger" data-color="danger"></span>
            <span class="badge filter badge-rose active" data-color="rose"></span>
          </div>
          <div class="clearfix"></div>
        </a>
      </li>


      <li class="header-title">Sidebar Background</li>
      <li class="adjustments-line">
        <a href="javascript:void(0)" class="switch-trigger background-color">
          <div class="ml-auto mr-auto">
            <span class="badge filter badge-black active" data-background-color="black"></span>
            <span class="badge filter badge-white" data-background-color="white"></span>
            <span class="badge filter badge-red" data-background-color="red"></span>
            <span class="badge filter badge-blue" data-background-color="blue"></span>
          </div>
          <div class="clearfix"></div>
        </a>
      </li>

      <li class="adjustments-line">
        <a href="javascript:void(0)" class="switch-trigger">
          <p>Sidebar Mini</p>
          <label class="ml-auto">
            <div class="togglebutton switch-sidebar-mini">
              <label>
                <input type="checkbox">
                <span class="toggle"></span>
              </label>
            </div>
          </label>
          <div class="clearfix"></div>
        </a>
      </li>

      <li class="adjustments-line">
        <a href="javascript:void(0)" class="switch-trigger">
          <p>Sidebar Images</p>
          <label class="switch-mini ml-auto">
            <div class="togglebutton switch-sidebar-image">
              <label>
                <input type="checkbox" checked="">
                <span class="toggle"></span>
              </label>
            </div>
          </label>
          <div class="clearfix"></div>
        </a>
      </li>

      <li class="header-title">Images</li>

      <li class="active">
        <a class="img-holder switch-trigger" href="javascript:void(0)">
          <img src="<?php echo base_url(); ?>assets/img/sidebar-1.jpg" alt="">
        </a>
      </li>
      <li>
        <a class="img-holder switch-trigger" href="javascript:void(0)">
          <img src="<?php echo base_url(); ?>assets/img/sidebar-2.jpg" alt="">
        </a>
      </li>
      <li>
        <a class="img-holder switch-trigger" href="javascript:void(0)">
          <img src="<?php echo base_url(); ?>assets/img/sidebar-3.jpg" alt="">
        </a>
      </li>
      <li>
        <a class="img-holder switch-trigger" href="javascript:void(0)">
          <img src="<?php echo base_url(); ?>assets/img/sidebar-4.jpg" alt="">
        </a>
      </li>

    </ul>
  </div>
</div> 
<div id="alert-model" tabindex="-1" role="dialog" class="modal fade">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header" style="padding: 5px 10px 0 0;">
            <button type="button" data-dismiss="modal" aria-hidden="true" class="close"><span class="mdi mdi-close"></span></button>
          </div>
          <div class="modal-body">
            <div class="text-center">
              <h7 style="line-height: 30px;">Are You Sure Want to Delete ?</h7>
               <div class="xs-mt-10" style="width: 335px;display:block;margin: 0 auto;">
                
              </div> 
            </div>
            <div class="text-right">
            <button type="submit" class="btn input-sm btn-success Yes"  style="height: 37px;">Yes</button>
                <button type="submit" class="btn input-sm btn-primary No"  style="height: 37px;">No</button>
            </div>
          </div> 
        </div>
      </div>
</div>
<!--END CONTENT-->
<!-- <script type="text/javascript" src="https://cdn.datatables.net/fixedcolumns/3.2.3/js/dataTables.fixedColumns.min.js"></script> -->
<script type="text/javascript">

		
	$('#example tfoot th').each( function () {
        var title = $('#example thead th').eq( $(this).index() ).text();
        $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
    } );

     $("#example").dataTable({
          processing: true,
          fixedColumns: {
			leftColumns: 1,
			rightColumns: 2
		}
        });


    $('#edit').click(function(){
	  $('#edit').hide();
	  $('td').each(function(){
	    var content = $(this).html();
	    $(this).html('<textarea>' + content + '</textarea>');
	  });  
	  
	  $('#save').show();
	});

	$('#save').click(function(){
	  $('#save').hide();
	  $('textarea').each(function(){
	    var content = $(this).val();//.replace(/\n/g,"<br>");
	    $(this).html(content);
	    $(this).contents().unwrap();    
	  }); 

	  $('#edit').show(); 
	});
 
     
    // Apply the filter
    $("#example tfoot input").on( 'keyup change', function () {
    	var table = $('#example').DataTable();
        table
            .column( $(this).parent().index()+':visible' )
            .search( this.value )
            .draw();
    } );


	$('.status').change(function(){ 
      var userid = $(this).attr('id');
      if($(this).val()==1)
      {
        var status = 0;
        $('#'+userid).val('0');
      } else {
      	var status = 1;
      	$('#'+userid).val('1');
      }
       $.ajax({
        type: "POST",
        url: "<?php echo base_url()?>user/ajax_changestatus",
        dataType: "JSON",
        data: {'userid':userid,'status':status}, 
        cache: false,
        success: function(data)
        {
          if(data['error']==0)
          {
          	$.gritter.add({
			 title: data['message'],
			 class_name: 'color success',
			 fade: true,
			 time: 1000,
			 speed:'slow',
			});
			setTimeout(function(){window.location.reload("<?php echo base_url();?>User");}, 1000);	
          } else {
          	$.gritter.add({
			 title: data['message'],
			 class_name: 'color danger',
			 fade: true,
			 time: 1000,
			 speed:'slow',
			});
			setTimeout(function(){window.location.reload("<?php echo base_url();?>User");}, 1000);	
          }
    	}
       });
     });

		$('#BtnAddCity').click(function(event){
		
		//var formData=$('#Frm_Register_User').serialize();
    	$.ajax({
           type: "POST",
           url: base_url+'city/add',
           data: null, 
           cache: false,
           success: function(data)
           {
            	bootbox.modal(data);
    		
           }
         });
		
		
	});
	

	function fn_edit(CityID){
		
		//var formData=$('#Frm_Register_User').serialize();
    	$.ajax({
           type: "POST",
           url: base_url+'city/edit',
           data: "CityID="+CityID, 
           cache: false,
           success: function(data)
           {
            	bootbox.modal(data);
    		
           }
         });
	}

	function fn_delete(CityID){
		
		//var formData=$('#Frm_Register_User').serialize();
    	$.ajax({
           type: "POST",
           url: base_url+'city/delete',
           data: "CityID="+CityID, 
           dataType:'json',
           cache: false,
           success: function(data)
           {
            	MsgBox[data.type](data.msg);
    		
           }
         });
		
		
	}

	$("#example").on("click",".btnDelete", function(){
		    $("#alert-model").modal({
           backdrop: 'static',
           keyboard: false
          });
		    var ID = $(this).attr('data-value');
		$('.Yes').attr('data-ID',ID);
	return false;
	});

	$('.No').click(function(){
		setTimeout(function() {$('#alert-model').modal('hide');});
	});

	$('.Yes').click(function(){
		
		var Id = $(this).attr('data-ID');
               $.ajax({
                 url: '<?php echo base_url();?>user/delete_user',
                 type: "POST",
                 data: {Id:Id}, 
                 dataType:'json',
                 cache: false,
                 success: function(data)
                 {
                  console.log(data);
                  if(data.validation_error == 1)
                  {

					$.gritter.add({
					title: data['message'],
					class_name: 'color success',
					fade: true,
					time: 3000,
					speed:'slow',
					});
					setTimeout(function() {$('#alert-model').modal('hide');});
					setTimeout(function(){window.location.reload("<?php echo base_url();?>User");}, 4000);	
                  }

                  else{
                  	$.gritter.add({
						title: data['message'],
						class_name: 'color danger',
						fade: true,
						time: 3000,
						speed:'slow',
						});
                  }

                 },
                error:function(jqXHR, textStatus, errorThrown)
                {
                  console.log(jqXHR.responseText);
                }
               });
	})



	
</script>