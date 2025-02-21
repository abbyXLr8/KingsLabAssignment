Get a jwt token: curl -X POST -H "Content-Type: application/json" -d '{"username":"user1","password":"pass1"}' http://localhost/login.php

Use the token in api requests: curl -X POST -H "Authorization: Bearer YOUR_TOKEN" -H "Content-Type: application/json" -d '{"name":"Laptop","price":1000,"description":"Gaming laptop"}' http://localhost/products