<style type="text/css">
	.alert.alert-infonotify
	{
		/*display: inline-block;
		margin: 15px auto;
		position: fixed;
		transition: all 0.5s ease-in-out 0s;
		z-index: 1031;
		top: 20px;
		right: 20px;
		max-width:30%;
		font-size: 14px;
		padding: 10px 0px 0px 0px !important;*/
		box-shadow : 0 4px 20px 0 rgba(0, 0, 0, .14), 0 7px 10px -5px rgba(0, 188, 212, .4);
		background-color : #00cae3;
		margin: 30px auto !important;
		padding: 15px 20px 10px 40px !important;
	}
.alert.alert-infonotify p{
	font-size: .875rem;
	margin-bottom: 0px !important;

}
.alert-infonotify i{
	color : #00cae3;
}
.alert-infonotify .close{
	top :30px !important;
}


	.alert-default .close
	{
		position: absolute !important;
		right: 3px !important;
		top: 10% !important;
		margin-top: 0px !important;
		z-index: 1033 !important;
	}
	.btn .btn-danger
	{
		font-size: 8px !important;
	}

	.btn .btn-danger
	{
		font-size: 8px !important;
	}
	.BookMark {
		width: 18px;
		cursor: pointer;
		display: inline-block;
	}
	.InstruDesc
	{		
		color :  #fff !important;
	}
	.alert span {
		max-width: 100%;
		display: -webkit-box !important;
	}

	.alert .close i {
		color: #fff;
		font-size: 11px;
	}
	.alertbtn{
		padding:3px 5px !important;
		text-transform: capitalize;
	}
	.morecontent  {
		display: none;
	}
	.morelink  , .morelink:hover , .morelink:active , .morelink:focus{
		color: #fff;
		background: none;
		border: none;
		text-decoration: underline;
		font-size: 10px;
		display: inline-block;		
		padding: 0px;
		margin : -5px;
		cursor: pointer;
	}
	.alert-default1{
		display: none !important;
		height: 3px !important;
	}
	 .dropdown-menu {
      z-index: 99999999 !important;
    }
</style>
<?php 
$OrderUID = $this->uri->segment(3);
$status = [$this->config->item('keywords')['Cancelled'],$this->config->item('keywords')['ClosedandBilled']]; 
$this->db->select('StatusUID')->from('tOrders');
$this->db->where('OrderUID', $OrderUID);
$this->db->where_not_in('StatusUID', $status);
$GetStatus =  $this->db->get()->row();
if(!empty($GetStatus))
{
	?>
	<!-- <img src="<?php echo base_url() ?>assets/img/star4.png" class="BookMark" data-toggle="bookmark-popover">  -->
	 <!-- <a class="btn btn-link btn-warning btn-xs pull-right nav-link doc_preview" id="navbarDropdownMenuLink" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" style="margin-top:5px;"> <i class="icon-files-empty"></i>  <div class="ripple-container "></div></a>  <div class="dropdown-menu dropdown-menu-right DocumentList" aria-labelledby="navbarDropdownMenuLink">                </div> -->
	<?php
}
?>

<script type="text/javascript">

	var Const_ORDERUID = '<?php echo $OrderUID; ?>'; 
	var CloseBtn = '<a href="#" data-dismiss="alert"><button type="button" class="btn btn-cancel alertbtn">Close</button></a>';

	function  expandnotify(e){
		$.notify({
		icon:"icon-bell-check",
			message: "",
		},
		{
			type:'default1',
			delay:10000000,
		});
		$(".alert-default1").remove();

	}

	
	function InstuctionSpecification(e)
	{
		var OrderUID = "<?php echo $OrderUID; ?>";	
		$.ajax({
			url : "<?php echo base_url('CommonController/CountInstructionSpecify'); ?>",
			type : "POST",
			dataType : "JSON",
			data : {"OrderUID" : OrderUID},
			success: function (data) 
			{    
				if(data.Count > 0)
				{        		
					var SpecifyData = '';        		        		
					for (var i = 0; i < data.Data.length; i++) 
					{ 
						var PermanentBtn = '<button type="button" id="Permanent'+i+'" class="DisableNotify btn btn-warning alertbtn" data-InstruData = "'+data.Data[i].InstructionUID+'">Permanent Close</button> &nbsp;';
						SpecifyData = "<div class='OverallMsg col-md-12 '><div class='MessageSpecification'><p class='InstruDesc more'>"+data.Data[i].Description+"</p></div><div class='OptionSpecification col-md-12 text-right'>"+PermanentBtn+CloseBtn+"</div></div>";
						$.notify(
						{
							icon:"icon-bell-check",
							message: SpecifyData,
						},
						{
							type:'infonotify',
							delay:10000000,
						});
					}
				}

				$('.more').each(function() {						
					var showChar = 52; 
					var moretext = "...";
					var ellipsestext  = "";
					var lesstext = "Show less";
					var content = '';
					content = $(this).html();
					var html = '';
					var c = '';
					var h  = '';
					if(content.length > showChar) {							
						c = content.substr(0, showChar);
						h = '';
						h = content.substr(showChar, content.length - showChar);
						html = c + '<strong class="moreellipses">' + ellipsestext+ '&nbsp;</strong><strong class="morecontent"><span>' + h + '</span>&nbsp;&nbsp;</strong><button class="morelink">' + moretext + '</button>';

						$(this).html(html);
					}
				});
			expandnotify();	
			}
		});
	}

	$(document).ready(function(){
// $(document).off('click', '#navbarDropdownMenuLink').on('click', '#navbarDropdownMenuLink', function(e)
//   {
//    var OrderUID=$('#OrderUID').val();
//    $.ajax({
//           type: "POST",
//           url: '<?php echo base_url(); ?>DocumentCheckList/Document_List',
//           data: {'OrderUID' :OrderUID}, 
//           success : function(data)
//           {
//             $('.DocumentList').html(data);
//             $('#ShowDocument').modal('show');
//           }
//         });
//   });

		InstuctionSpecification();
		jQuery('.alert-infonotify .close').trigger('click'); 

		$("body").on("click" , ".morelink" , function(e){   
			var showChar = 52; 
			var moretext = "...";
			var ellipsestext  = "";
			var lesstext = "Show less";

			if($(this).hasClass("less")) {
				$(this).removeClass("less");
				$(this).html("");
				$(this).html(moretext);				
			} else {
				$(this).addClass("less");
				$(this).html("");
				$(this).html(lesstext);				
			}
			$(this).prev().toggle(); 			
			expandnotify();     
		});
	});

	$(document).off('click','.DisableNotify').on('click','.DisableNotify',function(){		
		var OrderUID = "<?php echo $OrderUID; ?>";	
		var id = $(this).attr('id');
		var InstructionUID = $(this).attr('data-InstruData');	
		button = $(this);
		button_val = $(this).val();
		button_text = $(this).html();
		$.ajax({
			url : "<?php echo base_url('CommonController/DisableSpecificationInstru'); ?>",
			type : "POST",
			dataType : "JSON",
			data : {"OrderUID" : OrderUID, "InstructionUID" : InstructionUID},
			beforeSend: function(){
				$('.spinnerclass').addClass("be-loading-active");
				button.attr("disabled", true);
				button.html('Loading ...'); 
			},
			success: function (data) 
			{    
				button.html('Submit'); 
				button.removeAttr('disabled');         	        	
				$.notify(
				{
					icon:"icon-bell-check",
					message:data.message,
				},
				{
					type: data.type,
					delay:100,
				}); 
				jQuery('.alert-infonotify .close').trigger('click');   
				InstuctionSpecification();
			}
		});
	});









</script>
