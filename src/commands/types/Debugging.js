import { Command } from "../command.js";
import { playerManager } from "../../server.js";
import { DispatchChatPacket } from "../../network/protocol/ui/ChatPacket.js";

export class PushChatCommand extends Command {
    constructor() {
        super("pushchat");
    }

    execute(args) {
        playerManager.players.forEach((key, value) => {
            let packet = new DispatchChatPacket();

            packet.message = this.chain(args);
            packet.location = false;
            packet.username = "username";

            value.getRemote().send(packet.encode());
            console.log("[debug] Pushed `" + this.chain(args) + "` to " + value.getAddress())
        });

        console.log("Successfully pushed chat message.")
    }

    chain(array) {
        let string = "";
        array.forEach((value, index) => {
            string += (value + (((array.length - 1) == index) ? "" : " "));
        }); return string;
    }
}