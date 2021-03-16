			<div class="text-right">
				<span class="badge badge-pill" style="background-color: #757575;">Invalid</span>
				<span class="badge badge-pill" style="background-color: #ff8100c7;">Exception ID</span>
				<span class="badge badge-pill" style="background-color: #168998;">Client Name</span>
				<span class="badge badge-pill" style="background-color: #8123e8;">Client Code</span>
				<span class="badge badge-pill" style="background-color: #67941e;">Project Name</span>
				<span class="badge badge-pill" style="background-color: #BF6105;">Project Code</span>
				<span class="badge badge-pill" style="background-color: #ff04ec;">Loan Number</span>
				<span class="badge badge-pill" style="background-color: #ff5c33;">Document Type</span> 
				<span class="badge badge-pill" style="background-color: #bd302a;">Product Name</span>
			</div>

			<div class="">
				<div class="tablescroll defaultfontsize">
					<table class="table table-striped table-hover table-format nowrap datatable"  id="table-bulkorder">
						<thead>
							<tr>
								<?php 	


								foreach ($headingsArray as $key => $value) {
									?><th><?php echo $value; ?></th><?php
								}

								?>

							</tr>
						</thead>
						<tbody>

							<?php
							$loanunique = [];
							$loandoctypevalidation = $this->Orderentrymodel->isloan_doctypevalidation();

							foreach ($arrayCode as $i => $a) {
								//for missing fields

								/*LOAN DOCTYPE DUPLICATE VALIDATION*/

								if ($a[$columnvariables['LoanNumber']] != '') {


									if($loandoctypevalidation->ImportValidation == 'DocType'){

										$duplicate = $this->Orderentrymodel->multi_array_search($arrayCode, Array($columnvariables['LoanNumber'] =>  $a[$columnvariables['LoanNumber']],$columnvariables['InputDocTypeUID'] =>  $a[$columnvariables['InputDocTypeUID']]),$i);
										
										if(!empty($duplicate) || ($this->Orderentrymodel->is_loannodoctype_exists($a[$columnvariables['LoanNumber']],$a[$columnvariables['InputDocTypeUID']]) == 1 )){
											//duplicate exists
										}else{
											$validationarray[$i]['LoanNumber'] = $a[$columnvariables['LoanNumber']];

										}

										

									}else if($loandoctypevalidation->ImportValidation == 'Loan') {

										/*LOAN DUPLICATE VALIDATION*/

										if(($this->Orderentrymodel->is_loanno_exists($a[$columnvariables['LoanNumber']]) == 0) && (!in_array(strtolower($a[$columnvariables['LoanNumber']]), $loanunique))) {
											$validationarray[$i]['LoanNumber'] = $a[$columnvariables['LoanNumber']];

										}
										/*check loan duplicates*/
										if(!in_array(strtolower($a[$columnvariables['LoanNumber']]), $loanunique)) {
											array_push($loanunique, strtolower($a[$columnvariables['LoanNumber']]));
										}

									}

								}

								if ($validationarray[$i]['DataExceptionUID'] == '') { 

										echo '<tr style="background-color: #ff8100c7; color: #fff;">'; 
										foreach ($a as $key => $value) { 
											echo '<td >'.$value.'</td>';										
										}
										echo '</tr>';


									} elseif ($validationarray[$i]['ClientName'] == '') {
										
										echo '<tr style="background-color: #168998; color: #fff;">'; 
										foreach ($a as $key => $value) { 
											echo '<td >'.$value.'</td>';										
										}
										echo '</tr>';


									} elseif ($validationarray[$i]['ClientCode'] == '') {
										
										echo '<tr style="background-color: #8123e8; color: #fff;">'; 
										foreach ($a as $key => $value) { 
											echo '<td >'.$value.'</td>';										
										}
										echo '</tr>';


									} elseif ($validationarray[$i]['ProjectName'] == '') {
										
										echo '<tr style="background-color: #67941e; color: #fff;">'; 
										foreach ($a as $key => $value) { 
											echo '<td >'.$value.'</td>';										
										}
										echo '</tr>';


									} elseif ($validationarray[$i]['ProjectCode'] == '') {
										
										echo '<tr style="background-color: #BF6105; color: #fff;">'; 
										foreach ($a as $key => $value) { 
											echo '<td >'.$value.'</td>';										
										}
										echo '</tr>';


									} else if ($validationarray[$i]['LoanNumber'] == '') { 

										echo '<tr style="background-color: #ff04ec; color: #fff;">'; 
										foreach ($a as $key => $value) { 
											echo '<td >'.$value.'</td>';										
										}
										echo '</tr>';


									} elseif ($validationarray[$i]['InputDocTypeUID'] == '') {

									echo '<tr style="background-color: #ff5c33; color: #fff;">'; 
									foreach ($a as $key => $value) { 
										echo '<td >'.$value.'</td>';										
									}
									echo '</tr>';


								
								
								} elseif ($validationarray[$i]['ProductName'] == '') {
										
										echo '<tr style="background-color: #bd302a; color: #fff;">'; 
										foreach ($a as $key => $value) { 
											echo '<td >'.$value.'</td>';										
										}
										echo '</tr>';


									} else {

										echo '<tr>'; 
										foreach ($a as $key => $value) { 
											echo '<td >'.$value.'</td>';										
										}
										echo '</tr>';

									}


							}

							?> 
						</tbody>

					</table>
				</div>
			</div>
