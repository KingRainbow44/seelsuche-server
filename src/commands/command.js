export class Command {
    label = "defaultLabel";

    constructor(label) {
        this.label = label;
    }

    getLabel() {
        return this.label;
    }

    execute(args) {

    }
}