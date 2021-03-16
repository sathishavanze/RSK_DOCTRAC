	$(document).ready(function(){

		callselect2();


		//click event for .missingfiltertype
		$(document).off('change', '.missingfiltertype').on('change', '.missingfiltertype', function(e){
			e.preventDefault();
			var mDateType = $(this).attr('data-datetype');

			var cardid = '#card_Missing_Date';
			addcardspinner(cardid);

			getMissingDocs(e.target.value, mDateType, cardid);

		});

		//click event for .missingfiltertype
		$(document).off('change', '.receivedfiltertype').on('change', '.receivedfiltertype', function(e){
			e.preventDefault();
			var mDateType = $(this).attr('data-datetype');

			var cardid = '#card_Received_Date';
			addcardspinner(cardid);

			getReceivedDocs(e.target.value, mDateType, cardid);

		});

		$(document).off('change','.ShippingDocsFilter').on('change','.ShippingDocsFilter',function(e) {
			var FilterValue =$('.ShippingDocsFilter').val();
		// var datetype ='Docs_Shipped_Ageing_to_Pool_Due_Date';
		var datetype = $(this).attr('data-datetype');
		$.ajax({
			type: "POST",
			url: "AnalyticsChart/GetShippingDocs",
			data: {FilterValue:FilterValue},
			dataType:'json',
			success: function(response) {
				console.log(response);
				
				//var ctx = document.getElementById(datetype + '_chart').getContext('2d');
				var ctx =  datetype + '_chart';var ctx1= datetype + '_chart1';

				if (response.status == 1) {
					fillChart(ctx, response.dataset, response.labels, FilterValue, datetype);
					$('.'+datetype+'_docsgrid').html(response.html);
					fillChart(ctx1, response.dataset, response.labels, FilterValue, datetype);
					//$('.'+datetype+'_docsgrid').html(response.html);

				}

				// resolve('succeded');

			}
		});
	});

		$('.missingfiltertype').trigger('change');
		$('.receivedfiltertype').trigger('change');
		$('.ShippingDocsFilter').trigger('change');


		getReceivedDocsTrending(1, 1, 'received_docs', '#card_Received_Date');
		getMonthlyFundings(1, 'MonthlyFundings_by_Channel', '#card_MonthlyFundings_by_Channel');
		// /*Chart jS*/
		// // Any of the following formats may be used
		// var ctx = document.getElementById('myChart');
		// var myChart = new Chart(ctx, {
		// 	type: 'bar',
		// 	data: {
		// 		labels: ['0-30', '31-60', '61-90', '91-120', '151-180', '>181 days'],
		// 		datasets: [{
		// 			label: "% of Votes",
		// 			data: [12, 19, 3, 5, 2, 3],
		// 			fillColor: 'rgba(68, 114,196)',
		// 			backgroundColor: [
		// 			'rgba(68, 114,196)',
		// 			'rgba(68, 114,196)',
		// 			'rgba(68, 114,196)',
		// 			'rgba(68, 114,196)',
		// 			'rgba(68, 114,196)',
		// 			'rgba(68, 114,196)',
		// 			'rgba(68, 114,196)',
		// 			],
		// 			// borderColor: [
		// 			// 'rgba(255, 99, 132, 1)',
		// 			// 'rgba(54, 162, 235, 1)',
		// 			// 'rgba(255, 206, 86, 1)',
		// 			// 'rgba(75, 192, 192, 1)',
		// 			// 'rgba(153, 102, 255, 1)',
		// 			// 'rgba(255, 159, 64, 1)'
		// 			// ],
		// 			borderWidth: 1
		// 		}]
		// 	},
		// 	options: {
		// 		scales: {
		// 			yAxes: [{
		// 				ticks: {
		// 					beginAtZero: true
		// 				},
		// 			}],
		// 			xAxes: [{
		// 				barPercentage: 1.0,
		// 				categoryPercentage: 0.5,
		// 			}]
		// 		}
		// 	}
		// });
		// // Chart js init completed



	});

	function getMissingDocs(missingtype, datetype, cardid) {

		return new Promise(function (resolve, reject) {


			$.ajax({
				url: 'AnalyticsChart/getMissingDocs',
				type: 'GET',
				dataType: 'json',
				data: {"missingtype": missingtype, "datetype": datetype, "doctype": "Missing"},
				beforeSend: function(){
				},
			})
			.done(function(response) {
				//console.log("success", response);

				//var ctx = document.getElementById(datetype + '_chart').getContext('2d');
				var ctx = datetype + '_chart'; 

				if (response.status == 1) {
					fillChart(ctx, response.dataset, response.labels, missingtype, datetype);
					fillChart(ctx+'1', response.dataset, response.labels, missingtype, datetype);
					$('.'+datetype+'_docsgrid').html(response.html);
				}

				resolve('succeded');
			})
			.fail(function(jqXHR) {
				//console.log("error", jqXHR);
				reject('failed');
			})
			.always(function() {
				removecardspinner('#MissingDocs');
				//console.log("complete");
			});

		})
	}

	function getReceivedDocs(missingtype, datetype, cardid) {

		return new Promise(function (resolve, reject) {


			$.ajax({
				url: 'AnalyticsChart/getMissingDocs',
				type: 'GET',
				dataType: 'json',
				data: {"missingtype": missingtype, "datetype": datetype, "doctype": "Received"},
				beforeSend: function(){
				},
			})
			.done(function(response) {
				//console.log("success", response);

				//var ctx = document.getElementById(datetype + '_chart').getContext('2d');
				var ctx = datetype + '_chart';

				if (response.status == 1) {
					fillChart(ctx, response.dataset, response.labels, missingtype, datetype);
					fillChart(ctx+'1', response.dataset, response.labels, missingtype, datetype);
					$('.'+datetype+'_docsgrid').html(response.html);
				}

				resolve('succeded');
			})
			.fail(function(jqXHR) {
				//console.log("error", jqXHR);
				reject('failed');
			})
			.always(function() {
				removecardspinner('#MissingDocs');
				//console.log("complete");
			});

		})
	}

	function getReceivedDocsTrending(loans_paid, missingtype, datetype, cardid) {

		return new Promise(function (resolve, reject) {


			$.ajax({
				url: 'AnalyticsChart/getTrendingDocs',
				type: 'GET',
				dataType: 'json',
				data: {"loans_paid": loans_paid, "datetype": datetype, "doctype": "Received"},
				beforeSend: function(){
				},
			})
			.done(function(response) {
				//console.log("success", response);

				//var ctx = document.getElementById(datetype + '_chart').getContext('2d');
				var ctx = datetype + '_chart';
				if (response.status == 1) {
					fillChart(ctx, response.dataset, response.labels, missingtype, datetype);
					$('.'+datetype+'_docsgrid').html(response.html);
				}

				resolve('succeded');
			})
			.fail(function(jqXHR) {
				//console.log("error", jqXHR);
				reject('failed');
			})
			.always(function() {
				removecardspinner('#MissingDocs');
				//console.log("complete");
			});

		})
	}

	function getMonthlyFundings(missingtype, datetype, cardid) {

		return new Promise(function (resolve, reject) {


			$.ajax({
				url: 'AnalyticsChart/getMonthlyFundings',
				type: 'GET',
				dataType: 'json',
				data: {"datetype": datetype, "doctype": "Received"},
				beforeSend: function(){
				},
			})
			.done(function(response) {
				//console.log("success", response);

				//var ctx = document.getElementById(datetype + '_chart').getContext('2d');
				var ctx =datetype + '_chart';

				if (response.status == 1) {
					fillChart(ctx, response.dataset, response.labels, missingtype, datetype);
					$('.'+datetype+'_docsgrid').html(response.html);
				}

				resolve('succeded');
			})
			.fail(function(jqXHR) {
				//console.log("error", jqXHR);
				reject('failed');
			})
			.always(function() {
				removecardspinner('#MissingDocs');
				//console.log("complete");
			});

		})
	}

	function fillChart(ctx, dataset, labeldata, missingtype, datetype) {
		dataarr = [];
		columnwidth  = 0.30;
		$.each(labeldata, function(m,n) {
			dataobj = {};
				if(n=='No Purchase Date')
			{
				n='NPD';
			}
			dataobj["category"] = n;
			dataarr.push(dataobj);
		});
		graph  = [];
		$.each(dataset , function(a,b){	
			var datasvalue  = b.data;
			if(b.label){
				graphobj = {};
				graphobj["balloonText"] = "[[category]]:[[value]]";
				graphobj["fillAlphas"] = 1;
				graphid  = "AmGraph-" + (a + 1);
				graphobj["id"] = graphid;
				graphobj["title"] = b.label;
				graphobj["type"] = "column";
				labelvalue = "column-" + parseInt(a + 1);
				graphobj["valueField"] = labelvalue;
				graphobj["labelText"]="[[value]]";
				graphobj["balloonFunction"] = balloonFunction;
				graphobj['fontSize']="10";
				graphobj['labelRotation']="90";
				graph.push(graphobj);
			}
			var valueaxis = "column-" + parseInt(a + 1);		
			$.each(dataarr, function(c,d) {	
				if(datasvalue[c] > 0){
					d[valueaxis] = datasvalue[c];				
				}else{
					// d[valueaxis] = "0";	
				}
			});
		});

		if(graph.length == 0){
			graphobj = {};
			graphobj["balloonText"] = "[[category]]:[[value]]";
			graphobj["fillAlphas"] = 1;
			graphobj["id"] = "AmGraph-1";
			graphobj["title"] ="column 1";
			graphobj["type"] = "column";
			graphobj["valueField"] ="column-1";
			graphobj["labelText"]="[[value]]";
			graphobj["balloonFunction"] = balloonFunction;
			graphobj['fontSize']="10";
			graphobj['labelRotation']="90";
			graph.push(graphobj);
			legendtxt = false;
		}else{
			columnwidth  = 0.80;
			legendtxt = true;
		}

	
	var chart=	AmCharts.makeChart(ctx,
		{
			"type": "serial",
			"categoryField": "category",
			"marginBottom": 5,
			"marginLeft": 5,
			"marginRight": 5,
			"marginTop": 5,
			"columnWidth": columnwidth,
			"colors": [
			"#3f51b5",
			"#95A4A9",
			"#4D2A52",
			"#0D8ECF",
			"#9CBC59",
			"#BF3A2B",
			"#EE9A14",
			"#00CC00",
			"#0000CC",
			"#DDDDDD",
			"#999999",
			"#333333",
			"#990000"
			],
			"startDuration": 1,
			"categoryAxis": {
				"gridPosition": "start",
				"gridThickness": 0,
				"labelRotation" :30
			},
			"trendLines": [],
			"graphs":graph,
			"guides": [],

			"valueAxes": [
			{
				"id": "ValueAxis-1",
				"gridThickness":0,
				"title": ""
			},
			{
				"id": "ValueAxis-2",
				"position": "right",
				"gridAlpha": 0,
				"title": ""
			},

			],
			"allLabels": [],
			"balloon": {},
			"legend": {
				"enabled": legendtxt,
				"useGraphSettings": true,
				"spacing": 2
			},
			"titles": [
			{
				"id": "Title-1",
				"size": 15,
				"text": ""
			}
			],
			"chartCursor": {
		        "valueLineEnabled": false,
		        "valueLineBalloonEnabled": false,
		        "valueLineAlpha": 0.5,
		        "fullWidth": true,
		        "cursorAlpha": 0.05
		    },
			"dataProvider":dataarr
		}
		);
	}
		function balloonFunction(item, graph){
    		if(item.category=='NPD')
    		{
			  item.category='No Purchase Date';
    		}

    		return item.category+' : '+item.values.value;
		}
