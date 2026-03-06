# Authenticating requests

To authenticate requests, include an **`Authorization`** header with the value **`"Bearer {VOTRE_TOKEN_SANCTUM}"`**.

All authenticated endpoints are marked with a `requires authentication` badge in the documentation below.

Obtenez votre token via <b>POST /api/auth/login</b>, puis envoyez-le dans l'en-tête : <code>Authorization: Bearer {token}</code>.
