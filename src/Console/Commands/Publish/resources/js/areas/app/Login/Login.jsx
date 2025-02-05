import './Login.scss';
import useUser from "../../../hooks/useUser";
import {useLocation, useNavigate} from "react-router-dom";
import {useEffect, useState} from "react";
import {userLogin} from "../../../apis/apiUser";

const Login = ()=>{
    const {setUser, user} = useUser();
    const [errors, setErrors] = useState({});
    const navigate = useNavigate();
    const location = useLocation();

    const submitLogin = (evt)=>{
        evt.preventDefault();
        evt.stopPropagation();
        const email = evt.target.exampleInputEmail1.value;
        const password = evt.target.exampleInputPassword1.value;
        loginUser(email, password);
        return false;
    }
    const submitLogin2 = (evt)=>{
        evt.preventDefault();
        evt.stopPropagation();
        const email = evt.target.exampleInputEmail2.value;
        const password = evt.target.exampleInputPassword2.value;
        loginUser(email, password);
        return false;
    }

    const loginUser = (email, password) => {
        return userLogin(
            {
                email,
                password,
            })
            .then((response) => {
                setUser(response);
            })
            .catch((response)=>{
                setErrors({
                    ...response.response.data.errors,
                    submit: !!response.response.data.errors || "Login failed",
                });
            });
    }

    useEffect(() => {
        if(user && user.id)
        {
            navigate(location.state?.from?.pathname || '/');
        }
    }, [ location, navigate, user]);

    return (
        <div className="login">
            <form onSubmit={submitLogin}>
                <div className="mb-3">
                    <label htmlFor="exampleInputEmail1" className="form-label">Email address</label>
                    <input type="text" className="form-control" id="exampleInputEmail1" defaultValue={"test@example.com"}/>
                    {errors && errors.email && errors.email.map((error) => error)}
                </div>
                <div className="mb-3">
                    <label htmlFor="exampleInputPassword1" className="form-label">Password</label>
                    <input type="password" className="form-control" id="exampleInputPassword1" defaultValue={"rew453!rew453!"}/>
                    {errors && errors.password && errors.password.map((error) => error)}
                </div>
                <button type="submit" className="btn btn-primary">Submit</button>
                {errors && errors.submit && (<><br/>{errors.submit}</>)}
            </form>
            <form onSubmit={submitLogin2}>
                <div className="mb-3">
                    <label htmlFor="exampleInputEmail2" className="form-label">Email address</label>
                    <input type="text" className="form-control" id="exampleInputEmail2" defaultValue={"admin@admin.com"}/>
                    {errors && errors.email && errors.email.map((error) => error)}
                </div>
                <div className="mb-3">
                    <label htmlFor="exampleInputPassword2" className="form-label">Password</label>
                    <input type="password" className="form-control" id="exampleInputPassword2" defaultValue={"rew453!rew453!"}/>
                    {errors && errors.password && errors.password.map((error) => error)}
                </div>
                <button type="submit" className="btn btn-primary">Submit</button>
                {errors && errors.submit && (<><br/>{errors.submit}</>)}
            </form>
        </div>
    )
}

export default Login;
