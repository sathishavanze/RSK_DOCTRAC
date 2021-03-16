    <script src="https://d3js.org/d3.v5.min.js" charset="utf-8"></script>
    <script src="c3.js"></script>
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>

    <script>

      $(document).ready(function()
      {

        alert("load");
        demo();

      });

      function demo()
      {

            var jsonData = $.ajax({ 
            url: "<?php echo base_url() . 'Dashboard/chart_data_review' ?>", 
            dataType: "json", 
            async: false 
            }).responseText; 
            
          alert(jsonData);
          console.log(jsonData);

          var data1 = JSON.parse(jsonData);

            var chart = c3.generate({
                data: {
                    columns: [
                        ['inflow', 100, 50, 150],
                        ['complete', 76, 25, 75],
                        ['pending', 25, 25, 75],
                        ['ratio', 75, 50, 50],
                    ],
                    type: 'bar',
                    types: {
                        ratio: 'line',
                    }
                }
            });


          
      }


</script>


</html>