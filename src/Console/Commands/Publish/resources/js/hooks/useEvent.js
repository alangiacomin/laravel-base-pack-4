import {useEffect, useState} from "react";
import useUser from "./useUser";

const useEvent = (eventName, callback) => {
    const [chInstance, setChInstance] = useState(null);
    const [chName, setChName] = useState('');
    const {user} = useUser();

    useEffect(() => {
        if (user && user.id > 0) {
            setChName(`App.Models.User.User.${user.id}`);
        }
    }, [user]);

    useEffect(() => {
        if (chName) {
            // noinspection JSUnresolvedReference
            setChInstance(window.Echo.private(chName));
        }
        return () => {
            if (chName) {
                // noinspection JSUnresolvedReference
                window.Echo.leaveChannel(chName);
            }
        }
    }, [chName]);

    useEffect(() => {
        if (chInstance) {
            chInstance.listen(eventName, callback);
        }
        return () => {
            if (chInstance) {
                chInstance.stopListening(eventName);
            }
        }
    }, [callback, chInstance, eventName]);
};

export default useEvent;
