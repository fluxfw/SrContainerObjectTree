document.addEventListener("DOMContentLoaded", () => {
    class SrContainerObjectTree {
        constructor({edit_user_settings_error_text, edit_user_settings_form_el, edit_user_settings_icon_el, tree_children, tree_el, tree_empty_text, tree_fetch_url, tree_link_container_objects, tree_link_new_tab, tree_show_metadata, tree_start_deep}) {
            this.edit_user_settings_error_text = edit_user_settings_error_text;
            this.edit_user_settings_form_el = edit_user_settings_form_el;
            this.edit_user_settings_icon_el = edit_user_settings_icon_el;
            this.tree_children = tree_children;
            this.tree_el = tree_el;
            this.tree_empty_text = tree_empty_text;
            this.tree_fetch_url = tree_fetch_url;
            this.tree_link_container_objects = tree_link_container_objects;
            this.tree_link_new_tab = tree_link_new_tab;
            this.tree_show_metadata = tree_show_metadata;
            this.tree_start_deep = tree_start_deep;
            this._handleCloseUserSettingsIconClose = null;
            this._handleEscCloseUserSettingsIcon = null;
        }

        clearElement({el}) {
            el.innerHTML = "";
        }

        async clickNode({arrow_el, children, children_el, parent_deep}) {
            arrow_el.classList.toggle("SrContainerObjectTreeArrowOpen");

            if (children_el.children.length === 0) {
                await this.initNode({
                    children,
                    parent_deep,
                    parent_el: children_el
                });
            }
        }

        async handleOpenCloseUserSettingsIcon({forceOpenStatus}) {
            this.edit_user_settings_icon_el.classList.toggle("SrContainerObjectTreeEditUserSettingsIconOpen", forceOpenStatus);

            if (this.edit_user_settings_icon_el.classList.contains("SrContainerObjectTreeEditUserSettingsIconOpen")) {
                document.addEventListener("keydown", this.handleEscCloseUserSettingsIcon);
                document.body.addEventListener("click", this.handleCloseUserSettingsIconClose);
            } else {
                document.removeEventListener("keydown", this.handleEscCloseUserSettingsIcon);
                document.body.removeEventListener("click", this.handleCloseUserSettingsIconClose);
            }
        }

        get handleCloseUserSettingsIconClose() {
            if (!this._handleCloseUserSettingsIconClose) {
                this._handleCloseUserSettingsIconClose = this.handleOpenCloseUserSettingsIcon.bind(this, {
                    forceOpenStatus: false
                });
            }

            return this._handleCloseUserSettingsIconClose;
        }

        get handleEscCloseUserSettingsIcon() {
            if (!this._handleEscCloseUserSettingsIcon) {
                this._handleEscCloseUserSettingsIcon = (e) => {
                    if (e.key === "Escape") {
                        this.handleCloseUserSettingsIconClose();
                    }
                };
            }

            return this._handleEscCloseUserSettingsIcon;
        }

        init() {
            this.initTree();

            this.initEditUserSettings();
        }

        async initNode({children, parent_deep, parent_el}) {
            for (const {count_sub_children_types, description, icon, is_container, link, preloaded_children, title} of children) {
                const node_el = document.createElement("div");
                node_el.classList.add("SrContainerObjectTreeNode");

                const children_el = document.createElement("div");
                children_el.classList.add("SrContainerObjectTreeChildren");

                const link_el = document.createElement(link ? "a" : "div");
                link_el.classList.add("SrContainerObjectTreeLink");
                if (!is_container || this.tree_link_container_objects) {
                    link_el.href = link;
                    if (this.tree_link_new_tab) {
                        link_el.target = "_blank";
                    }
                }

                const icon_el = document.createElement("img");
                icon_el.classList.add("SrContainerObjectTreeIcon");
                icon_el.src = icon;
                link_el.appendChild(icon_el);

                const title_el = document.createElement("div");
                title_el.classList.add("SrContainerObjectTreeTitle");
                title_el.innerText = title;
                link_el.appendChild(title_el);

                if (is_container) {
                    const arrow_el = document.createElement("div");
                    arrow_el.classList.add("SrContainerObjectTreeArrow");

                    const clickNode = this.clickNode.bind(this, {
                        arrow_el,
                        children: preloaded_children,
                        children_el,
                        parent_deep: parent_deep + 1
                    });

                    arrow_el.addEventListener("click", clickNode);
                    if (!this.tree_link_container_objects) {
                        link_el.addEventListener("click", clickNode);
                    }

                    node_el.appendChild(arrow_el);

                    if (parent_deep < this.tree_start_deep) {
                        clickNode();
                    }
                }

                node_el.appendChild(link_el);

                if (this.tree_show_metadata) {
                    if (description) {
                        const description_el = document.createElement("div");
                        description_el.classList.add("SrContainerObjectTreeDescription");
                        description_el.innerText = description;
                        node_el.appendChild(description_el);
                    }

                    if (is_container && count_sub_children_types.length > 0) {
                        const count_sub_children_types_el = document.createElement("div");
                        count_sub_children_types_el.classList.add("SrContainerObjectTreeCountSubChildrenTypes");
                        count_sub_children_types_el.innerText = count_sub_children_types.map(({count, type_title}) => `${count} ${type_title}`).join(", ");
                        node_el.appendChild(count_sub_children_types_el);
                    }
                }

                if (is_container) {
                    node_el.appendChild(children_el);
                }

                parent_el.appendChild(node_el);
            }
        }

        async initTree() {
            this.clearElement({el: this.tree_el});

            if (this.tree_children.length > 0) {
                await this.initNode({
                    children: this.tree_children,
                    parent_deep: 1,
                    parent_el: this.tree_el
                });
            } else {
                const empty_el = document.createElement("div");
                empty_el.classList.add("SrContainerObjectTreeEmpty");
                empty_el.innerText = this.tree_empty_text;
                this.tree_el.appendChild(empty_el);
            }
        }

        async initEditUserSettings() {
            this.edit_user_settings_icon_el.addEventListener("click", this.handleOpenCloseUserSettingsIcon.bind(this, {}));

            for (const el of [this.edit_user_settings_form_el, this.edit_user_settings_icon_el]) {
                el.addEventListener("click", e => {
                    e.stopPropagation();
                });
            }

            const form = this.edit_user_settings_form_el.querySelector("form");

            form.addEventListener("change", this.updateEditUserSettings.bind(this, {
                form
            }));
        }

        insertError({err, error_text, parent_el}) {
            console.error(err);

            const error_el = document.createElement("div");
            error_el.classList.add("SrContainerObjectTreeError");
            error_el.innerText = error_text;
            parent_el.appendChild(error_el);
            return error_el;
        }

        insertLoading({parent_el}) {
            const loading_el = document.createElement("div");
            loading_el.classList.add("SrContainerObjectTreeLoading");
            parent_el.appendChild(loading_el);
            return loading_el;
        }

        removeLoading({loading_el, parent_el}) {
            parent_el.removeChild(loading_el);
        }

        async updateEditUserSettings({form}) {
            const data = new FormData(form);

            for (const el of form.elements) {
                el.disabled = true;
            }

            let result;

            const loading_el = this.insertLoading({parent_el: this.edit_user_settings_form_el});
            try {
                result = await (await fetch(form.action, {
                    body: data,
                    headers: {
                        "accept": "application/json"
                    },
                    method: form.method
                })).json();
            } catch (err) {
                this.insertError({
                    err,
                    error_text: this.edit_user_settings_error_text,
                    parent_el: this.edit_user_settings_form_el
                });

                return;
            } finally {
                this.removeLoading({loading_el, parent_el: this.edit_user_settings_form_el});
            }

            for (const el of form.elements) {
                el.disabled = false;
            }

            const {show_metadata, start_deep} = result;
            this.tree_show_metadata = show_metadata;
            this.tree_start_deep = start_deep;

            await this.initTree();
        }
    }

    for (const el of document.querySelectorAll(".SrContainerObjectTree")) {
        const config = JSON.parse(atob(el.dataset.config));

        const user_settings_container = el.querySelector(".SrContainerObjectTreeEditUserSettings");
        config.edit_user_settings_form_el = user_settings_container.querySelector(".SrContainerObjectTreeEditUserSettingsForm");
        config.edit_user_settings_icon_el = user_settings_container.querySelector(".SrContainerObjectTreeEditUserSettingsIcon");

        config.tree_el = el.querySelector(".SrContainerObjectTreeTree");

        const tree = new SrContainerObjectTree(config);
        tree.init();
    }
});
