<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <title></title>
    </head>
    <body>
            <div id="container" style="width:100%; height:800px;"></div>
            <script src="https://code.highcharts.com/highcharts.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

            <script type="text/javascript">
            chart = Highcharts.chart('container', {
                chart: {
                    type: 'area'
                },
                plotOptions: {
                    area: {
                        stacking: 'normal',
                        lineColor: '#666666',
                        lineWidth: 1,
                        marker: {
                            lineWidth: 1,
                            lineColor: '#666666'
                        }
                    }
                },
                title: {
                    text: 'Tỉ lệ autofulfill trong 90 ngày vừa qua'
                },
                subtitle: {
                    text: 'Nguồn: Printerval'
                },
                series: [{
                    name: 'Tỉ lệ auto fulfill',
                    data: []
                }, {
                    name: 'Tỉ lệ không có template fulfill',
                    data: []
                }, {
                    name: 'Tỉ lệ không có design',
                    data: []
                }
            ]
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
                let retval2 = [];
                let retval3 = [];
                for (var i = 0; i < days.length; i++) {
                    let ratio = await getRatio(days[i]);
                    retval.push(ratio.fulfill * 100 / ratio.order);
                    retval2.push(ratio.notTemplate * 100 / ratio.order);
                    retval3.push(ratio.notDesign * 100 / ratio.order);

                    chart.update({
                        series : [

                            {
                                name : "Tỉ lệ đơn không có template",
                                data : retval2
                            },
                            {
                                name : "Tỉ lệ đơn không có design",
                                data : retval3
                            },
                            {
                                name : "Tỉ lệ auto fulfill",
                                data : retval
                            },
                        ],
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

                url = `https://glob.api.printerval.com/v2/order?sorts=-created_at&get_is_merge=1&filters=order.created_at=[${day};${day}%2023:59:59],order.payment_status=paid&metric=count&scopes=orderMeta(keys=[is_auto_fulfill];values=[4])`;
                response = await axios.get(url);
                let fulfill = response.data.result;

                url = `https://glob.api.printerval.com/v2/order?sorts=-created_at&get_is_merge=1&filters=order.created_at=[${day};${day}%2023:59:59],order.payment_status=paid&metric=count&scopes=orderMeta(keys=[reason_auto_fulfill_fail,is_auto_fulfill];values=[template,0])`;
                response = await axios.get(url);
                let notTemplate = response.data.result;

                url = `https://glob.api.printerval.com/v2/order?sorts=-created_at&get_is_merge=1&filters=order.created_at=[${day};${day}%2023:59:59],order.payment_status=paid&metric=count&scopes=orderMeta(keys=[reason_auto_fulfill_fail,is_auto_fulfill];values=[design,2])`;
                response = await axios.get(url);
                let notDesign = response.data.result;

                totalOrders += order;
                totalFulfillOrders += fulfill;
                return {
                    fulfill,
                    order,
                    notTemplate,
                    notDesign
                };
            }
            initSlide()
            </script>
    </body>
</html>
