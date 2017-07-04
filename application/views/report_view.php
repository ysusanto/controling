<script>
    $(document).ready(function () {

        viewreport();
        barchartproject();
    })
    function viewreport() {

        var table = $('#tabelproject').dataTable({
            "sPaginationType": "full_numbers",
//            "bJQueryUI": true,
            "iDisplayLength": 30,
            "bDestroy": true,
            "bFilter": true,
            "bLengthChange": false,
            "aaSorting": [],
            "bAutoWidth": true,
            "bSortable": false,
            "bSortClasses": true,
            "sAjaxSource": '<?php echo base_url(); ?>report/getprojectreport',
			dom: 'Bfrtip',
            buttons: [
                {
                    text: 'Export Excel',
                    action: function (e, dt, node, config) {
						exportexcel()
                    }
                }
            ]

        });
    }
    function detailreport(projectid,platformid){
      location.replace("<?php echo base_url(); ?>home/viewreportdetail/"+projectid+"/"+platformid);
    }
	function exportexcel(){
      
      window.open('<?php echo base_url();?>report/exportexcelplanactual/','_blank');
    }
</script>


    <div class="row">
        <div class="col-lg-12" id="divdataproject">

            <div id="divproject">

                <table id="tabelproject" class="table table-striped table-bordered" cellspacing="0" >
                    <thead align='center'>
					
                    <tr>
                    <th rowspan="2">No.</th>
                    <th rowspan="2">Project Name</th>
                    <!--<th>Platform</th>-->

                    <th rowspan="2">Client</th>
                     <th rowspan="2">Project Manager</th>
                     <th rowspan="2">Platform</th>
					  <th colspan="2">Mainhours</th>
					   <th colspan="4">Costs</th>
					</tr>
					<tr>
                    <th>Planing</th>
                    <th>Actual</th>
					<th>Planing</th>
                    <th>Actual</th>
					<th>Operational Cost</th>
                    <th>Profit</th>
</tr>

                    <!--<th>Project Manager</th>-->

                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12" id="divchartproject" style="padding-right:40px;margin-left:20px;">
          <div class="chart">
                    <canvas id="barChart" style="height:250px"></canvas>
            </div>
        </div>
    </div>

<script>
function barchartproject(){
  $.ajax({
      type: 'post',
      url: '<?php echo base_url(); ?>report/getdatachartproject/',
//            data: 'item_id=' + id + '&hour=' + hour,
      success: function (msg) {
          var data = JSON.parse(msg);
          var label= data.label;
          var plan=data.plan;
          var actual=data.actual;
          barchart(label,plan,actual);
          //                $('#addModal').modal('show');
      }
  });
}

function barchart(label,data1,data2){
var areaChartData = {
          labels: label,
          datasets: [
            {
              label: "Plan",
              fillColor: "rgba(210, 214, 222, 1)",
              strokeColor: "rgba(210, 214, 222, 1)",
              pointColor: "rgba(210, 214, 222, 1)",
              pointStrokeColor: "#c1c7d1",
              pointHighlightFill: "#fff",
              pointHighlightStroke: "rgba(220,220,220,1)",
              data: data1
            },
            {
              label: "Actual",
              fillColor: "rgba(60,141,188,0.9)",
              strokeColor: "rgba(60,141,188,0.8)",
              pointColor: "#3b8bba",
              pointStrokeColor: "rgba(60,141,188,1)",
              pointHighlightFill: "#fff",
              pointHighlightStroke: "rgba(60,141,188,1)",
              data: data2
            }
          ]
        };


var barChartCanvas = $("#barChart").get(0).getContext("2d");
        var barChart = new Chart(barChartCanvas);
        var barChartData = areaChartData;
        barChartData.datasets[1].fillColor = "#00a65a";
        barChartData.datasets[1].strokeColor = "#00a65a";
        barChartData.datasets[1].pointColor = "#00a65a";
        var barChartOptions = {
          //Boolean - Whether the scale should start at zero, or an order of magnitude down from the lowest value
          scaleBeginAtZero: true,
          //Boolean - Whether grid lines are shown across the chart
          scaleShowGridLines: true,
          //String - Colour of the grid lines
          scaleGridLineColor: "rgba(0,0,0,.05)",
          //Number - Width of the grid lines
          scaleGridLineWidth: 1,
          //Boolean - Whether to show horizontal lines (except X axis)
          scaleShowHorizontalLines: true,
          //Boolean - Whether to show vertical lines (except Y axis)
          scaleShowVerticalLines: true,
          //Boolean - If there is a stroke on each bar
          barShowStroke: true,
          //Number - Pixel width of the bar stroke
          barStrokeWidth: 2,
          //Number - Spacing between each of the X value sets
          barValueSpacing: 5,
          //Number - Spacing between data sets within X values
          barDatasetSpacing: 1,
          //String - A legend template
          legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].fillColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>",
          //Boolean - whether to make the chart responsive
          responsive: true,
          maintainAspectRatio: true
        };

        barChartOptions.datasetFill = false;
        barChart.Bar(barChartData, barChartOptions);
}

</script>
