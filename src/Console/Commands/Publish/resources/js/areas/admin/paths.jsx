import Layout from "./Layout";
import ProtectedContent from "../../ProtectedContent";
import Users from "./Users/Users";
import {PermissionEnum} from "../../enums/PermissionEnum";

const Prima = () => {
    return (<ProtectedContent perm={PermissionEnum.ADMIN_PRIMA}>
        <div>prima</div>
    </ProtectedContent>);
}

const paths = {
    path: "/admin",
    element: <Layout/>,
    children: [
        {
            path: "",
            element: <div>root</div>,
        },
        {
            path: "prima",
            element: <Prima/>,
            loader: async () => {
                return "admin_view";
                return ({'perm': 'provaPermesso'});
            },
        },
        {
            path: "seconda",
            element: <div>seconda</div>,
        },
        {
            path: "Users",
            element: <Users/>,
        },
        {
            path: "*",
            element: <div>boh</div>,
        },
    ],
};

export {
    paths
};
