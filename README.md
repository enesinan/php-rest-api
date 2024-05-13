![image](https://github.com/enesinan/php-rest-api/assets/72499839/c617261e-e244-41b9-922c-c9f0a66ec36e)## REST API Documentation

This is a simple documentation for a REST API, including endpoints and example requests and responses.

### GET /api/v1/form-submissions

**Endpoint:** `http://localhost/inchubtestapi/wp-json/api/v1/form-submissions`

**Description:** Retrieve a list of all form submissions by sending a GET request.


Example Response:


```http
[
    {
        "id": 1,
        "name": "John Doe",
        "email": "johndoe@example.com"
    },
    {
        "id": 2,
        "name": "Jane Smith",
        "email": "janesmith@example.com"
    },
    ...
]
```
![1](https://github.com/enesinan/php-rest-api/assets/72499839/c1303d10-85cc-4669-8709-e13cbe69e8f4)


### POST /api/v1/form-submissions

**Endpoint:** `http://localhost/inchubtestapi/wp-json/api/v1/form-submissions`

Description: Create a new form submission by sending a POST request. Include a JSON object in the request body with the "name" and "email" fields.

Example Request:

```http
{
    "name": "John Doe",
    "email": "johndoe@example.com"
}
```
Example Response:


```json
{
    "id": 7,
    "name": "John Doe",
    "email": "johndoe@example.com"
}
```
![post](https://github.com/enesinan/php-rest-api/assets/72499839/4c8e9f95-5f42-4379-9253-99cf3471a6a3)


### GET /api/v1/form-submission/{id}

**Endpoint:** `http://localhost/inchubtestapi/wp-json/api/v1/form-submission/{id}`

Description: Retrieve a specific form submission by sending a GET request. Replace "{id}" with the ID of the desired submission.


Example Response:


```json
{
    "id": 7,
    "name": "John Doe",
    "email": "johndoe@example.com"
}
```
![getid](https://github.com/enesinan/php-rest-api/assets/72499839/0acb66df-e9d5-47e1-9f96-44e6f918f103)

### PUT /api/v1/form-submission/{id}

**Endpoint:** `http://localhost/inchubtestapi/wp-json/api/v1/form-submission/{id}`

Description: Update a specific form submission by sending a PUT request. Replace "{id}" with the ID of the submission to update. Include a JSON object in the request body with the "name" and "email" fields.

```
{
    "name": "Updated Name",
    "email": "updatedemail@example.com"
}
```
Example Response:


```
{
    "id": 1,
    "name": "Updated Name",
    "email": "updatedemail@example.com"
}
```
![put](https://github.com/enesinan/php-rest-api/assets/72499839/27fcc54e-d706-4074-9ce8-b9dc113c9ef1)


### DELETE /api/v1/form-submission/{id}

**Endpoint:** `http://localhost/inchubtestapi/wp-json/api/v1/form-submission/{id}`

Description: Delete a specific form submission by sending a DELETE request. Replace "{id}" with the ID of the submission to delete.


Example Response:


```
{
    "status": "success",
    "message": "Successfully deleted"
}
```

![delete](https://github.com/enesinan/php-rest-api/assets/72499839/96cb71d4-2479-4fab-abfa-09749d954ee7)

