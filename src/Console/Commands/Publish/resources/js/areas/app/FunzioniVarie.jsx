import useUser from "../../hooks/useUser";
import {useCallback, useMemo} from "react";
import {useNavigate} from "react-router-dom";
import {userLogin, userLogout} from "../../apis/apiUser";
import api from "../../apis/api";
import useEvent from "../../hooks/useEvent";

const FunzioniVarie = () => {
    const {user, setUser} = useUser();
    const navigate = useNavigate();
    const title = "questa è la pagina standard";
    const isLogged = useMemo(() => user && user.id, [user]);

    useEvent('AzioneFatta', (e) => {
        console.log(">> event", e);
    });
    useEvent('Nested\\SecondaAzione', (e) => {
        console.log(">> event", e);
    });

    const loginUser = useCallback((isAdmin = false) => {
        return userLogin({
            'email': isAdmin ? 'admin@admin.com' : 'test@example.com',
            'password': 'rew453!rew453!',
        })
            .then((data) => {
                setUser(data);
            });
    }, [setUser]);

    const logoutUser = useCallback(() => {
        return userLogout()
            .then((data) => {
                setUser(data);
            });
    }, [setUser]);

    const eseguiAzione = useCallback(() => {
        api.get("azione/esegui");
    }, []);

    return (
        <>
            <h1>{title}</h1>
            <p>User: {
                (isLogged)
                    ? (<span>{user.name} ({user.email})</span>)
                    : null}
            </p>
            {user && isLogged
                ? (
                    <>
                        <button className="btn btn-light" onClick={logoutUser}>Logout</button>
                        {user.hasPerm('admin_view') && (
                            <button className="btn btn-info" onClick={() => navigate('/admin')}>Admin</button>
                        )}
                    </>
                )
                : (
                    <>
                        <button className="btn btn-light" onClick={() => navigate('/login')}>Login</button>
                        <button className="btn btn-light" onClick={() => loginUser(true)}>Login (admin)</button>
                        <button className="btn btn-light" onClick={() => loginUser()}>Login (test)</button>
                    </>
                )}
            <hr/>
            <button className="btn btn-light" onClick={() => navigate('/login')}>Login</button>
            <button className="btn btn-light" onClick={() => loginUser(true)}>Login (admin)</button>
            <button className="btn btn-light" onClick={() => loginUser()}>Login (test)</button>
            <button className="btn btn-light" onClick={logoutUser}>Logout</button>
            <button className="btn btn-info" onClick={() => navigate('/admin')}>Admin</button>
            <button className="btn btn-info" onClick={() => navigate('/admin/prima')}>Admin/Prima</button>
            <hr/>
            <button className="btn btn-primary" onClick={() => eseguiAzione()}>Esegui azione</button>
        </>
    );
}

export default FunzioniVarie;
