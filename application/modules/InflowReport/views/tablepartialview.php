<style type="text/css">
  .textbold
  {
    font-weight: bold;
    color: #000;
  }
  .inflowtable > tbody > tr:first-child > td > a ,.inflowtable > tbody > tr:first-child
  {
   font-weight : bold;
   color : #000 !important;
  }
</style>
<table class="table table-hover table-striped inflowtable">
      <thead>
        <tr>
         <th><b>Inflows</b></th>
         <th><b>FHA</b></th>
         <th><b>VA</b></th>
         <th><b>Total</b></th>

         <th><b>FHA</b></th>
         <th><b>VA</b></th>
         <th><b>Total</b></th>
       </tr>	

     </thead>

     <tbody>
       <?php foreach ($InflowReportCounts as $InflowReport) 
       { 
        foreach ($InflowReport as $row) 
         {
           if($row->{'total'} != 0)
           {
            $fhaPercent = round($row->{'FHA'} * 100 / $row->{'total'});
             $vaPercent = round($row->{'VA'} * 100 / $row->{'total'});
             $totalPercent = $fhaPercent+$vaPercent;
           }
           else
           {
            $fhaPercent = 0;
            $vaPercent = 0;
            $totalPercent = 0;
          }

          ?>
      <tr>
          <td><b><?php echo $row->date; ?></b></td>

          <td><a href="javascript:void(0);" class="text-primary listorders" data-orderid="<?php echo $row->{'FHAOrderUID'} ?>" data-title="Process Inflow List" ><?php echo $row->{'FHA'} ?></a></td>
          <td><a href="javascript:void(0);" class="text-primary listorders" data-orderid="<?php echo $row->{'VAOrderUID'} ?>" data-title="Process Inflow List" ><?php echo $row->{'VA'} ?></a></td>
          <td ><a href="javascript:void(0);" class="text-primary listorders" data-orderid="<?php echo $row->{'totalOrderUID'} ?>" data-title="Process Inflow List" ><span class="textbold"><?php echo $row->{'total'} ?></span></a></td>

          <td><?php echo $fhaPercent.'%'; ?></td>
          <td><?php echo $vaPercent.'%'; ?></td>
          <td><span class="textbold"><?php echo $totalPercent.'%'; ?></span></td>

    </tr>        
      <?php }}?>
  </tbody>  
</table>