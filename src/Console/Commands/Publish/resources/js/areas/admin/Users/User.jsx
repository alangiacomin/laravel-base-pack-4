import {useCallback, useState} from "react";
import {userRemoveRole} from "../../../apis/apiUser";
import useUser from "../../../hooks/useUser";
import {RoleEnum} from "../../../enums/RoleEnum";

const User = ({user}) => {
    const [roles, setRoles] = useState(user.assigned_roles);
    const {user: currentUser} = useUser();

    const removeRole = useCallback((role) => {
        userRemoveRole({modelId: user.id, role: role})
            .then((resp) => {
                if (resp.success) {
                    setRoles(resp.result.assigned_roles);
                }
            });
        return false;
    }, [user.id]);

    return (
        <li>
            {user.name}
            <ul>
                {roles.map((role) => (
                    <li key={role}>
                        {role}
                        {(currentUser.id !== user.id || role !== RoleEnum.ADMIN) && (
                            <button type="button" className="btn btn-link" onClick={() => removeRole(role)}>X</button>)}
                    </li>
                ))}
            </ul>
        </li>
    );
}

export default User;
