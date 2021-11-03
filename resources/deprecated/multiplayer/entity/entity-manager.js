import { HashMap } from "../../utils/hashmap.js";
import { Entity } from "./entity.js";

export class EntityManager {
    entities = new HashMap();
    nextId = 0;

    addEntity(entity) {
        if(typeof entity !== typeof Entity) return null;
        this.entities.add(this.nextId, entity);
        this.nextId++; return entity;
    }

    getEntity(value) {
        switch(typeof value) {
            default:
                return null;
            case "number":
                return this.entities.get(value);
            case "string":
                let player = null;
                this.entities.values().forEach(arrVal => {
                    if(arrVal[1].getAddress() === value)
                        player = arrVal[1];
                });
                return player;
        }
    }

    removeEntity(id) {
        this.entities.remove(id);
    }
}