'use strict';

const fs = require('fs');
const axios = require('axios').default;

(async () => {

let json = fs.readFileSync('de-gelato.json');
let jsonData = JSON.parse(json);
// console.log(response.result);
for (var i = 0; i < jsonData.result.length; i++) {
    let order = jsonData.result[i];
    let data = JSON.parse(order.data);
    // console.log(order.target_id);
    let items = data.items;
    // console.log(items);
    let item = items[0];
    let url = `https://de.api.printerval.com/order_item/${items[0].itemReferenceId}`;
    // console.log(url);
    let response = await axios.get(url)
    if (response.data.result) {
        let product_sku_id = response.data.result.product_sku_id;
        // console.log(product_sku_id);
        url = `https://printerval.com/de/print-suppliers/${product_sku_id}/template`;
        response = await axios.get(url)
        if (response.data.input[0]) {
            console.log(response.data.input)
            console.log(item.productUid);
        }
    }
}

})();
