/**
 * A list of {server} packet IDs.
 */

export const CLIENT_PING = "0x01";
export const CLIENT_AUTH_RESPONSE = "0x02";
export const CLIENT_ACCOUNT_RESPONSE = "0x03";
export const CHAT_PACKET = "0x04";

/**
 * A list of {client} packet IDs.
 */

export const CLIENT_PING_RESPONSE = "0x01";
export const CLIENT_AUTH = "0x02";
export const CLIENT_ACCOUNT_REQUEST = "0x03";
// export const CHAT_PACKET = "0x04"; // This is removed because this packet already exists (with the same name and value).