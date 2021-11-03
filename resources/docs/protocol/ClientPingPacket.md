# ClientPingPacket
Ping packet sent between both the client and server. Can be used to calculate the latency between the client and server.

## Identification
`0x01`

### Server -> Client (Outbound)
1. `string` - The current UNIX timestamp.

### Client -> Server (Inbound)
1. `string` - The current UNIX timestamp.