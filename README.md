# API
POST based JWT API that supports 'services' and actions with permissions

## Example

### user login
```
Request payload:
{
    "service":"user",
    "action":"login",
    "payload":{
        "name":"testuser",
        "password":"testpassword"
    }
}

Response payload:
{
    "service":"user",
    "action":"login",
    "payload":{
        "message":"loggedin successful"
    }
}
```

### user register
```
Request payload:
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

Response payload:
{
    "service":"user",
    "action":"register",
    "payload":{
        "message":"user doesnt already exist - register done"
    }
}
```

### character get
```
Request payload:
{
    "service":"character",
    "action":"get",
    "payload":{
        "character_id":"1"
    }
}

Response payload:
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
```
Request payload:
{
    "service":"character",
    "action":"list"
}

Response payload:
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
```
Request payload:
{
    "service":"character",
    "action":"edit",
    "payload":{
        "character_id":"1",
        "character_name":"charname"
    }
}

Response payload:
{
    "service": "character",
    "action": "edit",
    "payload": {
        "message": "API DONE TODO.."
    }
}
```

### character delete
```
Request payload:
{
    "service":"character",
    "action":"delete",
    "payload":{
        "character_id":"24"
    }
}

Response payload:
{
    "service":"character",
    "action":"delete",
    "payload":{
        "message":"done TODO: errorhandling"
    }
}
```
