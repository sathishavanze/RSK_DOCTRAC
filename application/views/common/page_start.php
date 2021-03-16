<!DOCTYPE html>
<html lang="en" id="slim_1">    

<head>
	<base href="<?php echo base_url(); ?>">
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<meta charset="utf-8" />
	<!-- <link rel="apple-touch-icon" sizes="76x76" href="assets/img/apple-icon.png"> -->
	<link rel="icon" type="image/png" href="assets/img/favicon.png">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<title>Doctrac</title>
	<meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
	<meta name="keywords" content="Doctrac">
	<meta name="description" content="">
	<!-- Schema.org markup for Google+ -->
	<meta itemprop="name" content="">
	<meta itemprop="description" content="">
	<link rel="stylesheet" type="text/css"  href="assets/icon/css/ionicons.css" />
	<link href="assets/css/icomoon.css" rel="stylesheet" type="text/css"/>
	<link rel="stylesheet" href="assets/css/font-awesome-4.7.0/font-awesome.min.css" type="text/css">
	<link href="assets/css/material-dashboard.min.css?v=2.0.2&reload=2" rel="stylesheet" type="text/css"/>
	<link href="assets/demo/demo.css" rel="stylesheet" type="text/css"/>
	
	<link href="assets/css/fixedColumns.dataTables.min.css" rel="stylesheet" type="text/css"/>
	<link rel="stylesheet" type="text/css" href="assets/css/jquery.datetextentry.css" />
	<link href="assets/css/jquerysctipttop.css" rel="stylesheet" type="text/css"/>
	<!-- <link rel="stylesheet" type="text/css" href="assets/css/select2/select2.min.css" /> -->
	
<link rel="stylesheet" type="text/css"  href="assets/plugins/select2_old/select2.css" />
	<link rel="stylesheet" type="text/css" href="assets/css/select2/select2-bootstrap.css" />
	<link rel="stylesheet" type="text/css"  href="<?php echo base_url();?>assets/css/select2/pmd-select2.css" />
	<link href="assets/css/style.css?reload=1.10.4" rel="stylesheet" type="text/css"/>
	<link href="assets/css/responsive.dataTables.min.css" rel="stylesheet" media="screen">
	<link href="assets/css/scrolltabs.css" rel="stylesheet" />
	<link href="assets/lib/js-conveyer/jquery.jConveyorTicker.min.css" rel="stylesheet" />

	<!-- 	<link href="assets/plugins/bootstrap4-editable/css/bootstrap-editable.css" rel="stylesheet" -->
	<link href="assets/plugins/nprogress/nprogress.css" rel="stylesheet">
	<!-- js Files -->

	<!-- Base Tag Useful for using replacement base_url everywhere -->
	<script src="assets/js/core/jquery.min.js" type="text/javascript" ></script>
	<script src="assets/js/load.js?reload=1.0.1" type="text/javascript" ></script>	
	<script src="assets/plugins/nprogress/nprogress.js"  type="text/javascript"></script>



	<script type="text/javascript">
		const base_url = '<?php echo base_url(); ?>';
		const USERNAME = '<?php echo $this->session->userdata('UserName') ?>';
		const USERUID = '<?php echo $this->session->userdata('UserUID') ?>';
		
		var MENU_URL='';
	</script>
	<style>
		.select2{
			display:block !important;
			width:100%  !important;
		}
		@media (min-width: 576px){
			#abstractormodal .modal-dialog{
				max-width: 1000px;
			}
		}
		#abstractormodal .modal-dialog{
			margin-top: 20px !important;
		}
		#abstractormodal p{
			font-size: 12px;
		}		

		.swal2-container.swal2-in {
			z-index: 9999999999999;
		}
		#navigation-example{
			height: 45px !important;
			padding: 0px !important;
		}
		.btn.btn-fab, .btn.btn-just-icon {
			font-size: 24px;
			height: 35px !important;
			min-width: 35px !important;
			width: 35px !important;
			padding: 0;
			overflow: hidden;
			position: relative;
			line-height: 35px !important;
		}
		#containerstart{
			margin-top: 48px !important;
		}
		/*.sidebar .logo{
			padding: 0px !important;
		}
		.sidebar .user{
			padding: 0px !important;
			margin: 0px !important;
			
		}
		.sidebar .user .photo {
			width:26px !important;
			height: 25px ! important;
			margin: 4px 4px 4px 0px  !important;
			margin-left:24px !important;
			}*/
			.slimScrollBar{
				background: #795548  !important;
			}
			#Followup
			{
				background: #211616 !important;
			}
			#headers .badge {
				border-radius: 15px !important;
			}	
			
			body{
				background: white !important;
			}
		</style>

	</head>
