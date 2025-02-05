import {useEffect, useState} from "react";
import useUser from "./useUser";

const useEvent = (eventName, callback) => {
    const [channel, setChannel] = useState(null);
    const {user} = useUser();

    useEffect(() => {
        if (user && user.id) {
            setChannel(window.Echo.private(`App.Models.User.User.${user.id ?? 0}`));
        }
    }, [user]);

    useEffect(() => {
        if (channel) {
            channel.listen(eventName, callback);
        }
        return () => {
            if (channel) {
                channel.stopListening(eventName);
            }
        }
    }, [callback, channel, eventName]);
};

export default useEvent;
