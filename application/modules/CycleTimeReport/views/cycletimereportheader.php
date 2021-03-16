<ul class="nav nav-pills nav-pills-danger customtab cycletimetab" role="tablist">
	<li class="nav-item">
		<a data-reporttype="" class="nav-link ajaxload <?php if ($this->uri->segment(1) == 'CycleTimeReport' && $this->uri->segment(2) == '') {echo "active";} ?>" href="<?php echo base_url(); ?>CycleTimeReport" role="tablist">Cycle Time (MTD)
		</a>
	</li>
	<li class="nav-item">
		<a data-reporttype="" class="nav-link ajaxload <?php if ($this->uri->segment(1) == 'CycleTimeReport' && $this->uri->segment(2) == 'CycleTime') {echo "active";} ?>" href="<?php echo base_url(); ?>CycleTimeReport/CycleTime" role="tablist">Cycle Time
		</a>
	</li>
	<li class="nav-item">
		<a data-reporttype="agent" class="nav-link ajaxload <?php if ($this->uri->segment(1) == 'CycleTimeReport' && $this->uri->segment(2) == 'agent') {echo "active";} ?>" href="<?php echo base_url(); ?>CycleTimeReport/agent" role="tablist">Cycle Time Agent</a>
	</li>
	<li class="nav-item">
		<a data-reporttype="substatus" class="nav-link ajaxload <?php if ($this->uri->segment(1) == 'CycleTimeReport' && $this->uri->segment(2) == 'substatus') {echo "active";} ?>" href="<?php echo base_url(); ?>CycleTimeReport/substatus" role="tablist">Cycle Time Sub Status</a>
	</li>
	<li class="nav-item">
		<a data-reporttype="substatusagent" class="nav-link ajaxload <?php if ($this->uri->segment(1) == 'CycleTimeReport' && $this->uri->segment(2) == 'substatusagent') {echo "active";} ?>" href="<?php echo base_url(); ?>CycleTimeReport/substatusagent" role="tablist">Cycle Time Sub Status Agent</a>
	</li>
</ul>