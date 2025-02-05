import {Outlet} from "react-router-dom";
import useUser from "../../hooks/useUser";

import Sidebar from "./Sidebar/Sidebar";
import ProtectedContent from "../../ProtectedContent";
import {PermissionEnum} from "../../enums/PermissionEnum";

const Layout = () => {
    const {user} = useUser();

    return (
        <ProtectedContent perm={PermissionEnum.ADMIN_VIEW}>
            <div className="d-flex flex-row">
                <Sidebar/>
                <div style={{'width': '100vh'}}>
                    <div style={{'float': "right"}}>
                        <span className="p-3">{user && user.name}</span>
                    </div>
                    <div style={{'clear': "both"}}/>
                    <hr/>
                    <Outlet/>
                </div>
            </div>
        </ProtectedContent>
    )
}

export default Layout;
