<ul class="nav nav-pills nav-pills-danger customtab prioritytab" role="tablist">
	<li class="nav-item">
		<a data-reporttype="" class="nav-link ajaxload <?php if ($this->uri->segment(1) == 'Pipeline' && $this->uri->segment(2) == '') {echo "active";} ?>" href="<?php echo base_url(); ?>Pipeline" role="tablist">Milestone
		</a>
	</li>
	<li class="nav-item">
		<a data-reporttype="agingbucket" class="nav-link ajaxload <?php if ($this->uri->segment(1) == 'Pipeline' && $this->uri->segment(2) == 'agingbucket') {echo "active";} ?>" href="<?php echo base_url(); ?>Pipeline/agingbucket" role="tablist">Aging Bucket</a>
	</li>
	<li class="nav-item">
		<a data-reporttype="onshoreprocessor" class="nav-link ajaxload <?php if ($this->uri->segment(1) == 'Pipeline' && $this->uri->segment(2) == 'onshoreprocessor') {echo "active";} ?>" href="<?php echo base_url(); ?>Pipeline/onshoreprocessor" role="tablist">Processor</a>
	</li>
	<li class="nav-item">
		<a data-reporttype="onshoreteamleader" class="nav-link ajaxload <?php if ($this->uri->segment(1) == 'Pipeline' && $this->uri->segment(2) == 'onshoreteamleader') {echo "active";} ?>" href="<?php echo base_url(); ?>Pipeline/onshoreteamleader" role="tablist">Team Leader</a>
	</li>
	<!-- <li class="nav-item">
		<a data-reporttype="processor" class="nav-link ajaxload <?php if ($this->uri->segment(1) == 'Pipeline' && $this->uri->segment(2) == 'processor') {echo "active";} ?>" href="<?php echo base_url(); ?>Pipeline/processor" role="tablist">Processor</a>
	</li>
	<li class="nav-item">
		<a data-reporttype="teamleader" class="nav-link ajaxload <?php if ($this->uri->segment(1) == 'Pipeline' && $this->uri->segment(2) == 'teamleader') {echo "active";} ?>" href="<?php echo base_url(); ?>Pipeline/teamleader" role="tablist">Team Leader</a>
	</li> -->
	<li class="nav-item">
		<a data-reporttype="loantype" class="nav-link ajaxload <?php if ($this->uri->segment(1) == 'Pipeline' && $this->uri->segment(2) == 'loantype') {echo "active";} ?>" href="<?php echo base_url(); ?>Pipeline/loantype" role="tablist">Loan Type</a>
	</li>
</ul>