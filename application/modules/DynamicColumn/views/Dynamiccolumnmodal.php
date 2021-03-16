


<form action="#" name="dynamiccolumn-form" id="dynamiccolumn-form" class="form-horizontal">
	<input type="hidden" name="QueueColumnUID" value="<?php echo isset($DynamicColumnDetails) ? $DynamicColumnDetails->QueueColumnUID : ''; ?> ">
	<div class="modal-header">
		<h5 class="modal-title" id="exampleModalLabel"><span><?php echo $Heading; ?></span> <?php echo isset($DynamicColumnDetails) ? $DynamicColumnDetails->HeaderName : ''; ?></h5>
		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		</button>
	</div>
	<div class="modal-body">

		<div class="row mt-3">

			<div class="col-md-6">
				<div class="form-group bmd-form-group">
					<label for="HeaderName" class="bmd-label-floating">Header Name<span class="mandatory"></span></label>
					<input type="text" class="form-control" id="HeaderName" name="HeaderName" value="<?php echo isset($DynamicColumnDetails) ? $DynamicColumnDetails->HeaderName : ''; ?>">				
				</div> 
			</div>

			<div class="col-md-6">
				<div class="form-group bmd-form-group">
					<label for="ColumnName" class="bmd-label-floating"> Column Name<span class="mandatory"></span></label>
					<select class="select2picker form-control ColumnName"  id="ColumnName" name="ColumnName">
						<?php foreach ($QueueColumn as $key => $value) { ?>
							<option value="<?php echo $value; ?>" <?php echo isset($DynamicColumnDetails->ColumnName) && ($DynamicColumnDetails->ColumnName == $value)  ? 'selected' : ''; ?>><?php echo $key; ?></option>
						<?php } ?>								
					</select>
				</div> 
			</div>

		</div>

		<div class="row mt-3">

			<div class="col-md-6 SubQueueAging-div" <?php echo $DynamicColumnDetails->ColumnName == "ExceptionSubQueueAging" ? '' : 'style="display: none;"'; ?>>
				<div class="form-group bmd-form-group">
					<label for="SubQueueAging" class="bmd-label-floating"> SubQueue Aging<span class="mandatory"></span></label>
					<select class="select2picker form-control SubQueueAging"  id="SubQueueAging" name="SubQueueAging">
						<option value="Calendar Days" <?php echo (($DynamicColumnDetails->SubQueueAging == "Calendar Days" && !empty($DynamicColumnDetails->SortColumnName)) ? 'selected' : ''); ?>>Calendar Days</option>
						<option value="Business Days" <?php echo (($DynamicColumnDetails->SubQueueAging == "Business Days" && !empty($DynamicColumnDetails->SortColumnName)) ? 'selected' : ''); ?>>Business Days</option>							
					</select>
				</div> 
			</div>

		</div>


		<div class="row">


			<div class="col-md-12 PermissionQueue">
				<div class="form-group bmd-form-group">
					<label for="PermissionQueueUIDS" class="">Display in selected queues</label>
					<select class="select2picker form-control PermissionQueueUIDS"  id="PermissionQueueUIDS" name="PermissionQueueUIDS[]" multiple="true">
					<?php 
							$QueueUIDsoptionsArr = explode(",", $DynamicColumnDetails->QueueUIDs);   
							$StaticQueueUIDsoptionsArr = explode(",", $DynamicColumnDetails->StaticQueueUIDs);   

							foreach ($SubQueues as $key => $value) { ?>
								<option value="SubQueues-<?php echo $value->QueueUID; ?>" <?php if( in_array( $value->QueueUID,$QueueUIDsoptionsArr)){ echo 'selected'; };?> ><?php echo $value->QueueName; ?></option>
							<?php } ?>
						</optgroup>  
						<!-- <optgroup class="select2-result-selectable" label="Queues">                 -->

							<?php foreach ($StaticQueues as $key => $value) { ?>
								<option value="StaticQueues-<?php echo $value->StaticQueueUID; ?>" <?php if( in_array( $value->StaticQueueUID,$StaticQueueUIDsoptionsArr)){ echo 'selected'; };?>><?php echo $value->StaticQueueName; ?></option>
							<?php } ?>	
							<!-- </optgroup>   -->							
					</select>
				</div> 
			</div>


			</div>


			<div class="row mt-3">

				<div class="col-md-3"> 

					<div class="form-check dynamicSort" style="top: 20px;">
						<label class="form-check-label" style="color: teal">
							<input class="form-check-input NoSort" type="checkbox" name="NoSort" id="NoSort" value="<?php echo $DynamicColumnDetails->NoSort; ?>"<?php echo (($DynamicColumnDetails->NoSort == 1 && !empty($DynamicColumnDetails->SortColumnName) || ($DynamicColumnDetails->NoSort == 0) ) ? 'checked' : ''); ?>> Sorting
							<span class="form-check-sign">
								<span class="check"></span>
							</span>
						</label>
					</div>

				</div>

				<div class="col-md-6 SortColumnName-div" <?php echo (($DynamicColumnDetails->NoSort == 1 && !empty($DynamicColumnDetails->SortColumnName) || ($DynamicColumnDetails->NoSort == 0) ) ? '' : 'style="display: none;"'); ?> >
					<div class="form-group bmd-form-group">
						<label for="SortColumnName" class="bmd-label-floating">Sorting Column <span class="mandatory"></span></label>
						<select class="select2picker form-control SortColumnName" id="SortColumnName" name="SortColumnName">
							<option value="Select">Use Column Name</option>
							<?php foreach ($QueueColumn as $key => $value) { 

								if($value == $DynamicColumnDetails->SortColumnName){
									?>
									<option value="<?php echo $value; ?>" selected><?php echo $key; ?></option>
								<?php } else{?>
									<option value="<?php echo $value; ?>" ><?php echo $key; ?></option>
								<?php } } ?>
								<option value="Custom">Custom</option>
							</select>

						</div>
					</div>

					<div class="col-md-3 SortCustomColumnName-div" <?php echo isset($DynamicColumnDetails->SortColumnName) && !empty($DynamicColumnDetails->SortColumnName) && !in_array($DynamicColumnDetails->SortColumnName, $QueueColumn) ? '' : 'style="display: none;"'; ?>>
						<div class="form-group bmd-form-group">
							<label for="SortCustomColumnName" class="bmd-label-floating">Sorting Custom Column<span class="mandatory"></span></label>
							<input type="text" class="form-control" id="SortCustomColumnName" name="SortCustomColumnName" value="<?php echo isset($DynamicColumnDetails) ? $DynamicColumnDetails->SortColumnName : ''; ?>">				
						</div> 
					</div>
				</div>

				<div class="row mt-3">

					<div class="col-md-3"> 

						<div class="form-check dynamicCheckedList" style="top: 20px;">
							<label class="form-check-label" style="color: teal">
								<input class="form-check-input IsChecklist" type="checkbox" id="IsChecklist" name="IsChecklist" value="<?php echo $DynamicColumnDetails->IsChecklist; ?>"<?php echo $DynamicColumnDetails->IsChecklist == 1 ? 'checked' : ''; ?>> IsChecklist
								<span class="form-check-sign">
									<span class="check"></span>
								</span>
							</label>
						</div>

					</div>


					<div class="col-md-9 DocumentTypeName" <?php echo $DynamicColumnDetails->IsChecklist == 1 ? '' : 'style="display: none;"'; ?>>
						<div class="form-group bmd-form-group">
							<label for="DocumentTypeUID" class="bmd-label-floating"> Checklist<span class="mandatory"></span></label>
							<select class="select2picker form-control"  id="DocumentTypeUID" name="DocumentTypeUID">
								<option value=""></option>
								<?php foreach ($DocumentDetails as $key => $value) { if($value->DocumentTypeName == $DynamicColumnDetails->DocumentTypeName){
									?>
									<option value="<?php echo $value->DocumentTypeUID; ?>" selected><?php echo $value->DocumentTypeName; ?></option>
								<?php } else{?>
									<option value="<?php echo $value->DocumentTypeUID; ?>" ><?php echo $value->DocumentTypeName; ?></option>
								<?php } } ?>							
							</select>
						</div> 
					</div>

				</div>


			</div>


			<div class="modal-footer">
				<div class="ml-auto text-right">
					<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-arrow-left15 pr-10 Back" ></i> Cancel</button>
					<?php if(isset($DynamicColumnDetails)) { ?>

						<button class="btn btn-success updatecolumn" name="submit" type="submit" id="updatecolumn"><i class="icon-floppy-disk pr-10"></i> Update</button>                    

					<?php } else { ?>

						<button class="btn btn-success addcolumn" name="submit" type="submit" id="addcolumn"><i class="icon-floppy-disk pr-10"></i> Add</button>                    
					<?php } ?>
				</div>
			</div>

		</form>


		<!-- End Modal Edit Product-->