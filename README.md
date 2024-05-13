## REST API Documentation

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
### DELETE /api/v1/form-submission/{id}

**Endpoint:** `http://localhost/inchubtestapi/wp-json/api/v1/form-submission/{id}`

Description: Delete a specific form submission by sending a DELETE request. Replace "{id}" with the ID of the submission to delete.


Example Response:


```
{
    "message": "Form submission with ID 1 deleted."
}
```
