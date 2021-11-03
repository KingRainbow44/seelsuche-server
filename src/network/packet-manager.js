// noinspection EqualityComparisonWithCoercionJS

/*
 * Imports
 */
import * as pids from "./protocol/packet-ids.js";
import { ClientPingPacketResponse, ClientPingPacket } from "./protocol/ping/ClientPingPacket.js";
import { ClientAuthenticationResponsePacket, ClientAuthenticationPacket } from "./protocol/account/ClientAuthenticationPacket.js";
import { ClientAccountRequestPacket, ClientAccountResponsePacket } from "./protocol/account/ClientAccountPacket.js";
import { DispatchChatPacket, ReceiveChatPacket } from "./protocol/ui/ChatPacket.js";
import { OutboundPacket } from "./protocol/packet.js";
import { multiplayer } from "../server.js";

/**
 * The primary manager for packet handling.
 * Forwards, encodes, and decodes packets
 * sent by clients.
 */
export class PacketManager {
    receivePacket(data, client) {
        /*
         * [0] = Request type.
         * [1] = JSON-array of data.
         * [2] = Packet ID (string).
         */

        if(data[0] != "packet")
            return;

        let packet;
        switch(data[2]) {
            default:
                return;
            case pids.CLIENT_PING_RESPONSE:
                packet = new ClientPingPacketResponse(data[1]);
                break;
            case pids.CLIENT_AUTH_RESPONSE:
                packet = new ClientAuthenticationPacket(data[1]);
                break;
            case pids.CLIENT_ACCOUNT_REQUEST:
                packet = new ClientAccountRequestPacket(data[1]);
                break;
            case pids.CHAT_PACKET:
                packet = new ReceiveChatPacket(data[1]);
                break;
        }

        if(multiplayer.developerMode)
            console.log(packet.toDecode);
        packet.decode(); packet.handle(client);
    }

    requestPacket(pid, client) {
        let packet;
        switch(pid) {
            default:
                return;
            case pids.CLIENT_PING:
                packet = new ClientPingPacket();
                break;
            case pids.CLIENT_AUTH_RESPONSE:
                packet = new ClientAuthenticationResponsePacket();
                break;
            case pids.CLIENT_ACCOUNT_RESPONSE:
                packet = new ClientAccountResponsePacket();
                break;
            case pids.CHAT_PACKET:
                packet = new DispatchChatPacket();
                break;
        }

        client.getRemote().send(packet.encode());
    }

    sendDataPacket(packet, client) {
        if(typeof packet !== typeof(OutboundPacket))
            return;
        client.getRemote().send(packet.encode());
    }
}

/*
 * Placed here so Node.JS doesn't throw an initialization error.
 */
export const manager = new PacketManager();