# API: Restful

# Resource: restfulResource
+ id: string
+ name: string
+ email: email
+ content: text

# HTTP: GET /restfulResources
## Example: All
  + Response:
    + Content-Type: application/json
    + Status Code: 200
    + Body:
    ```
    {
      "restfulResources": [
        {
          "id": "1",
          "name": "rest name",
          "email": "rest@email.com",
          "content": "this is some content about this restful content"
        }
      ]
    }
    ```
