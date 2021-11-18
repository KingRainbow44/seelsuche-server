# AudioPacket
Send voice chat data to other clients.

## Identification
`0x05` (server) `0x04` (client)

### Client -> Server (Inbound)
1. `int` - The channels in the incoming audio clip.
2. `int` - The samples in the incoming audio clip.
3. `int` - The frequency of the incoming audio clip.
4. `string` - (JSON-encoded) The audio data of the incoming audio clip.
5. `string` - JSON-encoded data containing the players to send the audio to.

### Server -> Client (Outbound)
1. `int` - The channels in the outgoing audio clip.
2. `int` - The samples in the outgoing audio clip.
3. `int` - The frequency of the outgoing audio clip.
4. `string` - (JSON-encoded) The audio data of the outgoing audio clip.
5. `float` - The volume to set the player to when playing this audio clip.