<ul class="nav nav-pills nav-pills-danger customtab prioritytab" role="tablist">
	<li class="nav-item">
		<a data-reporttype="" class="nav-link ajaxload <?php if ($this->uri->segment(1) == 'JuniorProcessorPriority_Report' && $this->uri->segment(2) == '') {echo "active";} ?>" href="<?php echo base_url(); ?>JuniorProcessorPriority_Report" role="tablist">Milestone
		</a>
	</li>
	<li class="nav-item">
		<a data-reporttype="agingbucket" class="nav-link ajaxload <?php if ($this->uri->segment(1) == 'JuniorProcessorPriority_Report' && $this->uri->segment(2) == 'agingbucket') {echo "active";} ?>" href="<?php echo base_url(); ?>JuniorProcessorPriority_Report/agingbucket" role="tablist">Aging Bucket</a>
	</li>
	<li class="nav-item">
		<a data-reporttype="onshoreprocessor" class="nav-link ajaxload <?php if ($this->uri->segment(1) == 'JuniorProcessorPriority_Report' && $this->uri->segment(2) == 'onshoreprocessor') {echo "active";} ?>" href="<?php echo base_url(); ?>JuniorProcessorPriority_Report/onshoreprocessor" role="tablist">Processor</a>
	</li>
	<li class="nav-item">
		<a data-reporttype="onshorejuniorprocessor" class="nav-link ajaxload <?php if ($this->uri->segment(1) == 'JuniorProcessorPriority_Report' && $this->uri->segment(2) == 'onshorejuniorprocessor') {echo "active";} ?>" href="<?php echo base_url(); ?>JuniorProcessorPriority_Report/onshorejuniorprocessor" role="tablist">Junior Processor</a>
	</li> 
	<!-- <li class="nav-item">
		<a data-reporttype="processor" class="nav-link ajaxload <?php if ($this->uri->segment(1) == 'JuniorProcessorPriority_Report' && $this->uri->segment(2) == 'processor') {echo "active";} ?>" href="<?php echo base_url(); ?>JuniorProcessorPriority_Report/processor" role="tablist">Processor</a>
	</li>
	<li class="nav-item">
		<a data-reporttype="teamleader" class="nav-link ajaxload <?php if ($this->uri->segment(1) == 'JuniorProcessorPriority_Report' && $this->uri->segment(2) == 'teamleader') {echo "active";} ?>" href="<?php echo base_url(); ?>JuniorProcessorPriority_Report/teamleader" role="tablist">Team Leader</a>
	</li> -->
	<li class="nav-item">
		<a data-reporttype="loantype" class="nav-link ajaxload <?php if ($this->uri->segment(1) == 'JuniorProcessorPriority_Report' && $this->uri->segment(2) == 'loantype') {echo "active";} ?>" href="<?php echo base_url(); ?>JuniorProcessorPriority_Report/loantype" role="tablist">Loan Type</a>
	</li>
</ul>