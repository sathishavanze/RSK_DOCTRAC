 $(document).off('click','.listorders').on('click','.listorders',function() {
      var OrderUID = $(this).attr('data-orderid');
      var Orderlistname = $(this).attr('data-title');

      if(!OrderUID || OrderUID.length === 0) {
        $.notify(
        {
          icon:"icon-bell-check",
          message:'No Orders Available'
        },
        {
          type:'danger',
          delay:1000 
        });
        return false;
      }

      fetchorders(OrderUID,Orderlistname)
    });


      function fetchorders(OrderUID,Orderlistname)
  {
    $(".cyclereportdiv").fadeOut('fast');
    $("#orderstablediv").fadeIn('fast');
    $('#orderlisttitle').text(Orderlistname);
    $('#orderlist_orderuids').val(OrderUID);
    var WorkflowModuleUID = '';
    orderslist = $('#orderslist').DataTable( {
      scrollX: true,
      scrollY: "300px",
      scrollCollapse: true,
      fixedHeader: false,
      paging:  true,
      "bDestroy": true,
      searchDelay:1500,
      "autoWidth": true,
      "processing": true, //Feature control the processing indicator.
      "serverSide": true, //Feature control DataTables' server-side processing mode.
      "order": [], //Initial no order.
      "pageLength": 10, // Set Page Length
      "lengthMenu":[[10, 25, 50, 100], [10, 25, 50, 100]],
      // fixedColumns: {
      //   leftColumns: 2,
      //   rightColumns: 1
      // },

      language: {
        sLengthMenu: "Show _MENU_ Orders",
        emptyTable:     "No Orders Found",
        info:           "Showing _START_ to _END_ of _TOTAL_ Orders",
        infoEmpty:      "Showing 0 to 0 of 0 Orders",
        infoFiltered:   "(filtered from _MAX_ total Orders)",
        zeroRecords:    "No matching Orders found",
        processing: '<svg class="d2tspinner-circular" viewBox="25 25 50 50"><circle class="d2tspinner-path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/></svg>',

      },

      // Load data for the table's content from an Ajax source
      "ajax": {
        "url": base_url+"CycleTimeReport/fetchorders",
        "type": "POST",
        "data" : {'OrderUID':OrderUID}  
      },
      "columnDefs": [ {
        "targets": 'no-sort',
        "orderable": false,
      } ],
      createdRow:function(row,data,index){
},
      "fnDrawCallback": function (oSettings) {
      },
    });
  }

  $(document).off('click','.excelorderCountlist').on('click','.excelorderCountlist',function(){

    var OrderUID = $('#orderlist_orderuids').val();
    var fileName =  $('#orderlisttitle').text();
    var filename = fileName+'.xlsx';
    $.ajax({
      type: "POST",
      url: base_url+'CycleTimeReport/WriteListExcel',
      xhrFields: {
        responseType: 'blob',
      },
      data: {'OrderUID':OrderUID,'filename':filename},
      beforeSend: function(){


      },
      success: function(data)
      {
        if (typeof window.chrome !== 'undefined') {
          //Chrome version
          var link = document.createElement('a');
          link.href = window.URL.createObjectURL(data);
          link.download = filename;
          link.click();
        } else if (typeof window.navigator.msSaveBlob !== 'undefined') {
          //IE version
          var blob = new Blob([data], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
          window.navigator.msSaveBlob(blob, filename);
        } else {
          //Firefox version
          var file = new File([data], filename, { type: 'application/octet-stream' });
          window.open(URL.createObjectURL(file));
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {

        console.log(jqXHR);


      },
      failure: function (jqXHR, textStatus, errorThrown) {

        console.log(errorThrown);

      },
    });

  });

    $(document).on("click" , ".orderclose" , function(){
      $("#orderstablediv").fadeOut('fast');
      $(".cyclereportdiv").fadeIn('fast');
      $($.fn.dataTable.tables(true)).DataTable().columns.adjust().fixedColumns().relayout().responsive.recalc();
    })