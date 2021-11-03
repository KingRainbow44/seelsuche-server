// noinspection EqualityComparisonWithCoercionJS

import { OutboundPacket, InboundPacket } from "../packet.js";
import { CLIENT_ACCOUNT_RESPONSE, CLIENT_ACCOUNT_REQUEST } from "../packet-ids.js";

import { database, packetManager } from "../../../server.js";
import { insertAccount, updateAccount } from "../../../utils/database.js";
import { sha256, base64 } from "../../../utils/utils.js";

export class ClientAccountResponsePacket extends OutboundPacket {
    action = false; userHash = null;
    dataSaved = false;

    encode() {
        this.writeBoolean(this.action);
        this.writeString(this.userHash);
        this.writeBoolean(this.dataSaved);
        return JSON.stringify(this.prepare());
    }

    pid() {
        return CLIENT_ACCOUNT_RESPONSE;
    }
}

export class ClientAccountRequestPacket extends InboundPacket {
    time = null; ipAddress = null; action = false;
    arg1 = null; arg2 = null;

    decode() {
        this.time = this.next();
        this.ipAddress = this.next();
        this.action = this.next();
        this.arg1 = this.next();

        if(this.action == true) {
            this.arg2 = this.next();
        }
    }

    handle(client) {
        // True = Save Player Data; False = Create Account
        if(!this.action) {
            let credArray = this.arg1.split(":");
            database.query(`SELECT * FROM \`accounts\` WHERE \`username\`='${credArray[0]}';`, result => {
                let packet = new ClientAccountResponsePacket();
                packet.action = false;
                if(result != null && result.length < 1) {
                    // Username:Sha256-Password
                    let hashed = credArray[0] + ":" + sha256(credArray[1]);
                    packet.userHash = base64(hashed);
                    packet.dataSaved = true;

                    insertAccount(packet.userHash);
                } else {
                    packet.dataSaved = false;
                }
                packetManager.sendDataPacket(packet, client);
            });
        } else {
            updateAccount(this.arg1, this.arg2);
        }
    }

    pid() {
        return CLIENT_ACCOUNT_REQUEST;
    }
}