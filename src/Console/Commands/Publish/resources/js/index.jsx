import {createRoot} from 'react-dom/client';
import Bootstrap from "./Bootstrap";

// Render your React component instead
const root = createRoot(document.getElementById('app'));
root.render(<Bootstrap/>);
