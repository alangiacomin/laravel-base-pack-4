import api from "./api";

const userLogin = async (data) => {
    return api.post("user/login", data);
}

const userLogout = async () => {
    return api.post("user/logout");
}

const userLoad = async () => {
    return api.get("user/loadUser");
}

const userAll = async () => {
    return api.get("user/all");
}

const userRemoveRole = async (data) => {
    return api.post("user/removeRole", data);
}

export {
    userLogin,
    userLogout,
    userLoad,
    userAll,
    userRemoveRole,
};
