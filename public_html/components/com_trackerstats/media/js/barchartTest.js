/**
 * @copyright Copyright (C) 2013 Mark Dexter. All rights reserved.
 * @license GNU General Public License version 2 or later; see LICENSE.txt
 */
(function ($) {
	$.JQPLOTBarchartTest = function(containerId, urlId, barDirection) {
		$.jqplot.config.enablePlugins = true;
		// The url for our json data
		var jsonurl = $("#" + urlId).attr("href");

		var drawjqChart = function(url, tag) {
			$.ajax({
				url : url,
				type : "GET",
				dataType : "json",
				success : onDataReceived
			});

			function onDataReceived(series) {
				var chartData = series[0];
				var chartTicks = series[1];
				var chartLabels = series[2];
				var title = series[3];
				var xaxis = {renderer: $.jqplot.CategoryAxisRenderer, ticks: chartTicks};
				var yaxis = {padMin: 0, pad: 1.05};
				if (barDirection == 'horizontal')
					{
						temp = yaxis;
						yaxis = xaxis;
						xaxis = temp;
					}
				var plot2 = $.jqplot(containerId, chartData, {
		           title: title,
		           stackSeries: true,
		            seriesDefaults:{
		                renderer:$.jqplot.BarRenderer,
		                pointLabels: { show: false },
		                rendererOptions : {
							fillToZero : true,
							barDirection : barDirection,
//							barWidth: 50,
							barMargin: 50,
//							barPadding: 1,
						},
		            },
		            series : chartLabels,
		            legend : {
						show : true,
						placement : 'outsideGrid'
					},
		            axes: {
		                xaxis: xaxis,
		                yaxis: yaxis
		            },
		            highlighter: { show: false }
		        });
			}

		};
		drawjqChart(jsonurl, containerId);
	}
})(jQuery);
