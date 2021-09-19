import edgeMultiplay from "edge-multiplay";
import config from "resources/config.json";

edgeMultiplay.wsServer.on('newConnection', (path, connection) => {
    edgeMultiplay.addToLobby(connection);
});