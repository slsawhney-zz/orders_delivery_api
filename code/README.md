# API Reference Documentation: 
    http://localhost:8080/orders

## 1) GET Request:
    By default it will act as a get request and will show the list of top 10 orders.
    But if want to limit your data or want to use pagination the you can append parameters in below manner:
    URL: http://localhost:8080/orders?page=1&limit=3
    Method:GET

    Response: [
        {
            "id": 1,
            "distance": "46732",
            "status": "ASSIGN"
        },
        {
            "id": 2,
            "distance": "46732",
            "status": "ASSIGN"
        },
        {
            "id": 3,
            "distance": "46732",
            "status": "ASSIGN"
        },

        .....


        {
            "id": 10,
            "distance": "128287",
            "status": "ASSIGN"
        }
    ]

## 2) CREATE Request:
    URL: http://localhost:8080/orders
    Method:POST

    Request :{
        "origin": ["28.704060", "77.102493"],
        "destination": ["28.535517", "77.391029"]
    }

    Response:{
        "id": 50,
        "distance": "46732",
        "status": "UNASSIGN"
    }

    Error: {
        "error": "Entered data is not valid"
    }


## 3) UPDATE Request:
    URL: http://localhost:8080/orders/<order_id>
    Method:PATCH

    Request :{
        "status":"TAKEN"
    }

    Response:{
        "status": "SUCCESS"
    }

    Error:  {
        "error": "ORDER_ALREADY_BEEN_TAKEN"
    }

