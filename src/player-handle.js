// noinspection EqualityComparisonWithCoercionJS

import config from "resources/config.json";
import * as constants from "./constants";
import * as utils from "./utils";
import https from "https";
import http from "http";
import fs from "fs";
import ws from "ws";

let server;
if(config.player_handle.ssl.enabled) {
    let credentials = {
        key: fs.readFileSync(config.player_handle.ssl.key, 'utf-8'),
        certificate: fs.readFileSync(config.player_handle.ssl.certificate, 'utf-8')
    };

    server = https.createServer(credentials);
} else {
    server = http.createServer();
}
server.listen(config.player_handle.ports.websocket);

let ws = new ws.Server({
    server: server
});

ws.on('connection', (ws) => {
    ws.on('message', (data) => {
        if(typeof data != "string") {
            ws.send('error:invalid-request'); return;
        }
        if(!utils.isJson(data)) {
            ws.send('error:invalid-json'); return;
        }

        let request = JSON.parse(data);
        if(request[0] == null || !request[0].startsWith('action-')) {
            ws.send('error:invalid-action'); return;
        }

        switch(request[0]) {
            default:
                return;
            case constants.ACTION_LOGIN:
                if(request.length != 2) {
                    ws.send('error:invalid-request'); return;
                }

                let userHash = request[1]; let userExists = true;
                // Make MySQL request to check if user exists in database.
                if(userExists) {
                    ws.send('login:user-found');
                    // Take contents of MySQL result and send over data needed for client.
                    // JSON-encode this data and send over websocket (base64 encoded)
                } else {
                    ws.send('login:no-user-exists');
                }
                return;
            case constants.ACTION_SIGN_UP:
                if(request.length != 2) {
                    ws.send('error:invalid-request'); return;
                }

                let credentials = request[1].split(":"); let accountExists = false;
                // Make MySQL request to check if that account exists.
                if(!accountExists) {
                    ws.send('signup:sign-up-successful');
                } else {
                    ws.send('signup:account-exists');
                }
                return;
        }
    });
});

ws.on('listening', () => {
    console.log(`Player Handle server started on ${config.player_handle.ports.websocket}.`);
});