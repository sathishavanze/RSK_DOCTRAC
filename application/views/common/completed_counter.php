
<style type="text/css">

	.CompletedCounterDiv
	{
	  width: 100px;
	    height: 100px;
	    background: red;
	    -moz-border-radius: 50px;
	    -webkit-border-radius: 50px;
	    border-radius: 50px;
	  float:left;
	  margin:5px;
	}
	.CounterContainer {
		width: auto;
	    float: right;
	    margin-top: -38px;
	    margin-right: 0px;
	    float: right !important;
	}
	.CounterContainer .card-stats {
	    margin: 36px 0px 0px 20px;
	    width: 168px;
	    height: 48px;
	    float: right;
	    background: #f7f7f7;
	    box-shadow: 0 4px 20px 0 rgba(0, 0, 0, .14), 0 7px 10px -5px rgba(0,0,0,0.2);
	}

	.CounterContainer .card-stats .card-header .card-title {
	    font-size: 16px !important;
	    font-weight: 600 !important;
	    color: #000 !important;
	    margin-top: -16px !important;
	}
	.CounterContainer .card-stats .card-header .card-category {
		font-size: 14px !important;   
	    font-weight: 500 !important;
	    color: #000 !important;
	}
</style>

<div class="row CounterContainer">
		<?php 
		$intivitual_count = $this->Common_Model->OrdersCompletedCountToday($WorkflowModuleUID, 'self');
		$overall_count = $this->Common_Model->OrdersCompletedCountToday($WorkflowModuleUID);
		$reviewed_count = $this->Common_Model->TotalViewedCount($WorkflowModuleUID);
		?>
	<div class="card card-stats">
	  <div class="card-header card-header-success card-header-icon">
	    <div class="card-icon">
	      <i class="fa fa-calendar-check-o" aria-hidden="true"></i> </div>
	    <h3 class="card-title CompletedCounter" title="Total Reviewed Today"><?php echo $reviewed_count; ?></h3>
	    <p class="card-category">Total Reviewed Today</p>
	  </div>	  
	</div>

	<div class="card card-stats">
	  <div class="card-header card-header-success card-header-icon">
	    <div class="card-icon">
	      <i class="fa fa-calendar-check-o" aria-hidden="true"></i>   </div>
	    <h3 class="card-title CompletedCounter" title="Completed Today"><?php echo $intivitual_count; ?></h3>
	    <p class="card-category">Completed Today</p>
	  </div>
	  
	</div>

	<div class="card card-stats">
	  <div class="card-header card-header-success card-header-icon">
	    <div class="card-icon">
	      <i class="fa fa-calendar-check-o" aria-hidden="true"></i> </div>
	    <h3 class="card-title CompletedCounter" title="Team Completed Today"><?php echo $overall_count; ?></h3>
	    <p class="card-category">Team Completed Today</p>
	  </div>	  
	</div>

	
</div>

<script type="text/javascript">
			
			$('.CompletedCounter').each(function () {
			    $(this).prop('Counter',0).animate({
			        Counter: $(this).text()
			    }, {
			        duration: 4000,
			        easing: 'swing',
			        step: function (now) {
			            $(this).text(Math.ceil(now));
			        }
			    });
			});

</script>