// noinspection EqualityComparisonWithCoercionJS

import config from "../resources/config.json";
import * as constants from "./constants.js";
import { Database } from "./database.js";
import * as utils from "./utils.js";
import https from "https";
import http from "http";
import fs from "fs";
import { WebSocketServer } from "ws";

let server;
if(config.player_handle.ssl.enabled) {
    server = https.createServer({
        cert: fs.readFileSync(config.player_handle.ssl.certificate),
        key: fs.readFileSync(config.player_handle.ssl.key)
    });
} else {
    server = http.createServer();
}
server.listen(config.player_handle.ports.websocket);

let socket = new WebSocketServer({ server });
let database = new Database();

socket.on('connection', (ws) => {
    ws.on('message', (data) => {
        if(typeof data != "object") {
            ws.send('error:invalid-request'); return;
        } data = Buffer.from(data, 'hex');
        if(!utils.isJson(data)) {
            ws.send('error:invalid-json'); return;
        }

        let request = JSON.parse(data);
        if(request[0] == null || !request[0].startsWith('action-')) {
            ws.send('error:invalid-action'); return;
        } request = utils.json2array(request);

        switch(request[0]) {
            default:
                return;
            case constants.ACTION_LOGIN:
                if(request.length != 2) {
                    ws.send('error:invalid-request'); return;
                }

                let userHash = request[1];
                database.userExists(userHash, (exists) => {
                    if(!exists) {
                        ws.send('login:user-found');
                        // Take contents of MySQL result and send over data needed for client.
                        // JSON-encode this data and send over websocket (base64 encoded)
                    } else {
                        ws.send('login:no-user-exists');
                    }
                });
                return;
            case constants.ACTION_SIGN_UP:
                if(request.length != 2) {
                    ws.send('error:invalid-request'); return;
                }

                let credentials = request[1].split(":");
                let accountHash = credentials[0] + ":" + utils.sha256(credentials[1]);
                let buffer = Buffer.from(accountHash); accountHash = buffer.toString('base64url');
                database.userExists(accountHash, (exists) => {
                    if(!exists) {
                        database.insertUserData(accountHash);
                        ws.send('signup:sign-up-successful');
                    } else {
                        ws.send('signup:account-exists');
                    }
                });
                return;
        }
    });
});

socket.on('listening', () => {
    console.log(`Player Handle server started on ${config.player_handle.ports.websocket}.`);
});