<table class="table table-hover table-striped goalstable">
      <thead>
        <tr>
         <th><b>Description</b></th>
         <th><b>Count</b></th>
       </tr>  

     </thead>

     <tbody>
      <?php foreach ($ResultData as $key => $value)
       { ?>
       <tr>
         <td><?php echo $key;?></td>
         <td class="bold"><?php echo $value;?></td>
       </tr>
      <?php } ?>
  </tbody>  
</table>
