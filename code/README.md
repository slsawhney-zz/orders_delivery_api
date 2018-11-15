#API Reference Documentation: 
    http://localhost:8080/orders

##1) GET Request:
    By default it will act as a get request and will show the list of top 10 orders.
    But if want to limit your data or want to use pagination the you can append parameters in below manner:
    URL: http://localhost:8080/orders/2/10
    Method:GET

    Response: [
        {
            "id": 1,
            "distance": "46.732 Km",
            "status": "ASSIGN"
        },
        {
            "id": 2,
            "distance": "46.732 Km",
            "status": "ASSIGN"
        },
        {
            "id": 3,
            "distance": "46.732 Km",
            "status": "ASSIGN"
        },

        .....


        {
            "id": 10,
            "distance": "128.287 Km",
            "status": "ASSIGN"
        }
    ]

##2) CREATE Request:
    URL: http://localhost:8080/order
    Method:POST

    Request :{
        "origin": ["28.704060", "77.102493"],
        "destination": ["28.535517", "77.391029"]
    }

    Response:{
        "id": 50,
        "distance": "46.732 Km",
        "status": "UNASSIGN"
    }

    Error: {
        "error": "Entered data is not valid"
    }


##3) UPDATE Request:
    URL: http://localhost:8080/order/<order_id>
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

