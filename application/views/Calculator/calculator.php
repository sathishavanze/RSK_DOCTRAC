<style type="text/css">
	.calc_head{
		font-weight: 600;
		font-size: 20px;
	}
	.calctab_cnt .form-control{
		margin: 0;
		height: 22px;
	}
	.calculator .customtab{
		margin-left:50px;
	}
	.calctab_cnt{
		padding-left:0!important;
		padding-right:0!important;
		padding-top:0!important;
	}
	.calculatorIcon{
		position: relative;
	}
	.calculatorIcon .fa-calculator{
		position: absolute;
		z-index: 1;
		cursor: pointer;
		left: 0px;
		top: 0px;
		font-size: 20px;
	}
	.calculatorIcon:hover{
		color: #0b7894;
	}

</style>
<span class="calculatorIcon" title="Calculator"><i class="fa fa-calculator" aria-hidden="true"></i></span>
<div class="calculator">


<ul class="nav nav-pills nav-pills-rose customtab" role="tablist">

	<!-- InsuranceCalculator -->
	<li class="nav-item">
		<a class="nav-link active" data-toggle="tab" href="#InsuranceCalculator" role="tablist">
			Insurance Calculator
		</a>
	</li>
	<!-- InsuranceCalculator -->


	<!-- Escrow Calculator -->
	<li class="nav-item">
		<a class="nav-link " data-toggle="tab" href="#EscrowCalculator" role="tablist">
			Escrow Calculator
		</a>
	</li>
	<!-- Escrow Calculator -->


	<!-- Pay Off Calculator -->
	<li class="nav-item">
		<a class="nav-link " data-toggle="tab" href="#PayOffCalculator" role="tablist">
			Pay Off Calculator
		</a>
	</li>
	<!-- Pay Off Calculator -->
	
</ul>


<div class="tab-content tab-space calctab_cnt">

	<!-- InsuranceCalculator -->
	<?php $this->load->view('Calculator/InsuranceCalculator'); ?>
	<!-- InsuranceCalculator -->
	

	<!-- EscrowCalculator -->
	<?php $this->load->view('Calculator/EscrowCalculator'); ?>
	<!-- EscrowCalculator -->
	

	<!-- PayOffCalculator -->
	<?php $this->load->view('Calculator/PayOffCalculator'); ?>
	<!-- PayOffCalculator -->
	
</div>
</div>

<script src="assets/js/calculator.js"  type="text/javascript"></script>




