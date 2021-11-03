export class Player {
    remote = null; id = 0;
    address = null;

    /*
     * This is different to the sprite/entity id.
     * This is a unique identifier for each player
     */
    userId = "0000000000";

    /*
     * Data variables.
     */
    coordinates = [0, 0, 0];

    constructor(ws, address, id) {
        this.remote = ws;
        this.address = address;
        this.id = id;
    }

    /*
     * @return mixed
     * methods. Return data stored in variables.
     */

    getId() {
        return this.id;
    }

    getUserId() {
        return this.userId;
    }

    getAddress() {
        return this.address;
    }

    getRemote() {
        return this.remote;
    }
}