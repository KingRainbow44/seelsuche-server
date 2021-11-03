import { HashMap } from "../../utils/hashmap.js";
import { entityManager } from "../../server.js";

export const defaultWorld = new World(1);

export class World {
    // The scene for the world in Unity.
    sceneId = 1;

    // All entities in the current scene.
    entities = new HashMap();
    // Entities that should be rendered in the player's vision.
    renderedEntities = new HashMap();

    constructor(sceneId) {
        this.sceneId = sceneId;
    }

    /*
     * Scene Management
     */

    /**
     * Switch the currently active scene to this world.
     * Activates the player loading screen unless disabled.
     * Unloads all rendered entities on call but keeps all entities
     * in the HashMap.
     */
    switch() {

    }

    getSceneId() {
        return this.sceneId;
    }

    /*
     * Entity Management
     */

    registerEntity(id) {
        this.entities.add(id, entityManager.getEntity(id));
    }

    getEntity(id) {
        return this.entities.get(id);
    }
}