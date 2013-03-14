window.addEvent('domready', function() {
var graphData = new Array(
[100],
[150],
[75]
);
var myGraph = new mooBarGraph({
container: $('myGraph'),
data: graphData
width: 400 // width of graph panel in px
height: 300 // height of graph panel in px
title: false // graph title. can use html tags
barSpace: 10 // space between bars in px
color: '#111111' // default color for your bars
colors: false // array of colors. it will be used for parts of stacked type or will be repeated for simple type
sort: false // 'asc' or 'desc', this can be used only for simple type
prefix: '' // string that will be show before bar value
postfix: '' // string that will be shown after bar value
legend: false // set to true if you want to lefend box be created
legendWidth: 100 // width of legend box in px
legends: false // array of values for legend
showValues: true // for stacked bars type only. false to hide values in sub bars.
showValuesColor: '#fff' // color for values in parts for stacked type
realTime: false // refreshing graph with new data in real time
});
});

window.addEvent('domready', function() {
$$('.hasTip').each(function(el) {
var title = el.get('title');
if (title) {
var parts = title.split('::', 2);
el.store('tip:title', parts[0]);
el.store('tip:text', parts[1]);
}
});
var JTooltips = new Tips($$('.hasTip'), { maxTitleChars: 50, fixed: false});
}); 