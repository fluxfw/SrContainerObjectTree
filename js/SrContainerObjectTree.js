document.addEventListener("DOMContentLoaded", () => {
    class SrContainerObjectTree {
        constructor({container_ref_id, el, empty_text, error_text, fetch_url}) {
            this.container_ref_id = container_ref_id;
            this.el = el;
            this.empty_text = empty_text;
            this.error_text = error_text;
            this.fetch_url = fetch_url;
        }

        async clickNode({arrow_el, children_el, ref_id}) {
            arrow_el.classList.toggle("SrContainerObjectTreeArrowOpen");

            if (children_el.children.length === 0) {
                await this.fetchTree({
                    parent_el: children_el,
                    parent_ref_id: ref_id
                });
            }
        }

        async fetchTree({parent_el, parent_ref_id}) {
            const loading_el = document.createElement("div");
            loading_el.classList.add("SrContainerObjectTreeLoading");
            parent_el.appendChild(loading_el);

            let children;
            try {
                children = await (await fetch(`${this.fetch_url}${parent_ref_id}`, {
                    headers: {
                        "accept": "application/js",
                        "content-type": "application/js"
                    }
                })).json();
            } catch (err) {
                console.error(err);

                const error_el = document.createElement("div");
                error_el.classList.add("SrContainerObjectTreeError");
                error_el.innerText = this.error_text;
                parent_el.appendChild(error_el);

                return;
            } finally {
                parent_el.removeChild(loading_el);
            }

            if (children.length > 0) {
                for (const {icon, is_container, link, ref_id, title} of children) {
                    const node_el = document.createElement("div");
                    node_el.classList.add("SrContainerObjectTreeNode");

                    const children_el = document.createElement("div");
                    children_el.classList.add("SrContainerObjectTreeChildren");

                    if (is_container) {
                        const arrow_el = document.createElement("div");
                        arrow_el.classList.add("SrContainerObjectTreeArrow");

                        arrow_el.addEventListener("click", this.clickNode.bind(this, {
                            arrow_el,
                            children_el,
                            ref_id
                        }));

                        node_el.appendChild(arrow_el);
                    }

                    const link_el = document.createElement("a");
                    link_el.classList.add("SrContainerObjectTreeLink");
                    link_el.href = link;

                    const icon_el = document.createElement("img");
                    icon_el.classList.add("SrContainerObjectTreeIcon");
                    icon_el.src = icon;
                    link_el.appendChild(icon_el);

                    const title_el = document.createElement("div");
                    title_el.classList.add("SrContainerObjectTreeTitle");
                    title_el.innerText = title;
                    link_el.appendChild(title_el);

                    node_el.appendChild(link_el);

                    if (is_container) {
                        node_el.appendChild(children_el);
                    }

                    parent_el.appendChild(node_el);
                }
            } else {
                const empty_el = document.createElement("div");
                empty_el.classList.add("SrContainerObjectTreeEmpty");
                empty_el.innerText = this.empty_text;
                parent_el.appendChild(empty_el);
            }
        }

        async init() {
            await this.fetchTree({
                parent_el: this.el,
                parent_ref_id: this.container_ref_id
            });
        }
    }

    for (const el of document.querySelectorAll(".SrContainerObjectTree")) {
        const config = JSON.parse(atob(el.dataset.srcontainerobjecttree));
        config.el = el;

        const tree = new SrContainerObjectTree(config);
        tree.init();
    }
});
