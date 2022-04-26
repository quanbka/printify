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
                series: [{
                    name: 'Tỉ lệ auto fulfill',
                    data: []

                }]
            });
            var totalOrders = 0;
            var totalFulfillOrders = 0;
            async function initSlide () {
                let days = await getRecentDays();
                let ratio = await getRatios(days);
            }
            async function getRecentDays () {
                let retval = [];
                for (var i = 1648771200000; i < Date.now(); i = i + 84600000) {
                    time = new Date(i);
                    retval.push(time.toISOString().slice(0, 10));
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
                return retval;
            }
            async function getRatio (day) {
                let url = `https://glob.api.printerval.com/v2/order?sorts=-created_at&get_is_merge=1&filters=order.created_at=[${day};${day}%2023:59:59],order.payment_status=paid&metric=count`;
                let response = await axios.get(url);
                let order = response.data.result;
                totalOrders += order;
                url = `https://glob.api.printerval.com/v2/order?sorts=-created_at&get_is_merge=1&filters=order.created_at=[${day};${day}%2023:59:59],order.payment_status=paid&metric=count&scopes=orderMeta(keys=[is_auto_fulfill];values=[4])`;
                response = await axios.get(url);
                let fulfill_order = response.data.result;
                totalFulfillOrders += fulfill_order;
                return fulfill_order * 100 / order;
            }
            initSlide()
            </script>
    </body>
</html>
