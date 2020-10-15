# API
API written in PHP 7.3 that supports services and actions with permissions.
Accepts only POST requests and uses jwt for authentication.
A service is just a php class with functions in it. This functions are the actions with permissions via roles.
With the [client project](https://github.com/MystixGame/client) its possible to create new services and client pages that use them.

## Installation
- copy it to a [https webserver](https://github.com/MystixCode/webserver_install) ex: https://api.testest.xyz
- change settings in conf.ini file
- execute db.sql to set up some tables and default content

## Usage

### user login

Request payload:
```json
{
    "service":"user",
    "action":"login",
    "payload":{
        "name":"Admin",
        "password":"changethispassword"
    }
}
```

Response payload:
```json
{
    "service":"user",
    "action":"login",
    "payload":{
        "token_type":"bearer",
        "access_token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczpcL1wvYXBpLm15c3RpeGdhbWUudGsiLCJhdWQiOiJodHRwczpcL1wvbXlzdGl4Z2FtZS50ayIsInN1YiI6MSwiaWF0IjoxNjAyNzg2MDg0LCJleHAiOjE2MDI3ODYxMTQsInVzZXJuYW1lIjoiQWRtaW4iLCJyb2xlcyI6WyJhZG1pbiJdfQ==.YngeIsPEaUE0cvdfcNWEcFSaUzzEU0KwuPvrfTnGAeU=",
        "refresh_token":"5f889324d309f"
    }
}
```

### user register

Request payload:
```json
{
    "service":"user",
    "action":"register",
    "payload":{
        "name":"testuser",
        "email":"test@testuser.tk",
        "password":"testuser",
        "password2":"testuser"
    }
}
```

Response payload:
```json
{
    "service":"user",
    "action":"register",
    "payload":{
        "message":"user doesnt already exist - register done"
    }
}
```

### token refresh

Request payload:
```json
{
    "service":"token",
    "action":"refresh",
    "payload":"5f88b18039ff8"
}
```

Response payload:
```json
{
    "service":"token",
    "action":"refresh",
    "payload":{
        "token_type":"bearer",
        "access_token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczpcL1wvYXBpLm15c3RpeGdhbWUudGsiLCJhdWQiOiJodHRwczpcL1wvbXlzdGl4Z2FtZS50ayIsInN1YiI6MSwiaWF0IjoxNjAyNzkzOTg1LCJleHAiOjE2MDI3OTQwMTUsInVzZXJuYW1lIjoiQWRtaW4iLCJyb2xlcyI6WyJhZG1pbiJdfQ==.f_RS7KgBigFa7pMyJZvTIQsst-603obGCq9LElyGJhc=",
        "refresh_token":"5f88b2018b329"
    }
}
```
