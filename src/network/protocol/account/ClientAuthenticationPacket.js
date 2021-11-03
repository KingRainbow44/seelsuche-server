import { OutboundPacket, InboundPacket } from "../packet.js";
import { CLIENT_AUTH, CLIENT_AUTH_RESPONSE } from "../packet-ids.js";

import { database, packetManager } from "../../../server.js";

export class ClientAuthenticationResponsePacket extends OutboundPacket {
    receivingData = false; username = null;
    inventory = null; statistics = null;

    encode() {
        this.writeBoolean(this.receivingData);
        this.writeString(this.username);
        this.writeString(this.inventory);
        this.writeString(this.statistics);
        return JSON.stringify(this.prepare());
    }

    pid() {
        return CLIENT_AUTH_RESPONSE;
    }
}

export class ClientAuthenticationPacket extends InboundPacket {
    time = null; ipAddress = null;
    userHash = null; has2fa = false;
    twoFactorCode = 0o0000;

    decode() {
        this.time = this.next();
        this.ipAddress = this.next();
        this.userHash = this.next();
        this.has2fa = this.next();

        if(this.has2fa === true)
            this.twoFactorCode = this.next();
    }

    handle(client) {
        database.query("SELECT * FROM `player-data` WHERE `userHash`='" + this.userHash + "';", result => {
            let packet = new ClientAuthenticationResponsePacket();
            if(result != null && result.length > 0) {
                let rDP = result[0];
                packet.username = rDP.username;
            }

            packetManager.sendDataPacket(packet, client);
        });
    }

    pid() {
        return CLIENT_AUTH;
    }
}