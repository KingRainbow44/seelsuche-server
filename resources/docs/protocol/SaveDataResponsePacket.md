# SaveDataResponsePacket
Alerts the client that the data on the server is being saved.

## Identification
`0x03`

### Server -> Client (Outbound)
1. `boolean` - This contains whether the data was saved or not.
2. `string` - The current UNIX timestamp of the save time.