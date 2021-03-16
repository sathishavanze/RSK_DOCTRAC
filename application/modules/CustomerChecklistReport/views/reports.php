<link href="<?php echo base_url(); ?>assets/css/progresccircle.css" rel="stylesheet" type="text/css" />
<style>

</style>

<div class="card mt-20 customcardbody">
    <div class="card-header card-header-danger card-header-icon card_theme_color">
        <div class="card-icon">
            Reports List
        </div>
        <div class="row">
            <div class="col-md-6">
                <!-- <h4 class="card-title">Groups List</h4> -->
            </div>
            <div class="col-md-6 text-right">
                <a id="reportsmodalpopup" style="padding:0px 10px !important" class="btn btn-success btn-sm mt-10 cardaddinfobtn"><i class="pr-10 icon-plus22
        "></i>Add Report</a>
            </div>
        </div>
    </div>
    <div class="card-body ">
        <div class="col-md-12 col-xs-12">
            <div class="material-datatables">
                <table id="Checkllisttable" style="border:1px solid #ddd; white-space:nowrap;width: 100%;" class="table table-striped">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 7%;">S.No</th>
                            <th class="text-left" style="width: 50%;">Report Name</th>
                            <th class="text-left" style="width: 50%;">Workflows</th>
                            <th class="text-left" style="width:7%;">Active</th>
                            <th class="text-left" style="width:7%;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $sno = 1;
                        foreach ($ReportsDetails as $key => $value) { ?>
                            <tr>
                                <td class="text-center"><?php echo $sno; ?></td>
                                <td class="text-left"><?php echo $value['ReportName']; ?></td>
                                <td class="text-left"><?php 
                                foreach ($CustomerWorkflow as $keys => $values) { 
                                    if ($values['WorkflowModuleUID'] == $value['WorkflowModuleUID'])
                                    {
                                        echo $values['SystemName'];
                                    }
                                }
                                ?></td>
                                <td class="text-left">
                                    <div class="togglebutton">
                                      <label class="label-color">
                                        <input type="checkbox" id="Active" name="Active" class="Active" 
                                        <?php if($value['Active'] == 1) { 
                                            echo "checked"; 
                                        ?>
                                        <?php } ?> data-reportuid="<?php echo $value['ReportUID']; ?>">
                                        <span class="toggle"></span>
                                    </div>
                                </td>
                                <td style="text-align: left"> 
                                    <span style="text-align: left;width:100%;">
                                      <a href="<?php echo base_url(); ?>CustomerChecklistReport/editreport/<?php echo $value['ReportUID']; ?>" class="btn btn-link btn-info btn-just-icon btn-xs" title="Report Setup"><i class="icon-eye"></i></a>
                                    </span>
                                    <span style="text-align: left;width:100%;">
                                      <a class="btn btn-link btn-info btn-just-icon btn-xs editreport" data-reportname="<?php echo $value['ReportName']; ?>" data-workflowmoduleuid="<?php echo $value['WorkflowModuleUID']; ?>" data-reportuid="<?php echo $value['ReportUID']; ?>" title="Edit Report Name"><i class="icon-pencil"></i></a>
                                    </span>
                                </td>
                            </tr>
                        <?php $sno++; } ?>                        
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="reportsmodal" tabindex="-1" role="dialog" aria-labelledby="reportsmodalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form class="form-horizontal" id="frmreportsmodal" action="#" method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="reportsmodalLabel">Add Report</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">

                        <div class="row col-md-12">

                            <div class="col-sm-6">
                                <div class="form-group bmd-form-group">
                                    <label for="ReportName" class="bmd-label-floating">Report Name <span class="mandatory"></span></label>
                                    <input type="text" class="form-control" id="ReportName" name="ReportName" />
                                    <input type="hidden" name="ReportUID" id="ReportUID">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group bmd-form-group">
                                    <label for="WorkflowModuleUID" class="bmd-label-floating">Workflow</label>
                                    <select class="select2picker form-control"  id="WorkflowModuleUID" name="WorkflowModuleUID" required>
                                        <option value="empty">Select Workflow</option>
                                        <?php foreach ($CustomerWorkflow as $key => $value) { ?>
                                            <option value="<?php echo $value['WorkflowModuleUID']; ?>"><?php echo $value['SystemName']; ?></option>
                                        <?php } ?>                              
                                    </select>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-success btnreportsmodal" name="submit" type="submit" id="btnreportsmodal" value="reportsmodal">Save</button>                    
                </div>
            </form> 
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('#Checkllisttable').dataTable({
            responsive: true,
            // dom: 'Bfrtip',
            // buttons: [
            //     'excel', 'pdf'
            // ],
        });

        //Report Add
        $(document).off('click', '.btnreportsmodal').on('click', '.btnreportsmodal', function (e) {
            e.preventDefault();
            var ReportUID = $('#ReportUID').val();
            var ReportName = $('#ReportName').val();
            var WorkflowModuleUID = $('#WorkflowModuleUID').val();
            $.ajax({
                type:'POST',
                dataType: 'JSON',
                global: false,
                url:'<?php echo base_url();?>CustomerChecklistReport/addreports',
                data: {'ReportUID':ReportUID,'ReportName':ReportName,'WorkflowModuleUID':WorkflowModuleUID},
                success: function(data)
                {
                    console.log(data);
                    $.notify({icon:"icon-bell-check",message:data.message},{type:data.type,delay:1000 });
                    if (data.validation_error == 0) {
                        window.setTimeout(function(){location.reload()},3000)
                        $('#reportsmodal').modal('toggle');
                    }
                }
            });
        });

        //Report Edit
        $(document).off('click', '.editreport').on('click', '.editreport', function (e) {
            e.preventDefault();
            $('#WorkflowModuleUID').val('empty').parent().addClass('is-filled').trigger('change');
            $('#WorkflowModuleUID').trigger('change');
            $('#reportsmodalLabel').html('Edit Report');
            $('#btnreportsmodal').html('Update');
            $('#ReportName').val($(this).data('reportname')).parent().addClass('is-filled');
            $('#WorkflowModuleUID').val($(this).data('workflowmoduleuid')).parent().addClass('is-filled').trigger('change');
            $('#WorkflowModuleUID').trigger('change');
            $('#ReportUID').val($(this).data('reportuid'));
            $('#reportsmodal').modal('toggle');
        });

        //Report Edit
        $(document).off('click', '#reportsmodalpopup').on('click', '#reportsmodalpopup', function (e) {
            e.preventDefault();
            $('#WorkflowModuleUID').val('empty').parent().addClass('is-filled').trigger('change');
            $('#WorkflowModuleUID').trigger('change');
            $('#reportsmodalLabel').html('Add Report');
            $('#btnreportsmodal').html('Save');
            $('#ReportName').val('').parent().removeClass('is-filled');
            $('#ReportUID').val('');
            $('#reportsmodal').modal('toggle');
        });

        $(".Active").change(function(){
            if($(this).prop("checked") == true){
               var Active = 1;
            }else{
               var Active = 0;
            }
            var ReportUID = $(this).data('reportuid');
            $.ajax({
                type: 'POST',
                dataType: 'JSON',
                global: false,
                url: '<?php echo base_url();?>CustomerChecklistReport/DeleteReportDetails',
                data: {
                    'ReportUID': ReportUID,
                    'Active': Active
                },
                success: function(data) { 
                    $.notify({
                        icon: "icon-bell-check",
                        message: data.message
                    }, {
                        type: data.type,
                        delay: 2000
                    });
                }
            });
        });
    });
</script>