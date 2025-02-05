import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import env from "../env";
import {useEffect} from "react";

const useEcho = () => {
    useEffect(() => {
        window.Pusher = Pusher;

        window.Echo = new Echo({
            broadcaster: 'reverb',
            key: env.REVERB_APP_KEY,
            wsHost: env.REVERB_HOST,
            wsPort: env.REVERB_PORT ?? 80,
            wssPort: env.REVERB_PORT ?? 443,
            forceTLS: (env.REVERB_SCHEME ?? 'https') === 'https',
            enabledTransports: ['ws', 'wss'],
        });
    }, []);
}

export default useEcho;
