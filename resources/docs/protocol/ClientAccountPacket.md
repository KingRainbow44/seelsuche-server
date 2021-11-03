# ClientAccountPacket
Make account & or Save user data between the client and server.

## Identification
`0x03`

### Client -> Server (Inbound)
1. `string` - The current UNIX timestamp. Logged to the database as a safety measure.
2. `string` - The client's IP address. Logged to the database as a safety measure.
3. `boolean` - Defines if the action is a save user data or create account.
4. `string` - Argument 1. Can be a to-split string (username:password) or the user's hash. (userHash)
5. `string` - (optional) Argument 2. Outgoing-player data to save.

### Server -> Client (Outbound)
1. `boolean` - Defines if the action is a save user data or create account.
2. `string` - Response Argument 1. Always a user hash.
3. `boolean` - Response Argument 2. True or False depending on if the data was saved.