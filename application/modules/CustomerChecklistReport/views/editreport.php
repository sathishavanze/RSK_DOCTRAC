<link href="<?php echo base_url(); ?>assets/css/progresccircle.css" rel="stylesheet" type="text/css" />
<style>
    .tablist {
        background-color: #efefef;
    }
    .tablist .nav-item .nav-link {
        border-radius: 4px;
    }
    .tablist .nav-item .nav-link:hover {
        background-color: #E94441;   
        border-radius: 4px;
        color: #fff;
    }
    .tablist.nav-pills-warning .nav-item .nav-link.active, .tablist.nav-pills-warning .nav-item .nav-link.active:focus, .tablist.nav-pills-warning .nav-item .nav-link.active:hover {
        background-color: #E94441;
    }
    .reportnameheader h4 {
        text-align: center;
        font-size: 22px;
        margin-bottom: -10px;
        margin-top: 15px;
    }

    /* multiple row add */
    .bootstrap-select .dropdown-menu{
        z-index: 9999 !important;
    }
    #ChecklistReporttable .select2-container.select2-container-disabled .select2-choice {
        background-color: #ffffff;
        border:0;
        border-radius:0;
        padding-left:0;
    }
    #ChecklistReporttable .select2-container.select2-container-disabled .select2-choice .select2-arrow {
        background-color: #ffffff;
    }
    #ChecklistReporttable .select2-container-multi .select2-choices{
        border: 0;
        border-bottom: 1px solid #c1c1c1;
        background-image: none;
    }
    #ChecklistReporttable tbody tr td{
        padding: 8px 5px 8px 5px!important;
    }
    .move-handle-icon{
        cursor:grab;
    }#ChecklistReporttable tbody tr{
        height: 80px;
    }
    .ReportGroupHeader h4 {
        margin-top: 11px;
    }
    /* multiple row add end */
    #ChecklistReporttable .select2-container .select2-choice > .select2-chosen {
        white-space: pre-wrap;
    }
    #ChecklistReporttable .select2-container .select2-choice {
        height: auto;
    }
    #ChecklistReporttable.table>tbody>tr>td, #ChecklistReporttable.table>tbody>tr>th, #ChecklistReporttable.table>tfoot>tr>td, #ChecklistReporttable.table>thead>tr>td {
        vertical-align: top;
    }
    .IsChecklistEnable {
        background-color: #efefef;
    }
    .IsStandardColumnEnable {
        background-color: #efefef;
    }
</style>

<div class="card mt-20 customcardbody">
    <div class="card-header card-header-danger card-header-icon card_theme_color">
        <!-- <div class="card-icon">
            NRZ Report 1
        </div> -->        
    </div>
    <div class="card-body ">
        <div class="col-md-12 reportnameheader">
            <h4><?php echo $ReportsDetails['ReportName']; ?></h4>
        </div>
        <div class="col-md-12 col-xs-12">
            <div class="row">
                <div class="col-md-12 text-left" style="margin-top: 10px;">
                    <a data-toggle="modal" data-target="#addgroupmodal" data-html="true"  id="addgroupmodalpopup" style="color:white; padding:0px 10px !important" class="btn btn-success ajaxload btn-sm mt-10 cardaddinfobtn"><i class="pr-10 icon-plus22
            "></i>Add Group</a>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12" style="margin-top: 12px;">
                    <ul class="nav nav-pills nav-pills-warning tablist" role="tablist">
                        <?php
                        foreach ($GroupsDetails as $key => $value) {
                        ?>
                        <li class="nav-item">
                          <a class="nav-link" data-toggle="tab" href="#link1" role="tablist" data-groupuid="<?php echo $value['GroupUID']; ?>"><?php echo $value['GroupName']; ?></a>
                        </li>
                        <?php 
                        }
                        ?>
                    </ul>
                    <div class="tab-content tab-space">
                        <div class="tab-pane" id="link1">
                            <div class="col-md-12">
                                <div class="well clearfix">
                                    <div class="ReportGroupHeader">
                                        <div class="pull-right">
                                            <span style="text-align: left;width:100%; cursor: pointer;">
                                              <a id="editgroupmodalpopup" title="Group Name Edit">
                                                <i class="fa fa-pencil" aria-hidden="true" style="font-size: 20px; color: #00bcd4;"></i>
                                                <!-- <img src="<?php echo base_url(); ?>assets/img/edit_2_gray.png"> -->
                                              </a>
                                            </span>
                                            <span style="text-align: left;width:100%; padding-left: 5px; cursor: pointer;">
                                              <a id="deletegroup" title="Delete the group and its details">
                                                <i class="fa fa-trash" aria-hidden="true" style="font-size: 20px; color: red;"></i>
                                                <!-- <img src="<?php echo base_url(); ?>assets/img/delete.png"> -->
                                              </a>
                                            </span>
                                        </div>
                                    </div>

                                    <a class="btn btn-default pull-left add-record active" data-added="0"><i class="glyphicon glyphicon-plus"></i> Add Column</a>
                                </div>

                                <div class="material-datatables">
                                    <table class="table display nowrap sort_par_div" id="ChecklistReporttable" cellspacing="0" width="100%" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th style="width: 5%">S.No</th>
                                                <th style="width: 15%">Header Name</th>
                                                <th style="width: 5%">IsChecklist</th>
                                                <th style="width: 15%">Standard Column</th>
                                                <th style="width: 15%">Workflow</th>
                                                <th style="width: 20%">Checklist</th>
                                                <th style="width: 20%">Checklist Option</th>
                                                <th style="width: 15%;" class="text-center">Action</th>
                                                <th>Position</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbl_posts_body">
                                            
                                        </tbody>
                                    </table>
                                </div>   
                                
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<div style="display:none;">
    <table id="sample_table">
        <tr id='rec-1'>
            <td>
                <span class="sn">1</span>
                <input type="hidden" name="ReportFieldUID[]" class="ReportFieldUID">
            </td>
            <td>
                <div class="form-group bmd-form-group">
                    <input type="text" class="form-control" id="HeaderName" name="HeaderName" />
                </div>
            </td>
            <td>
                <div class="form-check">
                    <label class="form-check-label " style="color: teal">
                      <input class="form-check-input" id="IsChecklist" type="checkbox" value="" name="IsChecklist">
                      <span class="form-check-sign">
                        <span class="check"></span>
                      </span>
                    </label>
                </div>
            </td>
            <td>
                <div class="form-group">
                    <select class="select2picker ColumnName IsStandardColumnEnable" name="ColumnName" id="ColumnName" style="width: 100%"> 
                        <option value="">Select Column</option>                 
                        <?php foreach ($StandardColumns as $key => $value) { ?>
                            <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                        <?php } ?>  
                    </select>
                </div>
            </td>
            <td>
                <div class="form-group">
                    <select class="select2picker CustomerWorkflow IsChecklistEnable" name="CustomerWorkflow" id="CustomerWorkflow" style="width: 100%">  
                        <option value="">Select Workflow</option>                 
                        <?php foreach ($CustomerWorkflow as $key => $workflow) { ?>
                            <option value="<?php echo $workflow['WorkflowModuleUID']; ?>"><?php echo $workflow['SystemName'];?></option>
                        <?php } ?>  
                    </select>
                </div>
            </td>
            <td style="width: 35%">
                <div class="form-group">
                    <select class="select2picker CustomerChecklist IsChecklistEnable" name="CustomerChecklist" id="CustomerChecklist" style="width: 90%">     
                        <option value="">Select Checklist</option>              
                        <?php foreach ($Checklist as $key => $value) { ?>
                            <option value="<?php echo $value['DocumentTypeUID']; ?>"><?php echo $value['DocumentTypeName'];?></option>
                        <?php } ?>  
                    </select>
                    <a class="checklistclipboard IsChecklistEnable" data-id="" title="Copy the checklist name to the header name" style="cursor: pointer;">
                        <i class="fa fa-clipboard" aria-hidden="true" style="font-size: 20px; color: #9daecc;"></i>
                    </a>
                </div>
            </td>
            <td>
                <div class="form-group">
                    <select class="select2picker ChecklistOption IsChecklistEnable" name="ChecklistOption" id="ChecklistOption" style="width: 100%">  
                        <option value="">Select Column</option>
                        <option value="Answer">Findings</option>
                        <?php foreach ($ChecklistFields as $ChecklistField) { ?>
                        	<option value="<?php echo $ChecklistField->FieldName; ?>"><?php echo $ChecklistField->FieldLabel; ?></option>
                        <?php } ?>
                        <!-- <option data-WorkflowModuleUID="<?php echo $ChecklistField->WorkflowModuleUID; ?>" value="<?php echo $ChecklistField->FieldName; ?>"><?php echo $ChecklistField->FieldLabel; ?></option> -->
                        <option value="Comments">Comments</option>
                    </select>
                </div>
            </td>
            <td>
                <a class="save-record" data-id="" title="Save Checklist" style="cursor: pointer;">
                    <i class="fa fa-save pull-left" aria-hidden="true" style="font-size: 20px; color: green;"></i>
                </a>
                <a class="delete-record" data-id="" title="Delete Checklist" style="cursor: pointer;">
                    <i class="fa fa-trash" aria-hidden="true" style="font-size: 20px; color: red;"></i>
                </a>
            </td>
            <td class="text-center" style="width: 5%">
                <span title="Move" class="icon_action move-handle-icon">
                    <i class="fa fa-arrows" aria-hidden="true" style="color: #2196f3; font-size: 20px;"></i>
                </span>
            </td>
        </tr>
    </table>
</div>

<div class="ml-auto text-right">
    <a href="<?php echo base_url(); ?>CustomerChecklistReport/" class="btn btn-fill btn-danger btn-wd btn-back" name="UpdateCustomer"><i class="icon-arrow-left8 pr-10"></i> Back</a>
</div>

<div class="modal fade" id="addgroupmodal" tabindex="-1" role="dialog" aria-labelledby="addgroupmodalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form class="form-horizontal" id="frmaddgroupmodal" action="#" method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="addgroupmodalLabel">Add Group</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">

                        <div class="row col-md-12">

                            <div class="col-sm-12">
                                <div class="form-group bmd-form-group">
                                    <label for="GroupName" class="bmd-label-floating">Group Name <span class="mandatory"></span></label>
                                    <input type="text" class="form-control" id="GroupName" name="GroupName" />
                                    <input type="hidden" name="groupaction" id="groupaction" value="">
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-success btnaddgroupmodal" name="submit" type="submit" id="btnaddgroupmodal" value="addgroupmodal">Save</button>                    
                </div>
            </form> 
        </div>
    </div>
</div>
<script src="assets/js/jquery-ui.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#Checkllisttable').dataTable({
            responsive: true,
            // dom: 'Bfrtip',
            // buttons: [
            //     'excel', 'pdf'
            // ],
        });

        //append tab and content
        $(document).off('click','.btnaddgroupmodal').on('click','.btnaddgroupmodal', function(e){
            var GroupName = $('#GroupName').val();
            var groupaction = $('#groupaction').val();
            if (groupaction == 'edit') {
                var GroupUID = $('.nav-link.active.show').data('groupuid');
                if (typeof GroupUID !== 'undefined') {
                    $.ajax({
                        type: 'POST',
                        dataType: 'JSON',
                        global: false,
                        url: '<?php echo base_url();?>CustomerChecklistReport/UpdateGroupDetails',
                        data: {
                            'ReportUID': <?php echo $this->uri->segment(3); ?>,
                            'GroupUID': GroupUID,
                            'GroupName': GroupName
                        },
                        success: function(data) {
                            console.log(data);
                            if (data.validation_error == 0) {
                                $('#addgroupmodal').modal('toggle');
                                $('.nav-link.active.show').text(GroupName);
                            }   
                            $.notify({
                                icon: "icon-bell-check",
                                message: data.message
                            }, {
                                type: data.type,
                                delay: 1000
                            });
                        }
                    });
                } else {
                    $('#addgroupmodal').modal('toggle');
                    $('.nav-link.active.show').text(GroupName);
                }
            } else {
                if (GroupName) {
                    $('.nav-link').removeClass('active show');
                    $('#addgroupmodal').modal('toggle');
                    $('.tablist').append('<li class="nav-item"><a class="nav-link active show" data-toggle="tab" href="#link1" role="tablist">'+GroupName+'</a></li>');
                    // $('#link1').addClass('active show');
                    $("select.select2picker").select2({
                        theme: "bootstrap",
                    });
                    defaultgroupmenuloader(GroupUID = 'newgroupadded');
                } else {
                    $('#GroupName').parent().addClass('is-filled is-focused');
                    $.notify({
                        icon: "icon-bell-check",
                        message: 'Please Enter the Group Name!.'
                    }, {
                        type: 'danger',
                        delay: 1000
                    });
                }
            }

        });        

        $(document).off('click', 'a.add-record').on('click', 'a.add-record', function(e) {
            e.preventDefault();
            $("select.select2picker").select2('destroy');
            var content = $('#sample_table tr'),
                size = $('#ChecklistReporttable >tbody >tr').length + 1,
                element = null,
                element = content.clone();
            element.attr('id', 'rec-' + size);
            element.find('.delete-record').attr('data-id', size);
            element.appendTo('#tbl_posts_body');
            element.find('.sn').html(size);
            $("select.select2picker").select2({
                theme: "bootstrap",
            });
        });

        $(document).off('click', 'a.delete-record').on('click', 'a.delete-record', function(e) {
            e.preventDefault();
            var id = $(this).attr('data-id');
            $('#rec-' + id).remove();

            //regnerate index number on table
            $('#tbl_posts_body tr').each(function(index) {
                $(this).find('span.sn').html(index + 1);
            });  

            var ReportFieldUID = $(this).closest('tr').find('.ReportFieldUID').val();
            if (ReportFieldUID) {
                $.ajax({
                    type: 'POST',
                    dataType: 'JSON',
                    global: false,
                    url: '<?php echo base_url();?>CustomerChecklistReport/deletereportfields',
                    data: {
                        'ReportFieldUID': ReportFieldUID
                    },
                    success: function(data) {
                        console.log(data);
                        $.notify({
                            icon: "icon-bell-check",
                            message: data.message
                        }, {
                            type: data.type,
                            delay: 1000
                        });
                        updatereportfieldpostion(0);
                    }
                });
            }
        });

        $(document).off('click', '.save-record').on('click', '.save-record', function(e) {
            e.preventDefault();
            var $this = $(this);
            var GroupUID = $('.nav-link.active.show').data('groupuid');
            var ReportFieldUID = $(this).closest('tr').find('.ReportFieldUID').val();
            var GroupName = $('.nav-link.active.show').text();
            var HeaderName = $(this).closest('tr').children().find('#HeaderName').val();

            var $IsChecklist = $(this).closest('tr').children().find('#IsChecklist');
            if($IsChecklist.prop("checked") == true){
                IsChecklist = 1;
            }
            else if($IsChecklist.prop("checked") == false){
                IsChecklist = 0;
            }
            var ColumnName = $(this).closest('tr').children().find('.ColumnName :selected').val();

            var CustomerWorkflow = $(this).closest('tr').children().find('.CustomerWorkflow :selected').val();
            var CustomerChecklist = $(this).closest('tr').children().find('.CustomerChecklist :selected').val();
            var oldworkflowuid = $(this).data('oldworkflowuid');
            var olddocumenttypeuid = $(this).data('olddocumenttypeuid');
            var ChecklistOption = $(this).closest('tr').children().find('.ChecklistOption :selected').val();
            $.ajax({
                type: 'POST',
                dataType: 'JSON',
                global: false,
                url: '<?php echo base_url();?>CustomerChecklistReport/insertreportfields',
                data: {
                    'ReportUID': <?php echo $this->uri->segment(3); ?>,
                    'GroupUID': GroupUID,
                    'ReportFieldUID': ReportFieldUID,
                    'GroupName': GroupName,
                    'HeaderName': HeaderName,
                    'IsChecklist': IsChecklist,
                    'ColumnName': ColumnName,
                    'WorkflowUID': CustomerWorkflow,
                    'OldWorkflowUID': oldworkflowuid,
                    'DocumentTypeUID': CustomerChecklist,
                    'OldDocumentTypeUID': olddocumenttypeuid,
                    'ChecklistOption': ChecklistOption,
                },
                success: (json) => {
                    console.log(json);
                    if (json.GroupUID) {
                        $('.nav-link.active.show').attr('data-groupuid', json.GroupUID);
                    }
                    if (json.ReportFieldUID) {
                        $(this).closest('tr').children().find('.ReportFieldUID').val(json.ReportFieldUID);
                        updatereportfieldpostion(0);
                    }
                    if ((json.WorkflowUID) && (json.DocumentTypeUID)) {
                        $(this).removeData("oldworkflowuid");
                        $(this).attr('data-oldworkflowuid', json.WorkflowUID);
                        $(this).removeData("olddocumenttypeuid");
                        $(this).attr('data-olddocumenttypeuid', json.DocumentTypeUID);
                    }
                    $.notify({
                        icon: "icon-bell-check",
                        message: json.message
                    }, {
                        type: json.type,
                        delay: 1000
                    });                  
                    $.each(json, function(k, v) {
                        console.log(k);
                        $this.closest('tr').children().find('#'+k).addClass("is-invalid").closest('.form-group').removeClass('has-success').addClass('has-danger');
                        $this.closest('tr').children().find('#'+ k +'.select2picker').next().find('span.select2-selection').addClass('errordisplay');

                    });
                }
            });
        });

        defaultgroupmenuloader(GroupUID = 'fisttimepageload');
        function defaultgroupmenuloader(GroupUID) {
            tablistlength = $('.tablist li').length;
            $('#ChecklistReporttable >tbody').html('');
            if (tablistlength) {
                if (GroupUID == 'fisttimepageload') {
                    $('.tablist li:first-child').children('a').addClass('active show');
                    $('#link1').addClass('active show');
                    var GroupUID = $('.tablist li:first-child').children('a').data('groupuid');
                } else if(GroupUID == 'newgroupadded') {
                    $('#link1').addClass('active show');
                    return false;
                }
                $.ajax({
                    type: 'POST',
                    dataType: 'JSON',
                    global: false,
                    url: '<?php echo base_url();?>CustomerChecklistReport/GetReportGroupDetails',
                    data: {
                        'ReportUID': <?php echo $this->uri->segment(3); ?>,
                        'GroupUID': GroupUID
                    },
                    success: function(response) {
                        console.log(response);
                        $.each(response, function(key,value) {
                            $("select.select2picker").select2('destroy');
                            var content = $('#sample_table tr'),
                                size = $('#ChecklistReporttable >tbody >tr').length + 1,
                                element = null,
                                element = content.clone();
                            element.attr('id', 'rec-' + size);
                            element.find('.delete-record').attr('data-id', size);
                            //update group details
                            element.find('#HeaderName').val(value.HeaderName);

                            // element.find('.ChecklistOption option[data-WorkflowModuleUID]').hide();
                            // element.find('.ChecklistOption').find("option[data-WorkflowModuleUID='" + value.WorkflowUID +"']").show();

                            if (value.IsChecklist == 0) {
                                element.find('.IsChecklistEnable').attr("disabled", true);
                                element.find('.IsStandardColumnEnable').attr("disabled", false);
                                element.find('.CustomerWorkflow').val(value.WorkflowUID);
                                element.find('#IsChecklist').prop("checked", false);
                                element.find('.ColumnName').val(value.ColumnName);
                                element.find('.ColumnName').trigger('change.select2');
                            } else {
                                element.find('.IsChecklistEnable').attr("disabled", false);
                                element.find('.IsStandardColumnEnable').attr("disabled", true);
                                element.find('#IsChecklist').prop("checked", true);
                                element.find('.CustomerWorkflow').val(value.WorkflowUID);
                                element.find('.CustomerWorkflow').trigger('change.select2');
                                element.find('.CustomerChecklist').val(value.DocumentTypeUID);
                                element.find('.CustomerChecklist').trigger('change.select2');
                                element.find('.ChecklistOption').val(value.ChecklistOption);
                                element.find('.ChecklistOption').trigger('change.select2');
                                element.find('.save-record').attr('data-oldworkflowuid', value.WorkflowUID);
                                element.find('.save-record').attr('data-olddocumenttypeuid', value.DocumentTypeUID);
                                element.find('.delete-record').attr('data-oldworkflowuid', value.WorkflowUID);
                                element.find('.delete-record').attr('data-olddocumenttypeuid', value.DocumentTypeUID);
                            }
                            element.find('.ReportFieldUID').val(value.ReportFieldUID);
                            
                            //update group details end
                            element.appendTo('#tbl_posts_body');
                            element.find('.sn').html(size);
                            $("select.select2picker").select2({
                                theme: "bootstrap",
                            });
                        });
                    }
                });
            } else {
                $('#link1').removeClass('active show');
            }
        }

        $(document).off('click', '.nav-link').on('click', '.nav-link', function(e) {
            e.preventDefault();
            var GroupUID = $(this).data('groupuid');
            defaultgroupmenuloader(GroupUID);
        });

        $(document).off('click', '.checklistclipboard').on('click', '.checklistclipboard', function(e) {
            e.preventDefault();
            var data = $(this).closest('tr').children().find('.CustomerChecklist :selected').select2('data');
            if (data[0].value) {
                $(this).closest('tr').children().find('#HeaderName').val(data[0].text);
            } else {
                $.notify({
                    icon: "icon-bell-check",
                    message: 'Please select the checklist!.'
                }, {
                    type: 'danger',
                    delay: 2000
                });
            }
        });

        $(document).off('click', '#editgroupmodalpopup').on('click', '#editgroupmodalpopup', function(e) {
            e.preventDefault();
            var GroupUID = $('.nav-link.active.show').data('groupuid');
            var mReportsGroups = $('.nav-link.active.show').text();
            $('#addgroupmodalLabel').html('Edit Group');
            $('#GroupName').val(mReportsGroups).parent().addClass('is-filled');
            $('#groupaction').val('edit');
            $('#btnaddgroupmodal').html('Update');
            $('#addgroupmodal').modal('toggle');
        });

        $(document).off('click', '#addgroupmodalpopup').on('click', '#addgroupmodalpopup', function(e) {
            e.preventDefault();
            $('#addgroupmodalLabel').html('Add Group');
            $('#GroupName').val('').parent().removeClass('is-filled');
            $('#groupaction').val('new');
            $('#btnaddgroupmodal').html('Save');
            $('#GroupName').parent().removeClass('is-filled is-focused');
        });

        $(document).off('click', '#deletegroup').on('click', '#deletegroup', function(e) {
            e.preventDefault();
            var GroupUID = $('.nav-link.active.show').data('groupuid');
            if (!GroupUID) {
                // deleting menu
                var $delete_menuelement = $('.nav-link.active.show').parent('.nav-item');
                $delete_menuelement.prev().children('a').addClass('active show');
                $delete_menuelement.remove();
                //load group details
                var GroupUID = $('.nav-link.active.show').data('groupuid');
                defaultgroupmenuloader(GroupUID);
                return false;
            }
            var r=confirm("Do you want to delete this group and its details?")
            if (r==true) {
                $.ajax({
                    type: 'POST',
                    dataType: 'JSON',
                    global: false,
                    url: '<?php echo base_url();?>CustomerChecklistReport/DeleteGroupDetails',
                    data: {
                        'ReportUID': <?php echo $this->uri->segment(3); ?>,
                        'GroupUID': GroupUID
                    },
                    success: function(data) { 
                        $.notify({
                            icon: "icon-bell-check",
                            message: data.message
                        }, {
                            type: data.type,
                            delay: 2000
                        });
                        window.setTimeout(function(){location.reload()},3000)
                    }
                });
            }
            else {
              return false;            
            } 
            
        });

        //Default Checklist is disabled
        $('#sample_table').children().find('.IsChecklistEnable').attr("disabled", true);

        //IsChecklist Checked based on changes
        $(document).off('click', '#IsChecklist').on('click', '#IsChecklist', function (e) {
            var CustomerUID = $('#CustomerUID').val();
            var workflowUID = $(this).closest("tr").attr('id');
            var IsChecklist;
            if($(this).prop("checked") == true){
                IsChecklist = 1;
                $(this).closest('tr').children().find('.IsChecklistEnable').attr("disabled", false);
                $(this).closest('tr').children().find('.IsStandardColumnEnable').attr("disabled", true);
                //set default value
                $(this).closest('tr').children().find('.ColumnName').val('').trigger('change');
            }
            else if($(this).prop("checked") == false){
                IsChecklist = 0;
                $(this).closest('tr').children().find('.IsChecklistEnable').attr("disabled", true);
                $(this).closest('tr').children().find('.IsStandardColumnEnable').attr("disabled", false);
                //set default value
                $(this).closest('tr').children().find('.CustomerWorkflow').val('').trigger('change');
                $(this).closest('tr').children().find('.CustomerChecklist').val('').trigger('change');
                $(this).closest('tr').children().find('.ChecklistOption').val('').trigger('change');
            }

        });

        var fixHelperModified = function(e, tr) {
          var $originals = tr.children();
          var $helper = tr.clone();
          $helper.children().each(function(index) {
            $(this).width($originals.eq(index).width())
          });
          console.log($helper);
          return $helper;
        },
        updateIndex = function(e, ui) {
          $('td span.sn', ui.item.parent()).each(function (i) {
            $(this).html(i+1);
          });
        };

        $(".sort_par_div tbody").sortable({
            axis: "y",
            cursor: "grabbing",
            handle: ".move-handle-icon",
            opacity: 1,
            helper: fixHelperModified,
            stop: updateIndex,
            update: function (event, ui) {
              updatereportfieldpostion(1);
            }
        });

        function updatereportfieldpostion(action) {
            var data = $('.sort_par_div .ReportFieldUID').serialize();
            console.log();

            $.ajax({
                type: 'POST',
                dataType: 'JSON',
                global: false,
                url: '<?php echo base_url();?>CustomerChecklistReport/ReportFieldsPosition',
                data: data,
                success: function(data) {
                  console.log(data);  
                  if (action == 1) { // Show notification action is 1
                    show_popup(data); // Pass data to a function
                  }                                
                }
            });
        }

        function show_popup(data)
        {
            $.notify({
              icon: "icon-bell-check",
              message: data.msg
            }, {
              type: data.type,
              delay: 1000
            });
        }

    });
</script>