# ClientAuthenticationPacket
Sends authentication data between both the client and server.

## Identification
`0x02`

### Client -> Server (Inbound)
1. `string` - The current UNIX timestamp. Logged to the database as a safety measure.
2. `string` - The client's IP address. Logged to the database as a safety measure.
3. `string` - The user's hashed variant of their credentials. (acts as a token)
4. `string` - The user's username that is in the hashed credentials.

### Server -> Client (Outbound)
1. `int` - A status code that indicates if the request was okay or failed with a reason.
2. `string` - (optional) The username of the user (from database).
3. `string` - (optional) The serialized-form of the user inventory.
4. `string` - (optional) The serialized-form of the user's statistics.

##### Status Codes (for response)
- `200` - The request is **ok** and the user will be receiving the data.
- `404` - The username & pass do **not** exist at all.
- `403` - The user hash does not match what is on record. This is usually because the password is wrong.
- `429` - There have been too many login attempts on this user in the last hour. Try again in another hour.