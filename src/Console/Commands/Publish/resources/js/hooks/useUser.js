import {useCallback, useContext, useMemo} from "react";
import AuthContext from "../AuthContext";

const useUser = () => {
    const context = useContext(AuthContext);

    const user = useMemo(() => ({
        ...(context.user === '' ? {} : context.user),
        hasPerm: (perm) => !perm || (context.user && context.user.assigned_perms && context.user.assigned_perms.includes(perm)),
    }), [context.user]);

    const setUser = useCallback((user) => context.setUser(user), [context]);

    return {
        user,
        setUser,
    };
};

export default useUser;
