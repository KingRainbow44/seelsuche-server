/**
 * The server side entity for Seelsuche.
 * Handles the rendering of the object,
 * health, movement, actions, and others.
 */
export class Entity {
    id = 0;

    /*
     * Data variables.
     */
    coordinates = [0, 0, 0];

    constructor(id) {
        this.id = id;
    }

    /**
     * Called on every server update task.
     */
    update() { }

    /*
     * @return mixed
     * methods. Return data stored in variables.
     */

    getId() {
        return this.id;
    }
}

export class EntityData {
    otherStats = [];

    health = 1000;
    defense = 0;
}