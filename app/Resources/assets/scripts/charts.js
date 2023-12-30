var Index = function () {


    return {

        //main function
        init: function () {
            App.addResponsiveHandler(function () {
                jQuery('.vmaps').each(function () {
                    var map = jQuery(this);
                    map.width(map.parent().width());
                });
            });
        },

        initCharts: function (PrData) {
            if (!jQuery.plot) {
                return;
            }

            var data = [];

            var totalPoints = 250;

            // random data generator for plot charts

            function getRandomData() {
                if (data.length > 0) data = data.slice(1);
                // do a random walk
                while (data.length < totalPoints) {
                    var prev = data.length > 0 ? data[data.length - 1] : 50;
                    var y = prev + Math.random() * 10 - 5;
                    if (y < 0) y = 0;
                    if (y > 100) y = 100;
                    data.push(y);
                }
                // zip the generated y values with the x values
                var res = [];
                for (var i = 0; i < data.length; ++i) res.push([i, data[i]])
                return res;
            }

            function showTooltip(x, y, countPr, createdDate) {
                $('<div id="tooltip" class="chart-tooltip">' +
                    '<div class="date">' +  createdDate + '<\/div>' +
                    '<div class="label label-success">Project: ' + countPr + '%<\/div>' +
                    '').css({
                    position: 'absolute',
                    display: 'none',
                    top: y - 100,
                    width: 100,
                    left: x - 40,
                    border: '0px solid #ccc',
                    padding: '2px 6px',
                    'background-color': '#fff',
                }).appendTo("body").fadeIn(200);
            }

            function randValue() {
                return (Math.floor(Math.random() * (1 + 50 - 20))) + 10;
            }

            var pageviews = PrData.dataOpen ;
            var visitors  = PrData.dataClose;

            if ($('#site_statistics').size() != 0) {

                $('#site_statistics_loading').hide();
                $('#site_statistics_content').show();

                var plot_statistics = $.plot($("#site_statistics"), [
                    {
                        data: visitors,
                        label: "Close Pr"
                    },
                    {
                        data: pageviews,
                        label: "Open Pr"
                    }
                ], {
                    series: {
                        lines: {
                            show: true,
                            lineWidth: 2,
                            fill: true,
                            fillColor: {
                                colors: [
                                    {
                                        opacity: 0.05
                                    },
                                    {
                                        opacity: 0.01
                                    }
                                ]
                            }
                        },
                        points: {
                            show: true
                        },
                        shadowSize: 2
                    },
                    grid: {
                        hoverable: true,
                        clickable: true,
                        tickColor: "#eee",
                        borderWidth: 0
                    },
                    colors: ["#d12610", "#37b7f3", "#52e136"],
                    xaxis: {
                        ticks: 11,
                        tickDecimals: 0
                    },
                    yaxis: {
                        ticks: 11,
                        tickDecimals: 0
                    }
                });

                var previousPoint = null;
                $("#site_statistics").bind("plothover", function (event, pos, item) {
                    $("#x").text(pos.x.toFixed(2));
                    $("#y").text(pos.y.toFixed(2));
                    if (item) {
                        if (previousPoint != item.dataIndex) {
                            previousPoint = item.dataIndex;

                            $("#tooltip").remove();
                            var x = item.datapoint[0].toFixed(2),
                                y = item.datapoint[1].toFixed(2),
                                createdDate = item.series.data[item.dataIndex][2];

                            showTooltip(item.pageX, item.pageY, y,createdDate);
                        }
                    } else {
                        $("#tooltip").remove();
                        previousPoint = null;
                    }
                });
            }
        },

        initPieCharts :function (){

            $('.easy-pie-chart .number.transactions').easyPieChart({
                animate: 1000,
                size: 50,
                lineWidth: 3,
                barColor: App.getLayoutColorCode('yellow')
            });

            $('.easy-pie-chart .number.visits').easyPieChart({
                animate: 1000,
                size: 50,
                lineWidth: 3,
                barColor: App.getLayoutColorCode('blue')
            });

            $('.easy-pie-chart .number.bounce').easyPieChart({
                animate: 1000,
                size: 50,
                lineWidth: 3,
                barColor: App.getLayoutColorCode('red')
            });

            $('.easy-pie-chart-reload').click(function () {
                $('.easy-pie-chart .number').each(function () {
                    var newValue = Math.floor(100 * Math.random());
                    $(this).data('easyPieChart').update(newValue);
                    $('span', this).text(newValue);
                });
            });

            $("#sparkline_bar").sparkline([8, 9, 10, 11, 10, 10, 12, 10, 10, 11, 9, 12, 11, 10, 9, 11, 13, 13, 12], {
                    type: 'bar',
                    width: '100',
                    barWidth: 5,
                    height: '55',
                    barColor: '#35aa47',
                    negBarColor: '#e02222'}
            );

            $("#sparkline_bar2").sparkline([9, 11, 12, 13, 12, 13, 10, 14, 13, 11, 11, 12, 11, 11, 10, 12, 11, 10], {
                    type: 'bar',
                    width: '100',
                    barWidth: 5,
                    height: '55',
                    barColor: '#ffb848',
                    negBarColor: '#e02222'}
            );

            $("#sparkline_line").sparkline([9, 10, 9, 10, 10, 11, 12, 10, 10, 11, 11, 12, 11, 10, 12, 11, 10, 12], {
                type: 'line',
                width: '100',
                height: '55',
                lineColor: '#ffb848'
            });
        },

        initChartsPurchase: function (PoData) {
            if (!jQuery.plot) {
                return;
            }
            var data = [];

            var totalPoints = 250;

            // random data generator for plot charts

            function getRandomData() {
                if (data.length > 0) data = data.slice(1);
                // do a random walk
                while (data.length < totalPoints) {
                    var prev = data.length > 0 ? data[data.length - 1] : 50;
                    var y = prev + Math.random() * 10 - 5;
                    if (y < 0) y = 0;
                    if (y > 100) y = 100;
                    data.push(y);
                }
                // zip the generated y values with the x values
                var res = [];
                for (var i = 0; i < data.length; ++i) res.push([i, data[i]])
                return res;
            }

            function showTooltip(x, y, countPr, createdDate) {

                $('<div id="tooltip" class="chart-tooltip">' +
                    '<div class="date">' +  createdDate + '<\/div>' +
                    '<div class="label label-success">Project: ' + countPr + '%<\/div>' +
                    '').css({
                    position: 'absolute',
                    display: 'none',
                    top: y - 100,
                    width: 100,
                    left: x - 40,
                    border: '0px solid #ccc',
                    padding: '2px 6px',
                    'background-color': '#fff'
                }).appendTo("body").fadeIn(200);
            }

            function randValue() {
                return (Math.floor(Math.random() * (1 + 50 - 20))) + 10;
            }

            var pageviews = PoData.dataOpen;

            var visitors = PoData.dataClose;

            if ($('#site_statistics_Po').size() != 0) {

                $('#site_statistics_loading1').hide();
                $('#site_statistics_content_Po').show();

                var plot_statistics = $.plot($("#site_statistics_Po"), [
                    {
                        data: visitors,
                        label: "Close Po"
                    },
                    {
                        data: pageviews,
                        label: "Open Po"
                    }
                ], {
                    series: {
                        lines: {
                            show: true,
                            lineWidth: 2,
                            fill: true,
                            fillColor: {
                                colors: [
                                    {
                                        opacity: 0.05
                                    },
                                    {
                                        opacity: 0.01
                                    }
                                ]
                            }
                        },
                        points: {
                            show: true
                        },
                        shadowSize: 2
                    },
                    grid: {
                        hoverable: true,
                        clickable: true,
                        tickColor: "#eee",
                        borderWidth: 0
                    },
                    colors: ["#d12610", "#37b7f3", "#52e136"],
                    xaxis: {
                        ticks: 11,
                        tickDecimals: 0
                    },
                    yaxis: {
                        ticks: 11,
                        tickDecimals: 0
                    }
                });

                var previousPoint = null;
                $("#site_statistics_Po").bind("plothover", function (event, pos, item) {
                    $("#x").text(pos.x.toFixed(2));
                    $("#y").text(pos.y.toFixed(2));
                    if (item) {
                        if (previousPoint != item.dataIndex) {
                            previousPoint = item.dataIndex;

                            $("#tooltip").remove();
                            var x = item.datapoint[0].toFixed(2),
                                y = item.datapoint[1].toFixed(2),
                                createdDate = item.series.data[item.dataIndex][2];

                            showTooltip(item.pageX, item.pageY, y,createdDate);
                        }
                    } else {
                        $("#tooltip").remove();
                        previousPoint = null;
                    }
                });
            }



        }

    };

}();