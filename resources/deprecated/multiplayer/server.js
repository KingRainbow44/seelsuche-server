/*
 * Imports
 */
import https from "https";
import http from "http";
import fs from "fs";
import { WebSocketServer } from "ws";
import { PlayerManager } from "./player/player-manager.js";
import config from "../../resources/config.json";
import * as utils from "../utils/utils.js";

// Scripts
import * as mms from "./scripts/movement-script.js";

let server; let socket;
export const clients = new PlayerManager();
export class MultiplayerServer {
    constructor(port) {
        /*
         * Start the server.
         */
        if(config.socketing.ssl.enabled) {
            server = https.createServer({
                cert: fs.readFileSync(config.socketing.ssl.certificate),
                key: fs.readFileSync(config.socketing.ssl.privateKey)
            });
        } else {
            server = http.createServer();
        }

        server.listen(port);

        socket = new WebSocketServer({ server });
        socket.on('listening', () => log(`Multiplayer server started on 0.0.0.0:${port}.`));
    }

    listen() {
        socket.on('connection', (ws, req) => {
            let player = clients.addPlayer(ws, req.socket.remoteAddress);
            if(config.multiplayer.settings.developerMode)
                console.log("[mp] New client connected.")

            ws.on('message', (data) => {
                if(typeof data != "object") {
                    ws.send('error:invalid-request'); return;
                } data = Buffer.from(data, 'hex');
                if(config.multiplayer.settings.developerMode)
                    console.log(`[mp] Request: ${data}`);
                if(!utils.isJson(data)) {
                    ws.send('error:invalid-json'); return;
                }

                let request = JSON.parse(data);
                if(request[0] == null || !request[0].startsWith('call-')) {
                    ws.send('error:invalid-action'); return;
                } let method = utils.json2array(request);

                switch(method[0]) {
                    case "call-move":
                        // Call MovementScript to move ID to XYZ.
                        mms.move(player.getEntityId(), request[2]);
                        return;
                }
            });
        });

        socket.on('close', (ws, req) => {
            clients.removePlayer(
                clients.getPlayer(req.socket.remoteAddress)
                    .getId()
            );
        })
    }
}

/**
 * A faster way of logging data.
 * @param msg
 */
function log(msg) {
    console.log(msg);
}