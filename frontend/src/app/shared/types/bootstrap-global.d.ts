interface BootstrapModal {
    show: () => void,
    hide: () => void,
    dispose: () => void,
}

interface Bootstrap {
    Modal: new (element: Element, options?: any) => BootstrapModal;
}

declare global {
    interface Window {
        bootstrap: Bootstrap;
    }
}