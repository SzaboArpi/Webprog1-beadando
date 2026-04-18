class Menu {
    constructor(title, x, y) {
        this.container = document.createElement("div");
        this.container.style.position = "absolute";
        this.container.style.left = x + "px";
        this.container.style.top = y + "px";
        this.container.style.border = "1px solid black";
        this.container.style.padding = "30px";
        this.container.style.borderRadius= "15px";
        this.container.style.boxShadow = "2px 2px 10px gray";

        this.title = document.createElement("h4");
        this.title.innerText = title;
        this.container.appendChild(this.title);

        this.list = document.createElement("ul");
        this.container.appendChild(this.list);

        document.body.appendChild(this.container);
    }

    show() {
        this.container.style.visibility = "visible";
    }

    hide() {
        this.container.style.visibility = "hidden";
    }

    addItem(text) {
        let li = document.createElement("li");
        li.innerText = text;
        this.list.appendChild(li);
    }

    moveTo(x, y) {
        this.container.style.left = x + "px";
        this.container.style.top = y + "px";
    }
}


class ColoredMenu extends Menu {
    constructor(title, x, y, color) {
        super(title, x, y);
        this.container.style.backgroundColor = color;
    }

    addItem(text) {
        super.addItem(text);
        console.log("Új elem hozzáadva: " + text);
    }
}