<style type="text/css">
bar{
    height:10px;
    background:#99c;
}
</style>
<script type="text/javascript">

	jQuery(function($) {
		var visits = <?php echo $flot_data_visits; ?>;
		var views = <?php echo $flot_data_views; ?>;

		$('#analytics').css({
			height: '300px',
			width: '800px'
		});

		$.plot($('#analytics'), [{ label: 'Visitors', data: visits },{ label: 'Views', data: views }], {
			lines: { show: true },
			points: { show: true },
			grid: { hoverable: true, clickable: true, backgroundColor: '#fffaff' },
			series: {
				lines: { show: true, lineWidth: 1 },
				shadowSize: 0
			},
			xaxis: { mode: "time" },
			yaxis: { min: 0},
			selection: { mode: "x" }
		});
                function showTooltip(x, y, contents) {
                    $('<div id="tooltip">' + contents + '</div>').css( {
                        position: 'absolute',
                        display: 'none',
                        top: y + 5,
                        left: x + 5,
                        border: '1px solid #ddf',
                        padding: '2px',
                        'background-color': '#eef',
                        opacity: 0.80
                    }).appendTo("body").fadeIn(200);
                }


//================================================
                previousPoint = null;
                $("#analytics").bind("plothover", function (event, pos, item) {
                    
                    // secondary axis coordinates if present are in pos.x2, pos.y2,
                    // if you need global screen coordinates, they are pos.pageX, pos.pageY

                    if (item) {
                        if (previousPoint != item.datapoint) {
                            previousPoint = item.datapoint;

                            var x = Math.round(item.datapoint[0].toFixed(2)),
                                y = Math.round(item.datapoint[1].toFixed(2));
                                mydate=new Date(x).toLocaleDateString();
                            showTooltip(item.pageX, item.pageY,
                                    item.series.label +" " + y +" <br> " + mydate );
                        };
                      //alert("You over at " + pos.x + ", " + pos.y);
                    }else{
                         $("#tooltip").remove()
                         previousPoint = null;
                    }
                });

            });

</script>


<h1>Site statistics since <?php echo $start_date?> to <?php echo $end_date?></h1>
<div id="analytics" class="line" style="padding-bottom: 10px"></div>
<table>
    <tr>
        <td>
            <h2>Top referrers:</h2>
            <?php echo $topReferrers; ?>
        </td>
        <td>
            <h2>Top search words:</h2>
            <?php echo $topSearchWords; ?>
        </td>
    </tr>
    <tr>
        <td>
            <h2>Top Screen resolution:</h2>
            <?php echo $topScreenResolution; ?>
        </td>
        <td>
            <h2>Top browsers:</h2>
            <?php echo $topBrowsers; ?>
        </td>
    </tr>
</table>