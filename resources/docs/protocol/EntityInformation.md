# Entity Information
Transmit entity flags, data, and others with this packet. Position is exclusive to Movement Packet.

## Identification
`0x08` (server)

### Server -> Client (Outbound)
1. `string|int` - Entity runtime ID or Player UID specified by the server.
2. `string` - JSON-encoded flags for the specified entity or player.