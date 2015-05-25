# StenoAPI Version 0.0.0
StenoAPI is the process in writing short expressive API documents. Its too easy for APIs to get out of date and undocumented. Using this documentation process will help design and maintain the single source of truth for your APIs. 

# Sections 
  1. Version 
  2. API 
  3. Resource 
  4. Operation
  
# Version
The version section describes which document and parser format is being used. This allows StenoAPI grow and keep backwards compatibility. 

```
# Version: 0.0.0
```

# API
Brief API title and description to define the purpose for the API.

```
# API: <title>
```

# Resource 
The resource is the object returned after performing an API operation. This resource section also defines its attributes and types. Attributes are written as comma separated string with attribute name, type and a brief description. 

```
# Resource: <name>
+ id, number, Unique identifier
+ title, string, Resource's Title
```

# Operation
Documents may have many operations. Operations headers are described by its method and uri. 

``` 
# Operation: <METHOD> <uri>
```

## Operation's Sub Section
  1. Parameters
  2. Payload
  3. Example
  
### Parameters 
Available url parameters 

```
## Parameters
  + id , number, Unique Identifier
```

### Payload
The request payload structure for sending data to the server

```
## Payload
  + id , required|number, Unique Identifier
```

### Example
Operation's mock request & response

```
## Example
+ Request
  + Content-Type: <type>
  + Payload: <content>
+ Response
  + Content-Type: <type>
  + Status Code: <status>
  + Body: <content>
```

