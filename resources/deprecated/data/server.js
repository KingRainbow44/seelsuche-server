// noinspection EqualityComparisonWithCoercionJS

/*
 * Imports
 */
import https from "https";
import http from "http";
import fs from "fs";
import { WebSocketServer } from "ws";
import config from "../../resources/config.json";
import * as utils from "../utils/utils.js";

import { database } from "../server.js";
import { clients } from "../multiplayer/server.js";
import * as dbu from "../utils/data-query.js";

import { CONNECTION_REQUEST, CONNECTION_ACCEPTED } from "../utils/constants.js";

let server; let socket;
export class DataServer {
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
        socket.on('listening', () => log(`Data server started on 0.0.0.0:${port}.`));
    }

    listen() {
        socket.on('connection', (ws, req) => {
            if(clients.getPlayer(req.socket.remoteAddress) == null) {
                ws.send('error:not-connected'); ws.close();
                return;
            }

            if(config.data.settings.developerMode)
                console.log("[data] New client connected.")

            ws.on('message', data => {
                if(clients.getPlayer(req.socket.remoteAddress) == null) {
                    ws.send('error:not-connected'); ws.close();
                    return;
                }

                if(typeof data != "object") {
                    ws.send('error:invalid-request'); return;
                } data = Buffer.from(data, 'hex');
                if(config.data.settings.developerMode)
                    console.log(`[data] Request: ${data}`);

                if(data == CONNECTION_REQUEST) {
                    ws.send(CONNECTION_ACCEPTED);
                    return;
                }

                if(!utils.isJson(data)) {
                    ws.send('error:invalid-json'); return;
                }

                let request = JSON.parse(data);
                if(request[0] == null || !request[0].startsWith('call-')) {
                    ws.send('error:invalid-action'); return;
                } let method = utils.json2array(request);

                switch(method[0]) {
                    case "call-signin":
                        if(method.length != 2) {
                            ws.send('error:invalid-request'); return;
                        }

                        let userHash = method[1]; let username = utils.hashToArray(userHash)[0];
                        dbu.userExists(database, username, (exists) => {
                            if(exists) {
                                ws.send('login:user-found');
                                // Login matching client on the server-side.
                                let player = clients.getPlayer(req.socket.remoteAddress);
                                player.login(userHash);

                                // Take contents of MySQL result and send over data needed for client.
                                // JSON-encode this data and send over websocket. (base64 encoded)
                                let data = player.getPlayerData();
                                ws.push(new Buffer(
                                    JSON.stringify(utils.clientParse(data).values())
                                ).toString('base64'));
                            } else {
                                ws.send('login:no-user-exists');
                            }
                        });
                        return;
                    case "call-signup":
                        if(method.length != 2) {
                            ws.send('error:invalid-request'); return;
                        }

                        let credentials = method[1].split(":");
                        let accountHash = credentials[0] + ":" + utils.sha256(credentials[1]);
                        let buffer = Buffer.from(accountHash); accountHash = buffer.toString('base64url');
                        if(config.data.settings.developerMode)
                            console.log(accountHash);
                        dbu.userExists(database, credentials[0], (exists) => {
                            if(!exists) {
                                dbu.insertUserData(database, accountHash);
                                ws.send('signup:sign-up-successful');
                            } else {
                                ws.send('signup:account-exists');
                            }
                        });
                        return;
                }
            });
        });
    }
}

/**
 * A faster way of logging data.
 * @param msg
 */
function log(msg) {
    console.log(msg);
}