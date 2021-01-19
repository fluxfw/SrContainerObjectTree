document.addEventListener("DOMContentLoaded", () => {
    class SrContainerObjectTree {
        static get STORAGE() {
            return sessionStorage;
        }

        static get STORAGE_CACHE_KEY() {
            return "SrContainerObjectTreeCache";
        }

        constructor({edit_user_settings_el, edit_user_settings_form_container_el, edit_user_settings_icon_el, edit_user_settings_update_url, plugin_version, texts: {edit_user_settings_deep_x, edit_user_settings_hide_metadata, edit_user_settings_save_error, edit_user_settings_show_metadata, tree_apply, tree_empty, tree_fetch_error, tree_loaded_from_cache, tree_has_changed_meanwhile}, tree_el, tree_fetch_url, tree_link_container_objects, tree_link_new_tab, tree_show_metadata}) {
            this.edit_user_settings_el = edit_user_settings_el;
            this.edit_user_settings_form_container_el = edit_user_settings_form_container_el;
            this.edit_user_settings_icon_el = edit_user_settings_icon_el;
            this.edit_user_settings_update_url = edit_user_settings_update_url;
            this.plugin_version = plugin_version;
            this.texts = {
                edit_user_settings_deep_x,
                edit_user_settings_hide_metadata,
                edit_user_settings_save_error,
                edit_user_settings_show_metadata,
                tree_apply,
                tree_empty,
                tree_fetch_error,
                tree_loaded_from_cache,
                tree_has_changed_meanwhile
            };
            this.tree_el = tree_el;
            this.tree_fetch_url = tree_fetch_url;
            this.tree_link_container_objects = tree_link_container_objects;
            this.tree_link_new_tab = tree_link_new_tab;
            this.tree_show_metadata = tree_show_metadata;
            this._cache_el = null;
            this._edit_user_settings_form_el = null;
            this._edit_user_settings_show_metadata_el = null;
            this._edit_user_settings_start_deep_el = null;
            this._handleCloseUserSettingsIconClose = null;
            this._handleEscCloseUserSettingsIcon = null;
            this._is_load_from_cache = false;
            this._tree_data = {
                fetched_data: {
                    tree_children: null,
                    tree_min_deep: null,
                    tree_max_deep: null,
                    tree_start_deep: null
                },
                fetched_time: null,
                fetched_with_plugin_version: null
            };
        }

        applyTreeData({tree_data}) {
            this._tree_data = tree_data;

            this._edit_user_settings_start_deep_el.innerHTML = "";
            for (let deep = this._tree_data.fetched_data.tree_min_deep; deep <= this._tree_data.fetched_data.tree_max_deep; deep++) {
                const deep_option = document.createElement("option");
                deep_option.value = deep;
                deep_option.text = this.texts.edit_user_settings_deep_x.replace("_deep_", deep);
                this._edit_user_settings_start_deep_el.appendChild(deep_option);
            }
            this._edit_user_settings_start_deep_el.size = this._edit_user_settings_start_deep_el.options.length;
            this._edit_user_settings_start_deep_el.value = this._tree_data.fetched_data.tree_start_deep;
        }

        async backgroundFetchTree() {
            const tree_data = await this.fetchTree({parent_el: null});

            if (!tree_data) {
                return;
            }

            if (JSON.stringify(tree_data.fetched_data) !== JSON.stringify(this._tree_data.fetched_data)) {
                this._cache_el.innerText += `\n\n${this.texts.tree_has_changed_meanwhile}\n`;

                const apply_button = document.createElement("button");
                apply_button.type = "button";
                apply_button.classList.add("btn", "btn-default");
                apply_button.innerText = this.texts.tree_apply;
                apply_button.addEventListener("click", this.clickBackgroundFetchTreeApply.bind(this, {tree_data}));
                this._cache_el.appendChild(apply_button);
            }
        }

        async clickBackgroundFetchTreeApply({tree_data}) {
            this.applyTreeData({tree_data});

            await this.initTree();
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

        async fetchTree({parent_el}) {
            let fetched_data;

            const loading_el = this.insertLoading({parent_el});
            try {
                fetched_data = await (await fetch(this.tree_fetch_url, {
                    headers: {
                        "accept": "application/json"
                    }
                })).json();
            } catch (err) {
                this.insertError({
                    err,
                    error_text: this.texts.tree_fetch_error,
                    parent_el
                });

                return;
            } finally {
                if (parent_el) {
                    parent_el.removeChild(loading_el);
                }
            }

            const tree_data = {
                fetched_data,
                fetched_time: Date.now(),
                fetched_with_plugin_version: this.plugin_version
            }

            this.constructor.STORAGE[this.constructor.STORAGE_CACHE_KEY] = JSON.stringify(tree_data);

            return tree_data;
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
                this._handleEscCloseUserSettingsIcon = e => {
                    if (e.key === "Escape") {
                        this.handleCloseUserSettingsIconClose();
                    }
                };
            }

            return this._handleEscCloseUserSettingsIcon;
        }

        async init() {
            await this.initEditUserSettings();

            await this.initTree();
        }

        async initNode({children, parent_deep, parent_el}) {
            for (const {children: children_children, count_sub_children_types, description, icon, is_container, link, title} of children) {
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
                        children: children_children,
                        children_el,
                        parent_deep: parent_deep + 1
                    });

                    arrow_el.addEventListener("click", clickNode);
                    if (!this.tree_link_container_objects) {
                        link_el.addEventListener("click", clickNode);
                    }

                    node_el.appendChild(arrow_el);

                    if (parent_deep < this._tree_data.fetched_data.tree_start_deep) {
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
            this.edit_user_settings_el.classList.remove("SrContainerObjectTreeEditUserSettingsShow");

            this.tree_el.innerHTML = "";

            let background_fetch_tree = false;
            if (!this._tree_data.fetched_data.tree_children) {
                let cache = this.constructor.STORAGE[this.constructor.STORAGE_CACHE_KEY];
                if (cache) {
                    try {
                        cache = JSON.parse(cache);
                    } catch (err) {
                        this.insertError({err});
                        cache = null;
                    }
                }
                if (cache) {
                    if (cache.fetched_with_plugin_version !== this.plugin_version) {
                        cache = null;
                    }
                }

                let tree_data;
                if (cache) {
                    tree_data = cache;

                    this._is_load_from_cache = true;
                    background_fetch_tree = true;
                } else {
                    tree_data = await this.fetchTree({parent_el: this.tree_el});

                    if (!tree_data) {
                        return;
                    }

                    this._is_load_from_cache = false;
                }

                this.applyTreeData({tree_data});
            }

            if (this._tree_data.fetched_data.tree_children.length > 0) {
                await this.initNode({
                    children: this._tree_data.fetched_data.tree_children,
                    parent_deep: 1,
                    parent_el: this.tree_el
                });
            } else {
                const empty_el = document.createElement("div");
                empty_el.classList.add("SrContainerObjectTreeEmpty");
                empty_el.innerText = this.texts.tree_empty;
                this.tree_el.appendChild(empty_el);
            }

            if (this._is_load_from_cache) {
                if (!this._cache_el) {
                    this._cache_el = document.createElement("div");
                    this._cache_el.classList.add("SrContainerObjectTreeCache");
                    this.edit_user_settings_el.appendChild(this._cache_el);
                }
                this._cache_el.innerText = this.texts.tree_loaded_from_cache.replace("_date_", new Date(this._tree_data.fetched_time).toLocaleString());

                if (background_fetch_tree) {
                    this.backgroundFetchTree();
                }
            }

            this.edit_user_settings_el.classList.add("SrContainerObjectTreeEditUserSettingsShow");
        }

        async initEditUserSettings() {
            this._edit_user_settings_form_el = document.createElement("form");

            const edit_user_settings_show_metadata_show_el = document.createElement("option");
            edit_user_settings_show_metadata_show_el.value = "show";
            edit_user_settings_show_metadata_show_el.text = this.texts.edit_user_settings_show_metadata;

            const edit_user_settings_show_metadata_hide_el = document.createElement("option");
            edit_user_settings_show_metadata_hide_el.value = "hide";
            edit_user_settings_show_metadata_hide_el.text = this.texts.edit_user_settings_hide_metadata;

            this._edit_user_settings_show_metadata_el = document.createElement("select");
            this._edit_user_settings_show_metadata_el.appendChild(edit_user_settings_show_metadata_show_el);
            this._edit_user_settings_show_metadata_el.appendChild(edit_user_settings_show_metadata_hide_el);
            this._edit_user_settings_show_metadata_el.size = this._edit_user_settings_show_metadata_el.options.length;
            this._edit_user_settings_show_metadata_el.value = (this.tree_show_metadata ? "show" : "hide");
            this._edit_user_settings_form_el.appendChild(this._edit_user_settings_show_metadata_el);

            this._edit_user_settings_start_deep_el = document.createElement("select");
            this._edit_user_settings_form_el.appendChild(this._edit_user_settings_start_deep_el);

            this.edit_user_settings_form_container_el.appendChild(this._edit_user_settings_form_el);

            this.edit_user_settings_icon_el.addEventListener("click", this.handleOpenCloseUserSettingsIcon.bind(this, {}));

            for (const el of [this.edit_user_settings_form_container_el, this.edit_user_settings_icon_el]) {
                el.addEventListener("click", e => {
                    e.stopPropagation();
                });
            }

            this._edit_user_settings_form_el.addEventListener("change", this.updateEditUserSettings.bind(this));
        }

        insertError({err, error_text, parent_el}) {
            console.error(err);

            if (parent_el) {
                const error_el = document.createElement("div");
                error_el.classList.add("SrContainerObjectTreeError");
                error_el.innerText = error_text;
                parent_el.appendChild(error_el);
                return error_el;
            }
        }

        insertLoading({parent_el}) {
            if (parent_el) {
                const loading_el = document.createElement("div");
                loading_el.classList.add("SrContainerObjectTreeLoading");
                parent_el.appendChild(loading_el);
                return loading_el;
            }
        }

        async updateEditUserSettings() {
            for (const el of this._edit_user_settings_form_el.elements) {
                el.disabled = true;
            }

            let result;

            const loading_el = this.insertLoading({parent_el: this.edit_user_settings_form_container_el});
            try {
                result = await (await fetch(this.edit_user_settings_update_url, {
                    body: JSON.stringify({
                        "show_metadata": (this._edit_user_settings_show_metadata_el.value === "show"),
                        "start_deep": this._edit_user_settings_start_deep_el.value
                    }),
                    headers: {
                        "accept": "application/json",
                        "content-type": "application/json"
                    },
                    method: "post"
                })).json();
            } catch (err) {
                this.insertError({
                    err,
                    error_text: this.texts.edit_user_settings_save_error,
                    parent_el: this.edit_user_settings_form_container_el
                });

                return;
            } finally {
                this.edit_user_settings_form_container_el.removeChild(loading_el);
            }

            for (const el of this._edit_user_settings_form_el.elements) {
                el.disabled = false;
            }

            const {show_metadata, start_deep} = result;
            this.tree_show_metadata = show_metadata;
            this._edit_user_settings_show_metadata_el.value = (this.tree_show_metadata ? "show" : "hide");
            this._tree_data.fetched_data.tree_start_deep = this._edit_user_settings_start_deep_el.value = start_deep;

            await this.initTree();
        }
    }

    for (const el of document.querySelectorAll(".SrContainerObjectTree")) {
        const config = JSON.parse(atob(el.dataset.config));

        config.edit_user_settings_el = el.querySelector(".SrContainerObjectTreeEditUserSettings");
        config.edit_user_settings_form_container_el = config.edit_user_settings_el.querySelector(".SrContainerObjectTreeEditUserSettingsFormContainer");
        config.edit_user_settings_icon_el = config.edit_user_settings_el.querySelector(".SrContainerObjectTreeEditUserSettingsIcon");

        config.tree_el = el.querySelector(".SrContainerObjectTreeTree");

        const tree = new SrContainerObjectTree(config);
        tree.init();
    }
});
