# Movement Packet
Packets send between the client and the server to update the location of entities & players in real-time.

## Identification
`0x06` (server) `0x05` (client)

### Server -> Client (Outbound; PlayerPositionPacket)
1. `float` - The current X coordinate offset from **Vector3.zero**
2. `float` - The current Y coordinate offset from **Vector3.zero**
3. `float` - The current Z coordinate offset from **Vector3.zero**
4. `float` - The pitch of the player.
5. `float` - (optional) The pitch of the camera. Used in VR controls.
6. `float` - (optional) The yaw of the camera. Used in VR controls.

### Client -> Server (Inbound; EntityPlayOutPositionPacket)
1. `boolean` - Is the packet for an entity or a player?
2. `string|int` - The player UID or entity runtime ID.
3. `float` - The current X coordinate offset from **Vector3.zero**
4. `float` - The current Y coordinate offset from **Vector3.zero**
5. `float` - The current Z coordinate offset from **Vector3.zero**
6. `float` - The current pitch of the entity's model.