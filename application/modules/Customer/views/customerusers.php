<form class="frmcustomerproductusers" action="#"> 
	<div class="col-md-12 borderright dynamicdiv"> 
		<div class="row" style="background:#f7f7f7;"> 
			<div class="col-md-2 dyndivhead">       
				<strong>Product</strong> 
			</div>
			<div class="col-md-3 dyndivhead">
				<strong>Workflows</strong>         
			</div>
			<div class="col-md-6 dyndivhead">
				<strong>Users</strong>
			</div>
			<div class="col-md-1 dyndivhead">
				<strong>Action</strong>
			</div>
		</div>

		<?php foreach ($details as $key => $value) { 
			?>
			<div class="row UsermoduleWrapper"> 
				<div class="col-md-2 dyndiv">       
					<p class="selectedproduct"><?php echo $value['ProductName']  ?></p>
					<input type="hidden" name="ProductUID[<?php echo $key; ?>]" value="<?php echo $value['ProductUID']; ?>">
					<input type="hidden" name="SubProductUID[<?php echo $key; ?>]" value="<?php echo $value['SubProductUID']; ?>">
				</div>
				<div class="col-md-3 dyndiv">
					<p class="selectedproduct">
							<?php 

								foreach ($value['Customer_Workflow'] as $cwkeys => $values) { ?>

										<?php
										// print_r($values);
										 echo $values['WorkflowModuleName'];?>, 

										<?php 
										$selected = 1; 

								} ?>

						</p>
				</div>
				<div class="col-md-6 dyndiv">
					<div class="form-group">
						<select class="select2picker CustomerProductUsers" id="CustomerProductUsers<?php echo $key; ?>" name="CustomerProductUsers[<?php echo $key; ?>][]" multiple >  
							<option value=""></option>

							<?php foreach ($Users as $okey => $user) {

								$selected = 0;

								foreach ($value['CustomerProductUsers'] as $keys => $values) {

									if($user->UserUID == $values['UserUID']){  ?>
										<option value="<?php echo $user->UserUID; ?>" selected><?php echo $user->UserName;?></option>
										<?php 
										$selected = 1; 
									}

								} if($selected == 0){ ?>

									<option value="<?php echo $user->UserUID; ?>" ><?php echo $user->UserName;?></option>

								<?php } ?>
							<?php } ?>

						</select>
					</div>
				</div>

					<div class="col-md-1 dyndiv">
						<div class="mt-10">
							<button type="button" class="btn btn-xs btn-dribbble copyusers"><i class="icon-copy4"></i></button>
						</div>
					</div>
				</div>
			<?php } ?>
			<div class="updatediv text-right col-md-12 pd-0">
				<button type="submit" class="btn btn-fill btn-dribbble btn-wd" name="UpdateUsers" id="UpdateUsers"> Update</button>
			</div>
		</div>
	</form>
	<script type="text/javascript">
		$(function() {
			$(".selectpicker").selectpicker();
			$("select.select2picker").select2({
				//tags: false,
				theme: "bootstrap",
			});


			/*-----WORKFLOW TAB STARTS*/
			$('.frmcustomerproductusers').submit(function (e) {
				e.preventDefault();
				e.stopPropagation();
				var formdata=new FormData($(this)[0]);
				formdata.append('CustomerUID', $('#CustomerUID').val());
				var button = $('#UpdateUsers');
				var button_text = $('#UpdateUsers').html();

				$.ajax({
					type: "POST",
					url: '<?php echo base_url();?>Customer/CustomerProductUsersAdd',
					data: formdata, 
					processData:false,
					contentType:false,
					dataType:'json',
					beforeSend: function(){
							button.attr("disabled", true);
							button.html('Please Wait ...'); 
					},
					success: function(data)
					{
						button.html(button_text);
						if(data.validation_error == 0){
							$.notify(
							{
								icon:"icon-bell-check",
								message:data.message
							},
							{
								type:"success",
								delay:1000 
							})
						}else if(data.validation_error == 1){

							$.notify(
							{
								icon:"icon-bell-check",
								message:data.message
							},
							{
								type:"danger",
								delay:1000 
							})
						}
						button.removeAttr("disabled"); 

					},
					error: function(jqXHR){
						console.log(jqXHR);
					}
				});

			})
			/*-----WORKFLOW TAB ENDS*/

		});



	</script>