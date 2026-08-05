#!/bin/bash
set -e
curl -s -c /tmp/cj.txt https://triemdragonherbs.hu/webshop -o /tmp/webshop.html
TOKEN=$(grep -oP "X-CSRF-TOKEN': '\K[^']+" /tmp/webshop.html | head -1)
echo "CSRF=${TOKEN:0:20}..."
curl -s -b /tmp/cj.txt -c /tmp/cj.txt -X POST https://triemdragonherbs.hu/orders \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "X-CSRF-TOKEN: $TOKEN" \
  -d '{"name":"otto","phone":"30468988888","shipping_address":"Nagyréde kossuth 32","billing_address":"Nagyréde kossuth 32","items":[{"id":1,"title":"Bazsalikom","price":800,"qty":2}],"total_price":5099,"terms_accepted":true,"payment_method":"barion","shipping_method":"mpl","delivery_type":"home"}' \
  -w "\nHTTP_CODE:%{http_code}\n"
