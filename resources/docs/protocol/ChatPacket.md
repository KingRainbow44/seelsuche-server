# ChatPacket
Send a message to another client (or a multiple of clients).

## Identification
`0x04` (server) `0x03` (client)

### Client -> Server (Inbound)
1. `string` - The raw chat message to push to the server. Can be no longer than 56 characters.
2. `string` - The user ID to send this to, or `team` to push it to the team chat. 

### Server -> Client (Outbound)
1. `string` - The raw chat message to show on the client. (TODO: Add message encryption.)
2. `boolean` - Defines where to place this message, if true, read the next string, false means to push to team chat.
3. `string` - (optional) The username of the sending user.