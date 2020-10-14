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
        "message":"loggedin successful"
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

### character get

Request payload:
```json
{
    "service":"character",
    "action":"get",
    "payload":{
        "character_id":"1"
    }
}
```

Response payload:
```json
{
    "service": "character",
    "action": "get",
    "payload": {
        "character_id": 1,
        "character_name": "charname1"
    }
}
```

### character list

Request payload:
```json
{
    "service":"character",
    "action":"list"
}
```

Response payload:
```json
{
    "service": "character",
    "action": "list",
    "payload": [
        {
            "character_id": 1,
            "character_name": "charname1"
        },
        {
            "character_id": 2,
            "character_name": "charname2"
        }
    ]
}
```

### character edit

Request payload:
```json
{
    "service":"character",
    "action":"edit",
    "payload":{
        "character_id":"1",
        "character_name":"charname"
    }
}
```

Response payload:
```json
{
    "service": "character",
    "action": "edit",
    "payload": {
        "message": "API DONE TODO.."
    }
}
```

### character delete

Request payload:
```json
{
    "service":"character",
    "action":"delete",
    "payload":{
        "character_id":"24"
    }
}
```

Response payload:
```json
{
    "service":"character",
    "action":"delete",
    "payload":{
        "message":"done TODO: errorhandling"
    }
}
```
