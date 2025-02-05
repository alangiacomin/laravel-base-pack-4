import './Sidebar.scss';
import SidebarItem from "./SidebarItem";
import {Link} from "react-router-dom";
import useUser from "../../../hooks/useUser";
import {PermissionEnum} from "../../../enums/PermissionEnum";

const Sidebar = () => {
    const {user} = useUser();
    return (
        <>
            <div className="sidebar p-3 bg-dark text-white flex-column">
                <span className={"fs-4"}>Sidebar</span>
                <hr/>
                <ul className="nav nav-pills mb-auto flex-column">
                    <SidebarItem href="/admin" title="Home"/>
                    {user.hasPerm(PermissionEnum.ADMIN_PRIMA) && (<SidebarItem href="/admin/prima" title="Prima"/>)}
                    <SidebarItem href="/admin/seconda" title="Seconda"/>
                    {user.hasPerm(PermissionEnum.ADMIN_USERS) && (<SidebarItem href="/admin/users" title="Users"/>)}
                    <SidebarItem href="/admin/hdgjfkdgd" title="Non esiste"/>
                </ul>
                <div>
                    <Link to="/">back</Link>
                </div>
            </div>
        </>
    );
}

export default Sidebar;
