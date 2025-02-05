import FunzioniVarie from "./FunzioniVarie";
import Login from "./Login/Login";

const paths = {
    path: "/",
    children: [
        {
            path: "",
            element: <FunzioniVarie/>,
        },
        {
            path: "login",
            element: <Login/>,
        },
        {
            path: "*",
            element: <div>404</div>,
        },
    ],
};

export {
    paths
};
