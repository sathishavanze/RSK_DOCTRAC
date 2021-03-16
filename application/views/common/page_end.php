<!-- End -->

</body>

<script src="assets/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="assets/js/jquery.slimscroll.min.js"></script>
<!-- Sidebar -->
<script type="text/javascript">
	$(document).on('click','.Reports',function(){
		$("#Reports").slideToggle();
	});
</script>
<script type="text/javascript">
	function menucount()
	{	
		$.ajax({
			type: "POST",
			url: '<?php echo base_url();?>CommonController/GetCounts',
			dataType:"json",
			success: function(data)
			{

				Object.keys(data).map(function(key, value) {
					//mega menu
					$('.link-'+key).find('.count').remove();
					$('.link-'+key).append('<span class="count">'+data[key]+'</span>');	
					//sidebar
					$('.sidebarlink-'+key+' p').find('span').text('');
					$('.sidebarlink-'+key+' p').find('span').text(data[key]);	
				});

			},
			error: function(jqXHR, textStatus, errorThrown){

			}
		});
	}
	$(document).ready(function(){

		$('.sidebar-wrapper').slimScroll({
			height: 'intial'
		});

		$('html').removeClass('perfect-scrollbar-off');
		$('html').addClass('perfect-scrollbar-on');

		$('#form_search').off('submit').on('submit', function (e) {  
			e.preventDefault();
			e.stopPropagation();
			
			var interval = setInterval(function() { NProgress.inc(); }, 1000);  

			var formdata = new FormData($(this)[0])
			$.ajax({
				type: "POST",
				url: '<?php echo base_url(); ?>Search',
				data: formdata,
				processData: false,
				contentType: false,
				beforeSend: function () {  
					addbodyspinner();
				},
				success: function (response, textStatus, jqXHR) {

					$('#loadcontent').html(response);
					load_success = true;
					$(document).ready(function() {
						clearInterval(interval);
						NProgress.done();
						$("select.select2picker").select2({theme: "bootstrap"});
						ScrolltoTop_init();
						select_mdl();
						removebodyspinner();
					});	
				},
				error: function (jqXHR) {  

				}
				
			});
		})

	});
	$(document).on('click', ".abstractordetails", function(e){
		e.preventDefault();

		var abstractoruid=$(this).closest('tr').attr('data-id');

		if (abstractoruid=='' || typeof abstractoruid =='undefined') {
			swal({
				title: "<i class='icon-warning iconwarning'></i>", 
				html: "<p>Invalid Request</p>",
				confirmButtonClass: "btn btn-success",
				allowOutsideClick: true,
				width: '300px',
				buttonsStyling: false
			}).catch(swal.noop);
			return;
		}
		$.ajax({
			type: "POST",
			url: '<?php echo base_url();?>Customer/GetAbstractorDetailsView',
			data:{"AbstractorUID":abstractoruid},
			success: function(data)
			{
				console.log(data);
				$('#abstractormodal').remove();
				$('body').append(data);
				$('#abstractormodal').modal('show');
			},
			error: function(jqXHR, textStatus, errorThrown){

			}
		});

	})


	/*--- GENERAL FUNCTIONS ---*/
	function callselect2(){
		$("select.select2picker").select2({
			//tags: false,
			theme: "bootstrap",
		});
	}

	function callselect2byclass(byclass){
		$('.'+byclass).select2({
			//tags: false,
			theme: "bootstrap",
		});
	}

	function callselect2byid(byid){
		$('#'+byid).select2({
			//tags: false,
			theme: "bootstrap",
		});
	}
	/*--- GENERAL FUNCTIONS ---*/

	removebodyspinner();
	$(window).on('load', function () {
		setTimeout(function () {
			menucount();
		}, 3000);
	});


</script>


</html>
