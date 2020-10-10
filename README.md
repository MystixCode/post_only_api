# API
POST based API that support mutliple services and actions with permissions

##Example

###Login
```
Request payload:
{"service":"user","action":"login","payload":{"name":"testuser","password":"testpassword"}}
Response payload:
{"service":"user","action":"login","payload":{"message":"loggedin successful"}}
```

###Register
```
Request payload:
{"service":"user","action":"register","payload":{"name":"testuser","email":"test@testuser.tk","password":"testuser","password2":"testuser"}}
Response payload:
{"service":"user","action":"register","payload":{"message":"user doesnt already exist - register done"}}
```
