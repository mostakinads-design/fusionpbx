# FusionPBX Laravel API Documentation

## Base URL
```
http://your-domain.com/api
```

## Authentication

This API uses Laravel Sanctum for token-based authentication.

### Register New User

**POST** `/api/register`

**Request Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "role": "user"
}
```

**Response:** `201 Created`
```json
{
  "success": true,
  "message": "User registered successfully",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "role": "user"
    },
    "token": "1|abc123..."
  }
}
```

### Login

**POST** `/api/login`

**Request Body:**
```json
{
  "email": "admin@fusionpbx.com",
  "password": "password123"
}
```

**Response:** `200 OK`
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": 1,
      "name": "Admin User",
      "email": "admin@fusionpbx.com",
      "role": "admin"
    },
    "token": "1|abc123..."
  }
}
```

### Logout

**POST** `/api/logout`

**Headers:**
```
Authorization: Bearer {token}
```

**Response:** `200 OK`
```json
{
  "success": true,
  "message": "Logged out successfully"
}
```

### Get Authenticated User

**GET** `/api/user`

**Headers:**
```
Authorization: Bearer {token}
```

**Response:** `200 OK`
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Admin User",
    "email": "admin@fusionpbx.com",
    "role": "admin"
  }
}
```

---

## Users

### List All Users

**GET** `/api/users`

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `page` (optional): Page number for pagination (default: 1)
- `per_page` (optional): Items per page (default: 15)

**Response:** `200 OK`
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "name": "Admin User",
        "email": "admin@fusionpbx.com",
        "role": "admin",
        "created_at": "2026-02-09T05:00:00.000000Z"
      }
    ],
    "total": 5,
    "per_page": 15,
    "last_page": 1
  }
}
```

### Get Single User

**GET** `/api/users/{id}`

**Headers:**
```
Authorization: Bearer {token}
```

**Response:** `200 OK`
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Admin User",
    "email": "admin@fusionpbx.com",
    "role": "admin",
    "extensions": []
  }
}
```

### Create User

**POST** `/api/users`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "name": "New User",
  "email": "newuser@example.com",
  "password": "password123",
  "role": "user"
}
```

**Response:** `201 Created`
```json
{
  "success": true,
  "message": "User created successfully",
  "data": {
    "id": 6,
    "name": "New User",
    "email": "newuser@example.com",
    "role": "user"
  }
}
```

### Update User

**PUT** `/api/users/{id}`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "name": "Updated Name",
  "email": "updated@example.com",
  "role": "admin"
}
```

**Response:** `200 OK`
```json
{
  "success": true,
  "message": "User updated successfully",
  "data": {
    "id": 1,
    "name": "Updated Name",
    "email": "updated@example.com",
    "role": "admin"
  }
}
```

### Delete User

**DELETE** `/api/users/{id}`

**Headers:**
```
Authorization: Bearer {token}
```

**Response:** `200 OK`
```json
{
  "success": true,
  "message": "User deleted successfully"
}
```

---

## Extensions

### List All Extensions

**GET** `/api/extensions`

**Headers:**
```
Authorization: Bearer {token}
```

**Response:** `200 OK`
```json
{
  "success": true,
  "data": {
    "data": [
      {
        "id": 1,
        "extension_number": "1001",
        "user_id": 2,
        "status": "active",
        "description": "Sales Department Extension",
        "user": {
          "id": 2,
          "name": "John Smith"
        }
      }
    ]
  }
}
```

### Get Single Extension

**GET** `/api/extensions/{id}`

**Headers:**
```
Authorization: Bearer {token}
```

**Response:** `200 OK`
```json
{
  "success": true,
  "data": {
    "id": 1,
    "extension_number": "1001",
    "user_id": 2,
    "status": "active",
    "description": "Sales Department Extension",
    "user": {
      "id": 2,
      "name": "John Smith",
      "email": "john@example.com"
    }
  }
}
```

### Create Extension

**POST** `/api/extensions`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "extension_number": "1009",
  "user_id": 2,
  "status": "active",
  "description": "New Extension"
}
```

**Response:** `201 Created`
```json
{
  "success": true,
  "message": "Extension created successfully",
  "data": {
    "id": 9,
    "extension_number": "1009",
    "status": "active",
    "description": "New Extension"
  }
}
```

### Update Extension

**PUT** `/api/extensions/{id}`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "status": "inactive",
  "description": "Updated description"
}
```

**Response:** `200 OK`
```json
{
  "success": true,
  "message": "Extension updated successfully",
  "data": {
    "id": 1,
    "extension_number": "1001",
    "status": "inactive",
    "description": "Updated description"
  }
}
```

### Delete Extension

**DELETE** `/api/extensions/{id}`

**Headers:**
```
Authorization: Bearer {token}
```

**Response:** `200 OK`
```json
{
  "success": true,
  "message": "Extension deleted successfully"
}
```

---

## Call Logs

### List All Call Logs

**GET** `/api/call-logs`

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `page` (optional): Page number
- `per_page` (optional): Items per page

**Response:** `200 OK`
```json
{
  "success": true,
  "data": {
    "data": [
      {
        "id": 1,
        "caller_id": "+15551234567",
        "destination": "1001",
        "duration": 125,
        "status": "answered",
        "call_type": "inbound",
        "call_date": "2026-02-09T10:30:00.000000Z"
      }
    ]
  }
}
```

### Get Single Call Log

**GET** `/api/call-logs/{id}`

**Headers:**
```
Authorization: Bearer {token}
```

**Response:** `200 OK`
```json
{
  "success": true,
  "data": {
    "id": 1,
    "caller_id": "+15551234567",
    "destination": "1001",
    "duration": 125,
    "status": "answered",
    "call_type": "inbound",
    "call_date": "2026-02-09T10:30:00.000000Z"
  }
}
```

### Create Call Log

**POST** `/api/call-logs`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "caller_id": "+15559876543",
  "destination": "1002",
  "duration": 60,
  "status": "answered",
  "call_type": "outbound",
  "call_date": "2026-02-09T12:00:00Z"
}
```

**Response:** `201 Created`
```json
{
  "success": true,
  "message": "Call log created successfully",
  "data": {
    "id": 51,
    "caller_id": "+15559876543",
    "destination": "1002",
    "duration": 60,
    "status": "answered",
    "call_type": "outbound"
  }
}
```

### Filter Call Logs by Status

**GET** `/api/call-logs/filter/status/{status}`

**Headers:**
```
Authorization: Bearer {token}
```

**Parameters:**
- `status`: answered, missed, busy, or failed

**Response:** `200 OK`
```json
{
  "success": true,
  "data": {
    "data": [
      {
        "id": 1,
        "caller_id": "+15551234567",
        "destination": "1001",
        "status": "answered",
        "duration": 125
      }
    ]
  }
}
```

### Filter Call Logs by Caller

**GET** `/api/call-logs/filter/caller/{caller}`

**Headers:**
```
Authorization: Bearer {token}
```

**Parameters:**
- `caller`: Partial or full caller ID

**Response:** `200 OK`
```json
{
  "success": true,
  "data": {
    "data": [
      {
        "id": 1,
        "caller_id": "+15551234567",
        "destination": "1001",
        "duration": 125
      }
    ]
  }
}
```

---

## Settings

### List All Settings

**GET** `/api/settings`

**Headers:**
```
Authorization: Bearer {token}
```

**Response:** `200 OK`
```json
{
  "success": true,
  "data": {
    "data": [
      {
        "id": 1,
        "key": "app_name",
        "value": "FusionPBX",
        "type": "string",
        "group": "system",
        "description": "Application name"
      }
    ]
  }
}
```

### Get Single Setting

**GET** `/api/settings/{id}`

**Headers:**
```
Authorization: Bearer {token}
```

**Response:** `200 OK`
```json
{
  "success": true,
  "data": {
    "id": 1,
    "key": "app_name",
    "value": "FusionPBX",
    "type": "string",
    "group": "system"
  }
}
```

### Get Setting by Key

**GET** `/api/settings/key/{key}`

**Headers:**
```
Authorization: Bearer {token}
```

**Response:** `200 OK`
```json
{
  "success": true,
  "data": {
    "id": 1,
    "key": "app_name",
    "value": "FusionPBX",
    "type": "string",
    "group": "system"
  }
}
```

### Create/Update Setting

**POST** `/api/settings`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "key": "custom_setting",
  "value": "custom_value",
  "type": "string",
  "group": "custom",
  "description": "A custom setting"
}
```

**Response:** `201 Created`
```json
{
  "success": true,
  "message": "Setting created successfully",
  "data": {
    "id": 13,
    "key": "custom_setting",
    "value": "custom_value",
    "type": "string"
  }
}
```

### Update Setting

**PUT** `/api/settings/{id}`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "value": "updated_value",
  "description": "Updated description"
}
```

**Response:** `200 OK`
```json
{
  "success": true,
  "message": "Setting updated successfully",
  "data": {
    "id": 1,
    "key": "app_name",
    "value": "updated_value"
  }
}
```

### Delete Setting

**DELETE** `/api/settings/{id}`

**Headers:**
```
Authorization: Bearer {token}
```

**Response:** `200 OK`
```json
{
  "success": true,
  "message": "Setting deleted successfully"
}
```

---

## Error Responses

### 400 Bad Request
```json
{
  "success": false,
  "message": "Validation error",
  "errors": {
    "email": ["The email field is required."]
  }
}
```

### 401 Unauthorized
```json
{
  "success": false,
  "message": "Unauthenticated"
}
```

### 403 Forbidden
```json
{
  "success": false,
  "message": "Forbidden"
}
```

### 404 Not Found
```json
{
  "success": false,
  "message": "Resource not found"
}
```

### 500 Internal Server Error
```json
{
  "success": false,
  "message": "An error occurred",
  "error": "Error details..."
}
```

---

## Rate Limiting

The API is rate-limited to 60 requests per minute per user. When you exceed this limit, you'll receive a `429 Too Many Requests` response.

## Testing with cURL

### Example: Login and Make Request

```bash
# 1. Login
TOKEN=$(curl -X POST http://your-domain.com/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@fusionpbx.com","password":"password123"}' \
  | jq -r '.data.token')

# 2. Use token to access protected endpoint
curl -X GET http://your-domain.com/api/users \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json"
```

---

## Testing with Postman

1. Create a new request
2. Set the method (GET, POST, PUT, DELETE)
3. Enter the URL (e.g., `http://your-domain.com/api/users`)
4. Add headers:
   - `Authorization: Bearer {your_token}`
   - `Accept: application/json`
   - `Content-Type: application/json` (for POST/PUT)
5. Add request body (for POST/PUT requests)
6. Send the request

---

## Best Practices

1. **Always use HTTPS in production**
2. **Store tokens securely** (never in local storage for web apps)
3. **Handle token expiration** gracefully
4. **Validate all input** on the client side before sending
5. **Use appropriate HTTP methods**
6. **Check response status codes**
7. **Implement proper error handling**
8. **Use pagination** for list endpoints
9. **Filter and search** to reduce data transfer
10. **Keep tokens confidential**

---

**Last Updated:** February 2026  
**API Version:** 1.0.0
