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
   .cf:after {
  visibility: hidden;
  display: block;
  font-size: 0;
  content: " ";
  clear: both;
  height: 0;
}

* html .cf {
  zoom: 1;
}

*:first-child+html .cf {
  zoom: 1;
}

html {
  margin: 0;
  padding: 0;
}

/* body {
  font-size: 100%;
  margin: 0;
  padding: 1.75em;
  font-family: 'Helvetica Neue', Arial, sans-serif;
} */

h1 {
  font-size: 1.75em;
  margin: 0 0 0.6em 0;
}

a {
  color: #2996cc;
}

a:hover {
  text-decoration: none;
}

p {
  line-height: 1.5em;
}

.small {
  color: #666;
  font-size: 0.875em;
}

.large {
  font-size: 1.25em;
}
/**
 * Nestable
 */

.dd {
  position: relative;
  display: block;
  margin: 0;
  padding: 0;
  max-width: 100%;
  list-style: none;
  font-size: 13px;
  line-height: 20px;
}

.dd-list {
  display: block;
  position: relative;
  margin: 0;
  padding: 0;
  list-style: none;
}

.dd-list .dd-list {
  padding-left: 30px;
}

.dd-collapsed .dd-list {
  display: none;
}

.dd-item,
.dd-empty,
.dd-placeholder {
  display: block;
  position: relative;
  margin: 0;
  padding: 0;
  min-height: 20px;
  font-size: 13px;
  line-height: 20px;
}

.dd-handle {
  display: block;
  height: auto;
  margin: 5px 0;
  padding: 5px 10px;
  color: #333;
  text-decoration: none;
  font-weight: bold;
  border: 1px solid #e8e6e6;
  background: #fafafa;
  background: -webkit-linear-gradient(top, #fafafa 0%, #fbfbfb 100%);
  background: -moz-linear-gradient(top, #fafafa 0%, #eee 100%);
  background: linear-gradient(top, #fafafa 0%, #eee 100%);
  -webkit-border-radius: 3px;
  border-radius: 3px;
  box-sizing: border-box;
  -moz-box-sizing: border-box;
}

.dd-handle:hover {
  color: #2ea8e5;
  background: #fff;
}

.dd-item > button {
  display: block;
  position: relative;
  cursor: pointer;
  float: left;
  width: 25px;
  height: 20px;
  margin: 5px 0;
  padding: 0;
  text-indent: 100%;
  white-space: nowrap;
  overflow: hidden;
  border: 0;
  background: transparent;
  font-size: 12px;
  line-height: 1;
  text-align: center;
  font-weight: bold;
}

.dd-item > button:before {
  content: '+';
  display: block;
  position: absolute;
  width: 100%;
  text-align: center;
  text-indent: 0;
}

.dd-item > button[data-action="collapse"]:before {
  content: '-';
}

.dd-placeholder,
.dd-empty {
  margin: 5px 0;
  padding: 0;
  min-height: 30px;
  background: #f2fbff;
  border: 1px dashed #b6bcbf;
  box-sizing: border-box;
  -moz-box-sizing: border-box;
}

.dd-empty {
  border: 1px dashed #bbb;
  min-height: 100px;
  background-color: #e5e5e5;
  background-image: -webkit-linear-gradient(45deg, #fff 25%, transparent 25%, transparent 75%, #fff 75%, #fff), -webkit-linear-gradient(45deg, #fff 25%, transparent 25%, transparent 75%, #fff 75%, #fff);
  background-image: -moz-linear-gradient(45deg, #fff 25%, transparent 25%, transparent 75%, #fff 75%, #fff), -moz-linear-gradient(45deg, #fff 25%, transparent 25%, transparent 75%, #fff 75%, #fff);
  background-image: linear-gradient(45deg, #fff 25%, transparent 25%, transparent 75%, #fff 75%, #fff), linear-gradient(45deg, #fff 25%, transparent 25%, transparent 75%, #fff 75%, #fff);
  background-size: 60px 60px;
  background-position: 0 0, 30px 30px;
}

.dd-dragel {
  position: absolute;
  pointer-events: none;
  z-index: 9999;
}

.dd-dragel > .dd-item .dd-handle {
  margin-top: 0;
}

.dd-dragel .dd-handle {
  -webkit-box-shadow: 2px 4px 6px 0 rgba(0, 0, 0, .1);
  box-shadow: 2px 4px 6px 0 rgba(0, 0, 0, .1);
}
/**
 * Nestable Extras
 */

.nestable-lists {
  display: block;
  clear: both;
  padding: 30px 0;
  width: 100%;
  border: 0;
/*   border-top: 2px solid #ddd;
  border-bottom: 2px solid #ddd; */
}

#nestable-menu {
  padding: 0;
  margin: 20px 0;
}

#nestable-output,
#nestable2-output {
  width: 100%;
  height: 7em;
  font-size: 0.75em;
  line-height: 1.333333em;
  font-family: Consolas, monospace;
  padding: 5px;
  box-sizing: border-box;
  -moz-box-sizing: border-box;
}

#nestable2 .dd-handle {
  color: #fff;
  border: 1px solid #999;
  background: #bbb;
  background: -webkit-linear-gradient(top, #bbb 0%, #999 100%);
  background: -moz-linear-gradient(top, #bbb 0%, #999 100%);
  background: linear-gradient(top, #bbb 0%, #999 100%);
}

#nestable2 .dd-handle:hover {
  background: #bbb;
}

#nestable2 .dd-item > button:before {
  color: #fff;
}

/* @media only screen and (min-width: 700px) {
  .dd {
    float: left;
    width: 48%;
  }
  .dd + .dd {
    margin-left: 2%;
  }
} */

.dd-hover > .dd-handle {
  background: #2ea8e5 !important;
}
/**
 * Nestable Draggable Handles
 */

.dd-dragel > .dd3-item > .dd3-content {
  margin: 0;
}

</style>
<div class="card mt-20 customcardbody" id="Orderentrycard">
   <div class="card-header card-header-danger card-header-icon">
      <div class="card-icon">Checklist Position
      </div>
   </div>
   <div class="card-body">
      <div class="col-md-12">
         <input type="hidden" id="Category" value="<?php echo $this->uri->segment(3); ?>">
         <div class="material-datatables">
         <h5 class="headername" style="text-align: center;font-weight: bold;"><?php echo $this->uri->segment(3); ?> Category List</h5>
          <div class="cf nestable-lists" style="padding-top: 0px;">
            <div class="dd" id="nestable">
              <ol class="dd-list">
              <?php 
                $i = 1;$j=1;
              foreach($DocumentDetails as $key => $value){
                ?>
                <li class="dd-item" data-id="<?php echo $j.'~'.$value->DocumentTypeUID;?>">
                <div class="dd-handle"><?php echo $i.'. '.$value->DocumentTypeName; ?></div>
                <?php 
                      $childChecklist= $this->Documenttype_model->getDocumentPositionChildChecklist($this->uri->segment(3),$value->DocumentTypeUID);
                      if($childChecklist){ $j++;
                        $child=1;  ?>
                        <ol class="dd-list">
                        <?php foreach($childChecklist as $keyChild => $valueChild){ ?>
                                <li class="dd-item" data-id="<?php echo $j.'~'.$valueChild->DocumentTypeUID;?>">
                                  <div class="dd-handle"><?php echo $i.'.'.$child.'. '.$valueChild->DocumentTypeName; ?></div>
                                </li>
                            <?php  
                      $child++; } ?>
                        </ol><?php } ?>
                </li>
                <?php $i++;$j++;
                      }
                      ?> 
              </ol>
            </div>
          </div>

          <textarea id="nestable-output" hidden></textarea>

         </div>
         <div class="ml-auto text-right">
				<!-- <button type="submit" class="btn btn-fill btn-dribbble btn-wd Back" name="Back" id="Back"><i class="icon-arrow-left15 pr-10 Back"></i>Back</button> -->
				<a href="<?php echo base_url('Documenttype'); ?>" class="btn btn-fill btn-back btn-wd ajaxload" role="button" aria-pressed="true"><i class="icon-arrow-left15 pr-10 Back"></i> Back</a>
			</div>
      </div>
   </div>
</div>
<script src="assets/js/jquery-ui.min.js"></script>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="assets/js/jquery.nestable.js"></script>
<script type="text/javascript">

$(document).ready(function()
{
    $('.headername').html("<b><?php echo $value->CategoryName; if($this->uri->segment(4)){echo ' - '.base64_decode($this->uri->segment(4));}?> Category List</b>");
    var updateOutput = function(e)
    {
        var list   = e.length ? e : $(e.target),
            output = list.data('output');
        if (window.JSON) {
            output.val(window.JSON.stringify(list.nestable('serialize')));//, null, 2));
        } else {
            output.val('JSON browser support required for this demo.');
        }
    };

    // activate Nestable for list 1
    $('#nestable').nestable({
        group: 1
    })
    .on('change', updateOutput);

    // output initial serialised data
    updateOutput($('#nestable').data('output', $('#nestable-output')));

    $('#nestable-menu').on('click', function(e)
    {
        var target = $(e.target),
            action = target.data('action');
        if (action === 'expand-all') {
            $('.dd').nestable('expandAll');
        }
        if (action === 'collapse-all') {
            $('.dd').nestable('collapseAll');
        }
    });

    $('#nestable3').nestable();

});

</script>