import { OutboundPacket, InboundPacket } from "../packet.js";
import { CHAT_PACKET } from "../packet-ids.js";

import { packetManager } from "../../../server.js";

export class DispatchChatPacket extends OutboundPacket {
    message = ""; location = false;
    username = null;

    encode() {
        this.writeString(this.message);
        this.writeBoolean(this.location);
        if(this.location == true)
            this.writeString(this.username);
        return JSON.stringify(this.prepare());
    }

    pid() {
        return CHAT_PACKET;
    }
}

export class ReceiveChatPacket extends InboundPacket {
    message = ""; userID = "";

    decode() {
        this.message = this.next();
        this.userID = this.next();
    }

    handle() {
        if(this.message == "team") {
            // TODO: Implement the Co-Op system.
        } else {

        }
    }

    pid() {
        return CHAT_PACKET;
    }
}