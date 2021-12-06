# Co-op Packet
The backbone for the co-op system. Handles all co-op-related actions.

## Identification
`0x07` (server) `0x06` (client)

### Server -> Client (Outbound)
1. `int` - The action for the co-op packet.
2. `int` - The status code of the action.
3. `string` - (optional) The display name of the user who wants to join.

### Client -> Server (Inbound)
1. `int` - The action for the co-op packet.
2. `string` - (optional) The user ID for the player to invite/kick/join.

### Actions
- `-1` - No response is given except for a status code. (outbound)
- `0` - The client wants to start a co-op session. (inbound)
- `1` - The client wants to disband a co-op session. (inbound)
- `2` - The client wants to invite a player to their co-op session. (inbound)
- `3` - The client wants to kick a player from their co-op session. (inbound)
- `4` - An invited player wants to join another co-op session. (inbound)
- `5` - The player has been kicked from the co-op session. (outbound)
- `6` - The player has an incoming co-op session invite. (outbound)
- `7` - The host is receiving a join request. (outbound)
  - `8` - The client is joining a world shortly. (outbound)

### Status Codes
- `200` - The action went through successfully.
  - This also means the player was kicked because the host returned to single-player.
- `403` - The player has been kicked because the host kicked the client.
- `3xx` - A category of co-op status codes. All listed below:
  - `300` - Local player is a host, add a player at current position.
  - `301` - Local player is a client, move player to host position, create host & other player models.

### Notes
- Sending an `invite` request to a person who has sent a `join` request will add the requester to the world.
- Both an `invite` and `request` have to have a display name attached.