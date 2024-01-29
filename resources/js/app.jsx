import '../styles/base.css';

import { createInertiaApp } from "@inertiajs/react";
import { createRoot } from "react-dom/client"

const APP_NAME = import.meta.env.APP_NAME ?? 'FrameWire'

createInertiaApp({
    title: title => `${title} | ${APP_NAME}`,
    resolve: name => {
        const pages = import.meta.glob('./Pages/**/*.jsx')
        return  pages[`./Pages/${name}.jsx`]()
    },
    setup({ el, App, props }) {
        createRoot(el).render(<App {...props} />)
    },
})
