jQuery(document).ready(function($) {

	// The url for our json data
	var jsonurl = $("#chart2").attr("href");
	var ticks = [ 'May', 'June', 'July', 'August' ];
	// Our ajax data renderer which here retrieves a text file.
	// it could contact any source and pull data, however.
	// The options argument isn't used in this renderer.
	var ajaxDataRenderer = function(url, plot, options) {
		var ret = null;
		$.ajax({
			// have to use synchronous here, else the function
			// will return before the data is fetched
			async : false,
			url : url,
			dataType : "json",
			success : function(data) {
				ret = data[0];
				ticks = data[1];
			}
		});
		return ret;
	};

	// passing in the url string as the jqPlot data argument is a handy
	// shortcut for our renderer. You could also have used the
	// "dataRendererOptions" option to pass in the url.
	var plot2 = $.jqplot('chart2', jsonurl, {
		title : "AJAX JSON Data Renderer",
		dataRenderer : ajaxDataRenderer,
		dataRendererOptions : {
			unusedOptionalUrl : jsonurl
		},
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
		series : [ {
			label : 'Hotel'
		}, {
			label : 'Event Regristration'
		}, {
			label : 'Airfare'
		} ],
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
				ticks : ticks
			},
		// Pad the y axis just a little so bars can get close to, but
		// not touch, the grid boundaries. 1.2 is the default padding.
		// yaxis: {
		// pad: 1.05,
		// tickOptions: {formatString: '$%d'}
		// }
		}
	});
});