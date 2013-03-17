jQuery(document).ready(function($) {

	// The url for our json data
	var jsonurl = $("#chart2").attr("href");

	var drawjqChart = function(url, tag) {
		$.ajax({
			url : url,
			type: "GET",
			dataType : "json",
			success : onDataReceived
		});
		
		function onDataReceived(series) {
			var chartData = series[0];
			var chartTicks = series[1];
			var chartLabels = series[2];
			var plot2 = $.jqplot('chart2', chartData, {
				title : "Get Data from AJAX Request",
				stackSeries : true,
				// The "seriesDefaults" option is an options object that will
				// be applied to all series in the chart.
				seriesDefaults : {
					renderer : $.jqplot.BarRenderer,
					rendererOptions : {
						fillToZero : true,
						barDirection : 'horizontal'
					}
				},
				// Custom labels for the series are specified with the "label"
				// option on the series option. Here a series option object
				// is specified for each series.
				series : chartLabels,
				// Show the legend and put it outside the grid, but inside the
				// plot container, shrinking the grid to accomodate the legend.
				// A value of "outside" would not shrink the grid and allow
				// the legend to overflow the container.
				legend : {
					show : true,
					placement : 'outsideGrid'
				},
				axes : {
					// Use a category axis on the x axis and use our custom ticks.
					yaxis : {
						renderer : $.jqplot.CategoryAxisRenderer,
						ticks : chartTicks
					},
				// Pad the y axis just a little so bars can get close to, but
				// not touch, the grid boundaries. 1.2 is the default padding.
				// yaxis: {
				// pad: 1.05,
				// tickOptions: {formatString: '$%d'}
				// }
				}
			});
		}
		

	};
	drawjqChart(jsonurl, "chart2");
});
