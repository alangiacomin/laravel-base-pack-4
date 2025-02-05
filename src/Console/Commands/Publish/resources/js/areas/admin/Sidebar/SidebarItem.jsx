import {Link, useMatch, useResolvedPath} from "react-router-dom";
import classNames from "classnames";
import PropTypes from "prop-types";

const SidebarItem = ({href, title}) => {
    const isActive = useMatch({path: useResolvedPath(href).pathname, end: true});

    const linkClassname = classNames(
        'nav-link',
        'text-white',
        {'active': isActive},
    );
    return (
        <li className="nav-item">
            <Link to={href} className={linkClassname}>{title}</Link>
        </li>
    );
}

SidebarItem.propTypes = {
    href: PropTypes.string.isRequired,
    title: PropTypes.string.isRequired,
}

export default SidebarItem;
