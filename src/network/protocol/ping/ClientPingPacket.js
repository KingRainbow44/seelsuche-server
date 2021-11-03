import { OutboundPacket, InboundPacket } from "../packet.js";
import { CLIENT_PING, CLIENT_PING_RESPONSE } from "../packet-ids.js";

export class ClientPingPacket extends OutboundPacket {
    encode() {
        this.writeString(Date.now());
        return JSON.stringify(this.prepare());
    }

    pid() {
        return CLIENT_PING;
    }
}

export class ClientPingPacketResponse extends InboundPacket {
    time = null;

    decode() {
        this.time = this.next();
    }

    pid() {
        return CLIENT_PING_RESPONSE;
    }
}