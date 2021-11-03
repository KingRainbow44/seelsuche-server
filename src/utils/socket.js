// noinspection EqualityComparisonWithCoercionJS

/*
 * Imports
 */
import https from "https";
import http from "http";

import { WebSocketServer } from "ws";
import { PlayerManager } from "../player/player-manager.js";

import { isJson, json2array } from "./utils.js";
import { manager } from "../network/packet-manager.js";
import { packetManager, playerManager } from "../server.js";

import * as pids from "../network/protocol/packet-ids.js";

let socket;
export class Socket {
    developerMode = true;

    constructor(server) {
        socket = new WebSocketServer({ server });
        socket.on('listening', () => log(`Server has started listening.`));
    }

    /**
     * Forward data to a client.
     * @deprecated It is favored to use {client.getRemote()} to send data.
     */
    forward(client, packetData) {
        client.getRemote().send(packetData);
    }

    /**
     * Returns the socket.
     * @returns {*}
     */
    clients() {
        return socket.clients;
    }

    /**
     * Makes the socket listen for connections.
     */
    listen() {
        socket.on('connection', (ws, req) => {
            if(this.developerMode)
                console.log(`[socket] Connection from ${req.socket.remoteAddress}.`);
            let player = playerManager.addPlayer(ws, req.socket.remoteAddress);
            packetManager.requestPacket(pids.CLIENT_PING, player);

            ws.on('message', (data) => {
                if(typeof data != "object") {
                    ws.send('error:invalid-request'); return;
                } data = Buffer.from(data, 'hex');
                if(this.developerMode)
                    console.log(`[socket] Request: ${data}`);
                if(!isJson(data)) {
                    ws.send('error:invalid-json'); return;
                }

                let request = JSON.parse(data);
                if(request[0] == null) {
                    ws.send('error:invalid-request'); return;
                } request = json2array(request);

                if(request[0] == "packet") {
                    manager.receivePacket(request, player);
                }
            });
        });

        socket.on('close', (ws, req) => {
            playerManager.removePlayer(
                playerManager.getPlayer(req.socket.remoteAddress)
                    .getId()
            );

            if(this.developerMode)
                log(`[socket] Client disconnected. ${req.socket.remoteAddress}`);
        });
    }
}

export async function makeServer(port) {
    let httpServer = http.createServer();
    httpServer.listen(port);

    return httpServer;
}

export async function makeSecureServer(port, certificate, privateKey) {
    let httpsServer = https.createServer({
        cert: certificate,
        key: privateKey
    });
    httpsServer.listen(port);

    return httpsServer;
}

/**
 * A faster way of logging data.
 * @param msg
 */
function log(msg) {
    console.log(msg);
}