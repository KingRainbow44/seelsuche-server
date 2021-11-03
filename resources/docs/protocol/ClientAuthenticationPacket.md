# ClientAuthenticationPacket
Sends authentication data between both the client and server.

## Identification
`0x02`

### Client -> Server (Inbound)
1. `string` - The current UNIX timestamp. Logged to the database as a safety measure.
2. `string` - The client's IP address. Logged to the database as a safety measure.
3. `string` - The user's hashed variant of their credentials. (acts as a token)
4. `boolean` - Tells the server if it should read again. Acts as the 2FA-token barrier.
5. `int` - (optional) The 2FA code provided by the client.

### Server -> Client (Outbound)
1. `boolean` - True or False, returned depending on if the client is going to receive the necessary login information.
2. `string` - (optional) The username of the user (from database).
3. `string` - (optional) The serialized-form of the user inventory.
4. `string` - (optional) The serialized-form of the user's statistics.