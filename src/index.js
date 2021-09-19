import edgeMultiplay from "edge-multiplay";
import config from "resources/default-config.json";

edgeMultiplay.wsServer.on('newConnection', (path, connection) => {
    edgeMultiplay.addToLobby(connection);
});