<style type="text/css">
  .pd-btm-0 {
    padding-bottom: 0px;
  }

  .margin-minus8 {
    margin: -8px;
  }

  .mt--15 {
    margin-top: -15px;
  }

  .bulk-notes {
    list-style-type: none
  }

  .bulk-notes li:before {
    content: "*  ";
    color: red;
    font-size: 15px;
  }

  .nowrap {
    white-space: nowrap
  }

  .table-format>thead>tr>th {
    font-size: 12px;
  }

  table.dataTable thead .sorting:after, table.dataTable thead .sorting_asc:after, table.dataTable thead .sorting_asc_disabled:after, table.dataTable thead .sorting_desc:after, table.dataTable thead .sorting_desc_disabled:after {
    bottom: 0px;
    right: 3em;
    content: "\2193";
  }

  table.dataTable thead .sorting:before, table.dataTable thead .sorting_asc:before, table.dataTable thead .sorting_asc_disabled:before, table.dataTable thead .sorting_desc:before, table.dataTable thead .sorting_desc_disabled:before {
    bottom: 0px;
    right: 2em;
    content: "\2191";
  }
  

</style>

<div class="card mt-20 customcardbody" id="Orderentrycard">
  <div class="card-header card-header-danger card-header-icon">
    <div class="card-icon">Dynamic Column
    </div>
  </div>


  <div class="card-body">
    <!--
      <div class="row justify-content-md-center">
      -->
      <div class="col-md-12 col-xs-12">
        <div class="material-datatables1">
          <table id="Dynamicqueue" style="border:1px solid #ddd; white-space:nowrap;width:100%" class="table table-striped  table-hover order-column">
            <thead>
              <tr>
                <th class="text-center" style="width: 5%;" >S.No</th>
                <th class="text-left" style="width: 40%;" >Name</th>
                <th class="text-left" style="width: 5%;" >Action</th>
              </tr>
            </thead>
            <tbody>
              <?php $i=1; 
              foreach ($Modules as $key => $value) {
                ?>
                <tr>
                  <td class="text-center"><?php echo $i; ?></td>
                  <?php 
                  if($value->WorkflowModuleUID){
                    $Workflowqueue = $value->WorkflowModuleName;
                    $workflow =$value->WorkflowModuleUID;
                  }
                  if($value->Section && $value->Section != '') {
                   $Workflowqueue = $value->Section;
                   $workflow =$value->Section;

                 }
                 ?>
                 <td ><?php echo $value->WorkflowModuleName; ?></td>
                 <td><span style="text-align: left;width:50%;"><a  href="DynamicColumn/workflowlist/<?php echo $workflow;?>" class="btn btn-link btn-info btn-just-icon btn-xs ajaxload"><i class="icon-pencil"></i></a></span></td>
               </tr>

               <?php $i++; } ?>


               <!-- <?php foreach ($SectionDetails as $Section => $SectionName) { ?>
                <tr>
                  <td class="text-center"><?php echo $i; ?></td>

                  <td ><?php echo $SectionName; ?></td>
                  <td><span style="text-align: left;width:50%;"><a  href="DynamicColumn/workflowlist/<?php echo $Section;?>" class="btn btn-link btn-info btn-just-icon btn-xs ajaxload"><i class="icon-pencil"></i></a></span></td>
                </tr>
              <?php $i++; } ?> -->




            </tbody>
          </table>
        </div>
        <!--
        </div>
      -->
    </div>
  </div>
</div>

<script type="text/javascript">

 /* 
 *Dynamic column workflow table show
 *author Sathis Kannan(sathish.kannan@avanzegroup.com)
 *since Date:27-Jul-2020
 */

 $(document).ready(function() {
   $('#Dynamicqueue').DataTable({
    paging:true,
    scrollX:true,

  });



 });

</script>