import {Navigate, useLocation} from "react-router-dom";
import useUser from "./hooks/useUser";
import PropTypes from "prop-types";

const ProtectedContent = ({perm, children}) =>{
    const location = useLocation();
    const {user} = useUser();

    if (user.hasPerm(perm)) {
        return (<>{children}</>);
    }

    if (!user.id) {
        return (<Navigate to='/login' replace state={{from: location}}/>);
    }

    return (<h1>NON AUTORIZZATO</h1>);
}

ProtectedContent.propTypes = {
    perm: PropTypes.string.isRequired,
    children: PropTypes.node.isRequired,
}

export default ProtectedContent;
