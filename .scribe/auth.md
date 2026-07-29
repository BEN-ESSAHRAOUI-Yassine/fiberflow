# Authenticating requests

To authenticate requests, include an **`Authorization`** header with the value **`"Bearer {YOUR_TOKEN}"`**.

All authenticated endpoints are marked with a `requires authentication` badge in the documentation below.

Obtain a token by POSTing to `/api/v1/login` with your email and password. Include the returned token in the `Authorization` header as `Bearer {token}`.
