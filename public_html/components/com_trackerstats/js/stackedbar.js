	jQuery(function($) {
		var data = [ ["January", 10], ["February", 8], ["March", 4], ["April", 13], ["May", 17], ["June", 9] ];

		$.plot("#placeholder", [ data ], {
			series: {
				lines: {show:false},
				
				bars: {				
					show: true,
					barWidth: 0.6,
					
					align: "center",
					horizontal: true
				}
			},
			yaxis: {
				mode: "categories",
				tickLength: 0
			}
		});

		// Add the Flot version string to the footer

		$("#footer").prepend("Flot " + $.plot.version + " &ndash; ");

	});