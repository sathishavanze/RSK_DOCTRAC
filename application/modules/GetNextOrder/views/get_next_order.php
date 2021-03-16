
<?php 
$permissions=$this->Common_Model->get_row('mRole',['RoleTypeUID'=>$this->RoleType,'RoleUID'=>$this->RoleUID] );
/*echo "test";
echo $this->RoleType;
print_r($permissions);exit;*/
if($permissions->AssignGetNextOrder == 1)
{
?>

<button type="button" class="btn btn-fill btn-success getnextorder"><i class="fa fa-forward" aria-hidden="true"></i> Get Next Order</button>
<?php }?>
<script type="text/javascript">
	var WorkflowModuleUID = '<?php echo $WorkflowModuleUID; ?>';
	$(document).ready(function(){
	/*	$.ajax({
					type: "POST",
					url: '<?php echo base_url();?>GetNextOrder/GetNextOrderPermission',
					success: function(data)
					{
						if(data == 0)
						{
							$(".getnextorder").hide();
						}
					}
			  });*/
			$(document).off('click', '.getnextorder').on('click', '.getnextorder', function (e) { 
				$.ajax({
				      type: "POST",
				      url: '<?php echo base_url();?>GetNextOrder/AssignGetNextOrder',
				      data: ({'WorkflowModuleUID':WorkflowModuleUID}),
				      dataType: 'JSON',
				      beforeSend: function()
				      {
				        addcardspinner($('#containerstart'));
				      },
				      success: function(data)
				      {
				        removecardspinner($('#containerstart'));
				         if(data.validation_error == 1)
			                {
			                    $.notify({
			                    icon:"icon-bell-check",
			                    message:data.message
			                  	},
				                  {
				                    type:'danger',
				                    delay:1000 
				                  });


			                }else{
			                 $.notify(
			                  {
			                    icon:"icon-bell-check",
			                    message:data.message
			                  },
			                  {
			                    type:'success',
			                    delay:1000 
			                  });
			              	location.reload();
			              }

				      }
				    });

				});
	});
</script>