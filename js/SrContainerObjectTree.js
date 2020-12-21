document.addEventListener("DOMContentLoaded", () => {
    class SrContainerObjectTree {
        constructor({edit_user_settings_error_text, edit_user_settings_fetch_url, edit_user_settings_form_el, edit_user_settings_icon_el, tree_container_ref_id, tree_el, tree_empty_text, tree_error_text, tree_fetch_url}) {
            this.edit_user_settings_error_text = edit_user_settings_error_text;
            this.edit_user_settings_fetch_url = edit_user_settings_fetch_url;
            this.edit_user_settings_form_el = edit_user_settings_form_el;
            this.edit_user_settings_icon_el = edit_user_settings_icon_el;
            this.tree_container_ref_id = tree_container_ref_id;
            this.tree_el = tree_el;
            this.tree_empty_text = tree_empty_text;
            this.tree_error_text = tree_error_text;
            this.tree_fetch_url = tree_fetch_url;
            this._handleCloseUserSettingsIconClose = null;
            this._handleEscCloseUserSettingsIcon = null;
        }

        clearElement({el}) {
            el.innerHTML = "";
        }

        async clickNode({arrow_el, children_el, preloaded_children, ref_id}) {
            arrow_el.classList.toggle("SrContainerObjectTreeArrowOpen");

            if (children_el.children.length === 0) {
                await this.fetchTree({
                    parent_el: children_el,
                    parent_ref_id: ref_id,
                    preloaded_children
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

        async fetchEditUserSettings({parent_el}) {
            const loading_el = this.insertLoading({parent_el});

            let result;
            try {
                result = await (await fetch(this.edit_user_settings_fetch_url, {
                    headers: {
                        "accept": "application/json"
                    }
                })).json();
            } catch (err) {
                this.insertError({
                    err,
                    error_text: this.edit_user_settings_error_text,
                    parent_el
                });

                return;
            } finally {
                this.removeLoading({loading_el, parent_el});
            }

            const {html} = result;

            this.initEditUserSettingsForm({html, parent_el});
        }

        async fetchTree({parent_el, parent_ref_id, preloaded_children}) {
            let result;

            if (preloaded_children) {
                result = preloaded_children;
            } else {
                const loading_el = this.insertLoading({parent_el});
                try {
                    result = await (await fetch(this.tree_fetch_url.replace(":parent_ref_id", parent_ref_id), {
                        headers: {
                            "accept": "application/json"
                        }
                    })).json();
                } catch (err) {
                    this.insertError({
                        err,
                        error_text: this.tree_error_text,
                        parent_el
                    });

                    return;
                } finally {
                    this.removeLoading({loading_el, parent_el});
                }
            }

            const {children} = result;

            if (children.length > 0) {
                for (const {count_sub_children_types, description, icon, is_container, link, link_new_tab, preloaded_children, pre_open, ref_id, title} of children) {
                    const node_el = document.createElement("div");
                    node_el.classList.add("SrContainerObjectTreeNode");

                    const children_el = document.createElement("div");
                    children_el.classList.add("SrContainerObjectTreeChildren");

                    const link_el = document.createElement(link ? "a" : "div");
                    link_el.classList.add("SrContainerObjectTreeLink");
                    if (link) {
                        link_el.href = link;
                        if (link_new_tab) {
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
                            children_el,
                            preloaded_children,
                            ref_id
                        });

                        arrow_el.addEventListener("click", clickNode);
                        if (!link) {
                            link_el.addEventListener("click", clickNode);
                        }

                        node_el.appendChild(arrow_el);

                        if (pre_open) {
                            clickNode();
                        }
                    }

                    node_el.appendChild(link_el);

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

                    if (is_container) {
                        node_el.appendChild(children_el);
                    }

                    parent_el.appendChild(node_el);
                }
            } else {
                const empty_el = document.createElement("div");
                empty_el.classList.add("SrContainerObjectTreeEmpty");
                empty_el.innerText = this.tree_empty_text;
                parent_el.appendChild(empty_el);
            }
        }

        init() {
            this.initTree();

            this.initEditUserSettings();
        }

        async initTree() {
            this.clearElement({el: this.tree_el});

            await this.fetchTree({
                parent_el: this.tree_el,
                parent_ref_id: this.tree_container_ref_id
            });
        }

        async initEditUserSettings() {
            this.edit_user_settings_icon_el.addEventListener("click", this.handleOpenCloseUserSettingsIcon.bind(this, {}));

            for (const el of [this.edit_user_settings_form_el, this.edit_user_settings_icon_el]) {
                el.addEventListener("click", e => {
                    e.stopPropagation();
                });
            }

            await this.fetchEditUserSettings({
                parent_el: this.edit_user_settings_form_el
            });
        }

        initEditUserSettingsForm({html, parent_el}) {
            parent_el.insertAdjacentHTML("beforeend", html);

            const form = parent_el.querySelector("form");

            form.addEventListener("change", this.updateEditUserSettings.bind(this, {
                form,
                parent_el
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

        async updateEditUserSettings({form, parent_el}) {
            const data = new FormData(form);

            for (const el of form.querySelectorAll("button,input,select,textarea")) {
                el.disabled = true;
            }

            const loading_el = this.insertLoading({parent_el});

            let result;
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
                    parent_el
                });

                return;
            } finally {
                this.removeLoading({loading_el, parent_el});
            }

            this.clearElement({el: parent_el});

            const {html, ok} = result;

            this.initEditUserSettingsForm({html, parent_el});

            if (ok) {
                await this.initTree();
            }
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
