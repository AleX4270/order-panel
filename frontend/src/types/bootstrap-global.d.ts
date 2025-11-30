export {};

declare global {
  interface Window {
    bootstrap: {
      Modal: new (
        element: Element,
        options?: { backdrop?: boolean | 'static'; keyboard?: boolean; focus?: boolean }
      ) => BootstrapModalInstance;
    };
  }

  interface BootstrapModalInstance {
    show(): void;
    hide(): void;
    toggle(): void;
    dispose(): void;
  }
}