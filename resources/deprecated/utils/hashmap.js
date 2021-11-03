export class HashMap {
    array = [];

    constructor(value) {
        if(value != null && typeof value == 'object') {
            this.array = value;
        }
    }

    values() {
        return this.array;
    }

    add(key, value) {
        this.array.push([
            key,
            value
        ]);
    }

    get(key) {
        let value = null;
        this.array.forEach(arrVal => {
            if(arrVal[0] === key)
                value = arrVal[1]
        });
        return value;
    }

    remove(key) {
        this.array.forEach((value, index) => {
            if(value[0] === key)
                this.array.splice(index, 1);
        });
    }

    forEach(callable) {
        this.values().forEach(value => {
            callable(value[0], value[1]);
        });
    }
}