'use strict';

const fs = require('fs');
const axios = require('axios').default;
const knex = require('knex')({
  client: 'mysql2',
  connection: {
    host : '192.168.1.172',
    port : 3306,
    user : 'root',
    password : '123@123',
    database : 'shopot'
  }
});


//
(async () => {

let json = fs.readFileSync('de-gelato.json');
let jsonData = JSON.parse(json);
// console.log(response.result);
for (var i = 0; i < jsonData.result.length; i++) {
    console.log(i);
    let order = jsonData.result[i];
    console.log(order.id);
    let data = JSON.parse(order.data);
    // console.log(data);
    let items = data.items;
    // console.log(items);
    for (var j = 0; j < items.length; j++) {
        let item = items[j];
        // console.log(j);
        let url = `https://de.api.printerval.com/order_item/${items[j].itemReferenceId}`;
        // console.log(url);
        let response = await axios.get(url)
        // console.log(response.data);
        if (response.data.result) {
            let product_sku_id = response.data.result.product_sku_id;
            // console.log(product_sku_id);
            // console.log(url);
            url = `https://printerval.com/de/print-suppliers/${product_sku_id}/template`;
            response = await axios.get(url)
            // console.log("response")
            if (response.data.input[0]) {
                // consolelog(i);
                await knex('sb_print_suplliers_gelato_template').insert(
                    {
                        category_id: response.data.input[0],
                        color : response.data.input[1],
                        size : response.data.input[2],
                        type : response.data.input[3],
                        style: response.data.input[4],
                        product_uid : item.productUid,
                        country: 'de'
                    }
                )
                console.log(response.data.input)
                console.log(item.productUid);
            }
        }
    }

}

})();
