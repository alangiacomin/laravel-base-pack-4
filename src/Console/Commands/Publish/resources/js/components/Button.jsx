const Button = ({title, action}) => {
    const onClick = () => {
        action && action();
    }
    return (<button className={"btn btn-info"} onClick={onClick}>{title}</button>);
}

export default Button;
