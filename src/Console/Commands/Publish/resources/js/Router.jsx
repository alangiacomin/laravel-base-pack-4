import {createBrowserRouter, RouterProvider} from "react-router-dom";
import {paths as appPaths} from "./areas/app/paths";
import {paths as adminPaths} from "./areas/admin/paths";

const futureFlags =  {
    v7_startTransition: true,
    v7_fetcherPersist: true,
    v7_normalizeFormMethod: true,
    v7_partialHydration: true,
    v7_relativeSplatPath: true,
    v7_skipActionErrorRevalidation: true,
};

const br = createBrowserRouter([
    appPaths,
    adminPaths,
], {future: futureFlags});

const Router = () => (<RouterProvider router={br} future={futureFlags}/>);

export default Router;
