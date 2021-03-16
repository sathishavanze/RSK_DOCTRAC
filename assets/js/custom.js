


$(document).ready(function(){ 

  $("select.select2picker").select2({
    //tags: false,
    theme: "bootstrap",
  });

  $(".nav-item").hover(function(){
    if($(this).children("a").hasClass("disabled")){  
      $(this).css("cursor" , "not-allowed")   
    }
  });

  $("body").on("click" , "input[type='email']" , function(){
    var emailidvalid  = $(this).val();
    var regex=/^[a-zA-Z0-9\.\_]+\@@{1}[a-zA-Z0-9]+\.\w{2,4}$/;
    if(!regex.test(emailidvalid))
    {         
      $(this).closest(".form-group").addClass("has-danger");
    }else{        
     $(this).closest(".form-group").removeClass("has-danger");
   }
   if(emailidvalid == ""){
    $(this).closest(".form-group").removeClass("has-danger");
  }
});

  $("body").on("change" , "input[type='email']" , function(){
   var emailidvalid  = $(this).val();
   var regex=/^[a-zA-Z0-9\.\_]+\@@{1}[a-zA-Z0-9]+\.\w{2,4}$/;
   if(!regex.test(emailidvalid))
   {         
    $(this).closest(".form-group").addClass("has-danger");
  }else{        
   $(this).closest(".form-group").removeClass("has-danger");
 }  
 if(emailidvalid == ""){
  $(this).closest(".form-group").removeClass("has-danger");
}
});



  //$('.perfectscrollbar').perfectScrollbar();
  //$('.dataTables_scrollBody').perfectScrollbar();
  //$('.modal-dialog .modal-content').perfectScrollbar();
  //$(".multiselect-container").perfectScrollbar();

  

  $('.jq-dte-day').attr("maxLength","2");
  $('.jq-dte-month').attr("maxLength","2");
  $('.jq-dte-year').attr("maxLength","4");
  $(".jq-dte-day").keydown(function(){
    if ((event.keyCode >= 65) &&  (event.keyCode <= 90) ||  (event.keyCode >=106 && event.keyCode <=109) || (event.keyCode >= 111) && (event.keyCode < 190) || (event.keyCode > 190) ) {
     event.preventDefault(); 
   } 
 });
  $(".jq-dte-month").keydown(function(){
    if ((event.keyCode >= 65) &&  (event.keyCode <= 90) ||  (event.keyCode >=106 && event.keyCode <=109) || (event.keyCode >= 111) && (event.keyCode < 190) || (event.keyCode > 190) ) {
     event.preventDefault(); 
   } 
 });
  $(".jq-dte-year").keydown(function(){
    if ((event.keyCode >= 65) &&  (event.keyCode <= 90) ||  (event.keyCode >=106 && event.keyCode <=109) || (event.keyCode >= 111) && (event.keyCode < 190) || (event.keyCode > 190) ) {
     event.preventDefault(); 
   } 
 });
  currencyformat();
  calldatetmask();   

  $('.contactnum').mask('(999) 999-9999');
  $("body").on("keyup" , ".contactnum" , function(e){     
   if(46==e.keyCode || 8==e.keyCode || 9==e.keyCode){
    var $this = $(this);
    if($this.val() == "(___)___-____")
      $this.val("");            
  }
});

  $("body").on("blur" , ".currency" , function(e){             
    var ele=$(this);
    var val = $(this).val();               
    if(val==='0.00' || val==='0' || val==='$0.00'){               
      $(this).val('0.00');
      $(this).parent().removeClass('is-dirty');
    }
    if($(this).val()!= 0){
      $(this).val($(this).val().split(" ").join(""));
      ele.formatCurrency();
    }
  });
  $( ".currency" ).trigger("blur");
  var keyDown = false, ctrl = 17, vKey = 86, Vkey = 118, Vdown=46;

  $("body").on("keypress" , ".currency", function (e) {
    if (!e) var e = window.event;
    if (e.keyCode > 0 && e.which == 0) return true;
    if (e.keyCode)    code = e.keyCode;
    else if (e.which) code = e.which;
    var character = String.fromCharCode(code);          
    if (character == '\b' || character == ' ' || character == '\t') return true;

    if ((code == vKey || code == Vkey || code == Vdown))  {                
     if (e.keyCode === 46 && this.value.split('.').length === 2) {
      return false   
    }else{
      return (character) 
    }
  }
  else return (/[0-9]$/.test(character));
});


  $('.currency').keydown(function (e) {
    if (e.keyCode == ctrl) keyDown = true;
  }).keyup(function (e) {
    if (e.keyCode == ctrl) keyDown = false;
  });


  $("body").on("change" , "select.select2picker" , function(){
    var sval  =  $(this).val();
    if(sval  == ""){
      $(this).closest(".form-group").removeClass("is-filled");
    }else{
      $(this).closest(".form-group").addClass("is-filled");
    }
  })

  // Sidebar
  window_width = $(window).width();
  fixed_plugin_open = $('.sidebar .sidebar-wrapper .nav li.active a p').html();


  if (window_width > 767 && fixed_plugin_open == 'Dashboard') {
    if ($('.fixed-plugin .dropdown').hasClass('show-dropdown')) {
      $('.fixed-plugin .dropdown').addClass('open');
    }
  }

  $('.fixed-plugin a').click(function(event) {
    // Alex if we click on switch, stop propagation of the event, so the dropdown will not be hide, otherwise we set the  section active
    if ($(this).hasClass('switch-trigger')) {
      if (event.stopPropagation) {
        event.stopPropagation();
      } else if (window.event) {
        window.event.cancelBubble = true;
      }
    }
  });




  });



$(".abstractordetails").click(function(e){
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
      $('#abstractormodal').remove();
      $('body').append(data);
      $('#abstractormodal').modal('show');
    },
    error: function(jqXHR, textStatus, errorThrown){

    }
  });

  $('#abstractordts').modal('toggle');
})

$(document).ready(function(){ 
  md.initFormExtendedDatetimepickers();
});


$(document).ready(function() {
  //ScrolltoTop_init();
  select_mdl();


  $(".popoveroption").click(function(){
  var ttop = $(".popover").css("top");
  getscrolltop();
  })
});

 function getscrolltop(){
    var ttop = $(".popover").css("top");
 }

function ScrolltoTop_init(){
  $('.scrollup').remove();
  var button = $('<a href="javascript:void(0);" class="scrollup" style="display: none;"><i class="icon-arrow-up8"></i></a>');
  button.appendTo("body");

  const container = document.querySelector('.main-panel');
  const ps = $('.main-panel').perfectScrollbar();
  var button = document.querySelector('.scrollup');
  button.addEventListener("click", ScrolltoTop);

  container.addEventListener('ps-scroll-down', function () {
    $('.scrollup').show();
    // const ps =  document.querySelector('.main-panel');
    // var psY = ps.querySelector('.ps-scrollbar-y');      
    // var topposition = psY.offsetTop;   
    // var dd  = $(".popover").css("top");
    // var avoid = "px";
    // var abc = dd.replace(avoid, '');
    //alert(abc);
    // var newtop =  parseInt(abc) - parseInt(topposition);
    // //alert(newtop);
    // $(".popover").css("top" , newtop);  
  });
  container.addEventListener('ps-y-reach-start', function () {
    $('.scrollup').hide(); 
  });
  container.addEventListener('ps-scroll-up', function () {   
  popovertop();  
  });
}


function currencyformat(){
  $(".currency").trigger("blur");
}


function popovertop(){
    const ps =  document.querySelector('.main-panel');
    var psY = ps.querySelector('.ps-scrollbar-y');      
    var topposition = psY.offsetTop;   
  //  alert(topposition);
    var dd  = $(".popover").css("top");
    var avoid = "px";
    // var abc=dd.replace(avoid, '');
    // var newtop = parseInt(abc) + parseInt(topposition);
    // $(".popover").css("top" , newtop);
    // console.log(newtop);
 
}
// function getposition(){
//  var topval =$("body .ps-scrollbar-y-rail").attr("style");
//  alert(topval);
// }


function ScrolltoTop() {
  $('.main-panel').animate({ scrollTop: 0 }, 600);
  $('.main-panel').scrollTop(0).perfectScrollbar('update');
}


function calldatetmask(){
  $(".date-entry1").datetextentry();
}
/*FOR SELECT2 COMPATABILITY*/

function select_mdl() {
  var $eventSelect = $("select.select2picker");
  $eventSelect.on("select2-opening", function () { 
    $(this).closest('.form-group').addClass("is-focused is-filled");
  });

  $eventSelect.on("select2-close", function () {
    $(this).closest('.form-group').removeClass("is-focused");
    var selected_value = $(this).val();
    if (selected_value==0 || selected_value=='' || selected_value==undefined) {
      $(this).closest('.form-group').removeClass("is-filled");
    } else {
      $(this).closest('.form-group').addClass("is-filled");
      if($(this).next().find('span.select2-selection').hasClass('errordisplay')){
        $(this).removeClass('is-invalid').closest('.form-group').removeClass("has-danger");
        $(this).next().find('span.select2-selection').removeClass('errordisplay');
      }
      if($(this).hasClass('is-invalid')){
        $(this).removeClass('is-invalid').closest('.form-group').removeClass("has-danger");
      }
    }
  });

  $eventSelect.each(function(){
    var selected_value = $(this).val();
    if (selected_value==0 || selected_value=='' || selected_value==undefined) {
      $(this).closest('.form-group').removeClass("is-filled");
    } else {
      $(this).closest('.form-group').addClass("is-filled");
    }
  });

  var $eventSelectTag = $(".mdl-select2-tags");
  $eventSelectTag.on("select2-opening", function () { 
    $(this).closest('.form-group').addClass("is-focused is-filled");
  });

  $eventSelectTag.on("select2-close", function () {
    $(".form-group").removeClass("is-focused");
    var selected_tag = $(this).closest('.form-group').find('.select2-selection__choice').hasClass('select2-selection__choice');
    if (selected_tag) {
      $(this).closest('.form-group').addClass("is-filled");
    } else {
      $(this).closest('.form-group').removeClass("is-filled");
    }
  });

  $eventSelectTag.on("change", function(){
    if ($('.select2-selection__rendered li').hasClass('select2-selection__choice')) {
      $(this).closest('.form-group').addClass("is-filled");
    } else {
      $(this).closest('.form-group').removeClass("is-filled");
    }
  });

  $eventSelectTag.each(function(){
    var selected_tag = $(this).closest('.form-group').find('.select2-selection__choice').hasClass('select2-selection__choice');
    if (selected_tag) {
      $(this).closest('.form-group').addClass("is-filled");
    } else {
      $(this).closest('.form-group').removeClass("is-filled");
    }
  });

};




$( document ).ajaxComplete(function() {
  select_mdl();
});

/*FOR SELECT2 COMPATABILITY*/

$(document).on('select2-close', 'select.select2picker', function(event) {
  event.preventDefault();
  /* Act on the event */
  $(this).closest('.form-group').removeClass("is-focused");
  var selected_value = $(this).val();
  if (selected_value==0 || selected_value=='' || selected_value==undefined) {
    $(this).closest('.form-group').removeClass("is-filled");
  } else {
    $(this).closest('.form-group').addClass("is-filled");
    $(this).removeClass('is-invalid').closest('.form-group').removeClass("has-danger");
    if($(this).next().find('span.select2-selection').hasClass('errordisplay')){
      $(this).next().find('span.select2-selection').removeClass('errordisplay');
    }
  }
});

$(document).ready(function() {
 $(document).on('blur', 'input[type=text]', function(event) {
  event.preventDefault();
  /* Act on the event */
  var ele = $(event.currentTarget);
  if(ele.val().length != 0){
    ele.removeClass('is-invalid').closest('.form-group').removeClass("has-danger");
  }
});

 $(document).on('blur', '.check-invalid', function(event) {
  event.preventDefault();
  /* Act on the event */
  var ele = $(event.currentTarget);
  if(ele.val().length != 0){
    ele.removeClass('is-invalid').closest('.form-group').removeClass("has-danger");
  }
});
});


/*GENERAL FUNCTIONS*/
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

function myfunction(){
 $.notify(
 {
  icon:"icon-bell-check",
  message:"Record deleted successfully"
},
{
  type:"success",
  delay : 1000 
});
}

function ChangeCustomerFileDetails(file, data) {

  fsize  = bytesToSize(file.size);   
  var fname = file.name;    
  var appeddiv  = "<div class='row filediv'><div class='col-md-2'><p class='mb-0'><strong>"+fname+"</strong></p><p><strong>"+fsize+"</strong></p></div><div class='col-md-10'><a href='"+data.URL+"' target='_blank' class='btn btn-sm btn-outline-info defaultfileview'><i class='icon-eye'></i></a><button class='btn btn-outline-danger btn-sm customerdocumentremove_server'><i class='icon-x'></i></button></div></div>";
  $(".uploadedfile").html(appeddiv);   
}

function ShowViewDocumentLink(URL, currentform) {
  var view='<a class="btn btn-link btn-dribbble" href="'+URL+'" target="_blank"><i class="icon-eye"></i> View Document</a>'
  var file= $(currentform).find('.viewdocumentcontainer');
  $(currentform).find('.removeabstractordoc').addClass('removeabstractordocserver');
  $(currentform).find('.removeabstractordoc').removeClass('removeabstractordoc');
  $(currentform).find('.viewdocumentcontainer').html(view);
}

function ResetProgress(progress) {
  $(progress).width(0 + '%');
  $(progress).text('');
  $(progress).parent('.progress').hide(); 
}

function bytesToSize(bytes) {
 var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
 if (bytes == 0) return '0 Byte';
 var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
 return (bytes / Math.pow(1024, i)).toFixed(2) + ' ' + sizes[i];
};
function findParentForm(elem){ 
  var parent = elem.parentNode; 
  if(parent && parent.tagName != 'FORM'){
    parent = findParentForm(parent);
  }
  return parent;
}

function getParentForm( elem )
{
  var parentForm = findParentForm(elem);
  if(parentForm){
    return parentForm;
  }else{
    alert("unable to locate parent Form");
  }

}

function findParentElement(elem, parentClass){ 
  var parent = elem.parentNode; 
  var classlist = parent.classList;
  var ispresent=$.inArray(parentClass, classlist);
  if(parent && ispresent ==-1){
    parent = findParentElement(parent, parentClass);
  }
  return parent;
}

function getParentByClass(elem, parentClass) {
  var parentElement=findParentElement(elem, parentClass);
  if (parentElement) {
    return parentElement;
  }
}

function calcTime(city, offset) {
    // create Date object for current location
    var d = new Date();

    // convert to msec
    // subtract local time zone offset
    // get UTC time in msec
    var utc = d.getTime() + (d.getTimezoneOffset() * 60000);

    // create new Date object for different city
    // using supplied offset
    var nd = new Date(utc + (3600000*offset));

    console.log(nd.toISOString());

    return nd.toISOString().slice(0,10);
    // return time as a string
    return "The local time for city "+ city +" is "+ nd.toLocaleString();
  }

// alert(calcTime('Caribbean', '-5'));


function bytesToSize(bytes) {
 var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
 if (bytes == 0) return '0 Byte';
 var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
 return (bytes / Math.pow(1024, i)).toFixed(2) + ' ' + sizes[i];
};
function findParentForm(elem){ 
  var parent = elem.parentNode; 
  if(parent && parent.tagName != 'FORM'){
    parent = findParentForm(parent);
  }
  return parent;
}

function getParentForm( elem )
{
  var parentForm = findParentForm(elem);
  if(parentForm){
    return parentForm;
  }else{
    alert("unable to locate parent Form");
  }

}

function findParentElement(elem, parentClass){ 
  var parent = elem.parentNode; 
  var classlist = parent.classList;
  var ispresent=$.inArray(parentClass, classlist);
  if(parent && ispresent ==-1){
    parent = findParentElement(parent, parentClass);
  }
  return parent;
}

function getParentByClass(elem, parentClass) {
  var parentElement=findParentElement(elem, parentClass);
  if (parentElement) {
    return parentElement;
  }
}

function calcTime(city, offset) {
    // create Date object for current location
    var d = new Date();

    // convert to msec
    // subtract local time zone offset
    // get UTC time in msec
    var utc = d.getTime() + (d.getTimezoneOffset() * 60000);

    // create new Date object for different city
    // using supplied offset
    var nd = new Date(utc + (3600000*offset));


    dformat = [(nd.getMonth()+1),
               nd.getDate(),
               nd.getFullYear()].join('/') +' ' +
              [nd.getHours(),
               nd.getMinutes(),
               nd.getSeconds()].join(':');
    console.log(dformat);

    return dformat;
    // return time as a string
    return "The local time for city "+ city +" is "+ nd.toLocaleString();
  }

// alert(calcTime('Caribbean', '-5'));


function ResetProgress(progress) {
  $(progress).width(0 + '%');
  $(progress).text('');
  $(progress).parent('.progress').hide(); 
  $(progress).find('span').text(''); 
}

function callselect2() {
  $("select.select2picker").select2({
    //tags: false,
    theme: "bootstrap",
  });
}

function callselect2byclass(byclass) {
  $('.' + byclass).select2({
    //tags: false,
    theme: "bootstrap",
  });
}

function callselect2byid(byid) {
  $('#' + byid).select2({
    //tags: false,
    theme: "bootstrap",
  });
}


$(document).off('click','.Reports').on('click','.Reports',function(){
  $("#Reports").slideToggle();
});




//call back for notify js
function redirecturl(url) {
	return function() {
		window.location.href = url;

	};
}

var lastValue = $('#adv_CustomerUID').val();
$(document).on('change','#adv_CustomerUID',function(event){
	var ele = $(this);
	var adv_CustomerUID = ele.val();

	swal({
		text: "Would you like to change the client?",
		type: 'info',
		showCancelButton: true,
		confirmButtonClass: 'btn btn-success',
		cancelButtonClass: 'btn btn-danger',
		buttonsStyling: false,
		showLoaderOnConfirm: true,
		preConfirm: function() {
			return new Promise(function(resolve, reject) {
				if(adv_CustomerUID == ''){
					ele.closest('.form-group').removeClass('has-success').addClass('has-danger is-focused');
					reject('Please select client');
				}else{
					$.ajax({
						type: "POST",
						url :base_url+"Profile/setDefaultClient",
						data:{'adv_CustomerUID':adv_CustomerUID},
						success: function(data)
						{
              var obj = $.parseJSON( data );
              if(obj.DefaultScreen != null && obj.DefaultScreen != '') {
                window.location.href = base_url + obj.DefaultScreen;
              } else {
                window.location.href = base_url;
              }

						}
					});
				}
			});
		}
	}).then(function() {}, function(dismiss) {
		$("#adv_CustomerUID").val(lastValue).trigger('change.select2');
	}).catch(swal.noop);
});


//clear previous datatable request
$(function() {
  $('table').on('preXhr.dt', function ( e, settings, data ) {
    if (settings.jqXHR) settings.jqXHR.abort();
  })
});


function loadbackground(){       
  $sidebar = $('.sidebar');
  $sidebar_img_container = $sidebar.find('.sidebar-background');
  $full_page = $('.full-page');
  $sidebar_responsive = $('body > .navbar-collapse');
  window_width = $(window).width();
  fixed_plugin_open = $('.sidebar .sidebar-wrapper .nav li.active a p').html();
  $.ajax({
   type : "GET",
   url : "Profile/getuserDetails",    
   async : false,
   cache : false,
   success : function(data){ 
    profiledata =  JSON.parse(data);
    ProfileColor = profiledata.ProfileColor;
    ProfileBackground = profiledata.ProfileBackground;
    SidebarActive = profiledata.SidebarActive;
    SidebarBackgroundActive = profiledata.SidebarBackgroundActive;
    SidebarBackground = profiledata.SidebarBackground;
    var cardclass  = "";
    var activeProfileColor = '';
    var activeProfileBackground = '';
    var activeSidebarBackground = '';
    if(ProfileColor  == "1"){
      activeProfileColor = "purple";
      cardclass = "card-header-primary"; 
    }else if(ProfileColor == "2"){
      activeProfileColor = "azure";
      cardclass = "card-header-info";  
    }else if(ProfileColor == "3"){
      activeProfileColor = "green";
      cardclass = "card-header-success"; 
    }else if(ProfileColor == "4"){
      activeProfileColor = "orange";
      cardclass = "card-header-warning";  
    }else if(ProfileColor == "5"){
      activeProfileColor = "danger";
      cardclass = "card-header-danger"; 
    }else if(ProfileColor == "6"){
      activeProfileColor = "rose";
      cardclass = "card-header-rose"; 
    }
    if(ProfileBackground == "1"){
      activeProfileBackground = "black";
    }else if(ProfileBackground == "2"){
      activeProfileBackground = "white";
    }else if(ProfileBackground == "3"){
      activeProfileBackground = "red";
    }
    if(SidebarBackground == "1"){
      activeSidebarBackground = "http://localhost/d2trevampnew/assets/img/sidebar-1.jpg";
    }else if(SidebarBackground == "2"){
      activeSidebarBackground = "http://localhost/d2trevampnew/assets/img/sidebar-2.jpg";
    }else if(SidebarBackground == "3"){
      activeSidebarBackground = "http://localhost/d2trevampnew/assets/img/sidebar-3.jpg";
    }else if(SidebarBackground == "4"){
      activeSidebarBackground = "http://localhost/d2trevampnew/assets/img/sidebar-4.jpg";
    }
    $sidebar.attr('data-color', activeProfileColor);
    $sidebar_responsive.attr('data-color', activeProfileColor);
    $full_page.attr('filter-color', activeProfileColor);
    $sidebar.attr('data-background-color', activeProfileBackground);
    $sidebar_responsive.attr('data-color', activeProfileBackground);
    if(SidebarBackgroundActive == "0"){
      $(".switch-sidebar-image input").attr("checked" , false);
    }else{
      $(".switch-sidebar-image input").attr("checked" , true);
    }
    if(SidebarActive == "1"){
      $(".switch-sidebar-mini input").attr("checked" , false);
      $("body").removeClass("sidebar-mini");

    }else{
      $(".switch-sidebar-mini input").attr("checked" , true); 
      $("body").addClass("sidebar-mini");
    }
    if(SidebarBackgroundActive == "1"){
      $(".sidebar-background").css('background-image' , 'url("' + activeSidebarBackground + '")');
      $(".sidebar").attr("data-image" , activeSidebarBackground);
    }else{
      $(".sidebar").removeAttr("data-image");
      $("sidebar-background").hide();
    }
  }
});
}

function fn_commondatatable(tableselector,ReportName='file')
{

  fndatatable = $(tableselector).DataTable( {
    scrollX:        true,
    scrollCollapse: true,
    fixedHeader: false,
    scrollY: '100vh',
    paging:  true,
    searchDelay:1500,
    //"bInfo" : false,
    "bDestroy": true,
    "autoWidth": true,
    "processing": true, //Feature control the processing indicator.
    "serverSide": false, //Feature control DataTables' server-side processing mode.
    "order": [], //Initial no order.
    "pageLength": 10, // Set Page Length
    "lengthMenu":[[10, 25, 50, 100], [10, 25, 50, 100]],
    fixedColumns: {
      leftColumns: 1,
      rightColumns: 1
    },
    dom: 'lBfrtip',
    "buttons": [
    {
      extend: 'csv',
      title: ReportName,
    },
    {
      extend: 'excelHtml5',
      title: ReportName,
      footer: true,
    },
    ],

    language: {
      processing: '<svg class="d2tspinner-circular" viewBox="25 25 50 50"><circle class="d2tspinner-path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/></svg>',

    },
    "columnDefs": [ {
      "targets": 'no-sort',
      "orderable": false,
    } ],
  });

  return fndatatable;
}


// $(document).on('change','#adv_CustomerUID',function(){
// 	var ele = $(this);
// 	var adv_CustomerUID = ele.val();


// 	swal({
// 		text: "Would you like to change the client?",
// 		type: 'info',
// 		showCancelButton: true,
// 		confirmButtonClass: 'btn btn-success',
// 		cancelButtonClass: 'btn btn-danger',
// 		buttonsStyling: false,
// 		showLoaderOnConfirm: true,
// 		preConfirm: function() {
// 			return new Promise(function(resolve, reject) {
// 				if(adv_CustomerUID == ''){
// 					ele.closest('.form-group').removeClass('has-success').addClass('has-danger is-focused');
// 					reject('Please select client');
// 				}else{
// 					$.ajax({
// 						type: "POST",
// 						url :base_url+"Profile/setDefaultClient",
// 						data:{'adv_CustomerUID':adv_CustomerUID},
// 						success: function(data)
// 						{
// 							location.href = location.href
// 						}
// 					});
// 				}
// 			});
// 		}
// 	}).catch(swal.noop);
// });


//open in background tab
function openNewBackgroundTab(redirecturl){
  var a = document.createElement("a");
  a.href = redirecturl;
  var evt = document.createEvent("MouseEvents");
  //the tenth parameter of initMouseEvent sets ctrl key
  evt.initMouseEvent("click", true, true, window, 0, 0, 0, 0, 0,
    true, false, false, false, 0, null);
  a.dispatchEvent(evt);
}

function open_backgroundtab(redirecturl){
  var is_chrome = navigator.userAgent.toLowerCase().indexOf('chrome') > -1;
  if(!is_chrome)
  {
    window.open(redirecturl, '_blank');
  } else {
    openNewBackgroundTab(redirecturl);
  }
}


function getFormattedDate(date) {
  var year = date.getFullYear();

  var month = (1 + date.getMonth()).toString();
  month = month.length > 1 ? month : '0' + month;

  var day = date.getDate().toString();
  day = day.length > 1 ? day : '0' + day;
  
  return month + '/' + day + '/' + year;
}