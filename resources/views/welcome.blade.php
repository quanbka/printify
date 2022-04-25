<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <title></title>
    </head>
    <body>
            <div id="container" style="width:100%; height:400px;"></div>
            <script src="https://code.highcharts.com/highcharts.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

            <script type="text/javascript">
            chart = Highcharts.chart('container', {
                title: {
                    text: 'Tỉ lệ autofulfill trong 90 ngày vừa qua'
                },
                subtitle: {
                    text: 'Nguồn: Printerval'
                },
                xAxis: {
                    categories: [
                        'Jan',
                        'Feb',
                        'Mar',
                        'Apr',
                        'May',
                        'Jun',
                        'Jul'

                    ],
                    crosshair: true
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: 'Tỉ lệ auto fulfill (mm)'
                    }
                },
                tooltip: {
                    headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                    pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                        '<td style="padding:0"><b>{point.y:.1f} %</b></td></tr>',
                    footerFormat: '</table>',
                    shared: true,
                    useHTML: true
                },
                plotOptions: {
                    column: {
                        pointPadding: 0.2,
                        borderWidth: 0
                    }
                },
                series: [{
                    name: 'Tỉ lệ auto fulfill',
                    data: []

                }]
            });

            var totalOrders = 0;
            var totalFulfillOrders = 0;

            async function initSlide () {
                let days = await getRecentDays();
                console.log(days);
                let ratio = await getRatios(days);
                console.log(ratio);

            }

            async function getRecentDays () {
                let retval = [];
                for (var i = -90; i < 0; i++) {
                    retval.push(new Date(Date.now() + i * 84600000).toISOString().slice(0, 10));
                }
                chart.update({
                    xAxis : {
                        categories : retval
                    }
                });
                return retval;
            }

            async function getRatios (days) {
                let retval = [];
                for (var i = 0; i < days.length; i++) {
                    retval.push(await getRatio(days[i]));
                    chart.update({
                        series : {
                            data : retval
                        },
                        subtitle : { text : `Số đơn autofulfill: ${totalFulfillOrders} / ${totalOrders} ` },
                        title : { text : `Tỉ lệ auto fulfill : ${totalFulfillOrders * 100 / totalOrders} %` },
                    });
                }
                // console.log(totalOrders)
                // console.log(totalFulfillOrders)
                return retval;
            }

            async function getRatio (day) {
                let url = `https://glob.api.printerval.com/v2/order?sorts=-created_at&get_is_merge=1&filters=order.created_at=[${day};${day}%2023:59:59],order.payment_status=paid&metric=count`;
                let response = await axios.get(url);
                let order = response.data.result;
                totalOrders += order;
                // console.log(order);
                url = `https://glob.api.printerval.com/v2/order?sorts=-created_at&get_is_merge=1&filters=order.created_at=[${day};${day}%2023:59:59],order.payment_status=paid&metric=count&scopes=orderMeta(keys=[is_auto_fulfill];values=[4])`;
                response = await axios.get(url);
                let fulfill_order = response.data.result;
                totalFulfillOrders += fulfill_order;
                // console.log(fulfill_order);
                return fulfill_order * 100 / order;
            }

            initSlide()

            </script>



            Filter
    </body>
</html>
