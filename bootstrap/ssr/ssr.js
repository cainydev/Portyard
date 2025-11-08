import { jsx } from "react/jsx-runtime";
import { createInertiaApp } from "@inertiajs/react";
import createServer from "@inertiajs/react/server";
import ReactDOMServer from "react-dom/server";
async function resolvePageComponent(path, pages) {
  for (const p of Array.isArray(path) ? path : [path]) {
    const page = pages[p];
    if (typeof page === "undefined") {
      continue;
    }
    return typeof page === "function" ? page() : page;
  }
  throw new Error(`Page not found: ${path}`);
}
const appName = "Portyard";
createServer(
  (page) => createInertiaApp({
    page,
    render: ReactDOMServer.renderToString,
    title: (title) => title ? `${title} - ${appName}` : appName,
    resolve: (name) => resolvePageComponent(
      `./pages/${name}.tsx`,
      /* @__PURE__ */ Object.assign({ "./pages/auth/confirm-password.tsx": () => import("./assets/confirm-password-D-efTpP0.js"), "./pages/auth/forgot-password.tsx": () => import("./assets/forgot-password-w9tsugRh.js"), "./pages/auth/login.tsx": () => import("./assets/login-CD5eq7Eu.js"), "./pages/auth/register.tsx": () => import("./assets/register-aVQt4t3D.js"), "./pages/auth/reset-password.tsx": () => import("./assets/reset-password-D0DPOiN2.js"), "./pages/auth/two-factor-challenge.tsx": () => import("./assets/two-factor-challenge-CyXetMXX.js"), "./pages/auth/verify-email.tsx": () => import("./assets/verify-email-Bz6w-2V5.js"), "./pages/dashboard.tsx": () => import("./assets/dashboard-zF38ar-d.js"), "./pages/settings/appearance.tsx": () => import("./assets/appearance-DO2OcARx.js"), "./pages/settings/password.tsx": () => import("./assets/password-BrpBNOXC.js"), "./pages/settings/profile.tsx": () => import("./assets/profile-BS37B0fG.js"), "./pages/settings/two-factor.tsx": () => import("./assets/two-factor-Bvhvy0qV.js"), "./pages/welcome.tsx": () => import("./assets/welcome-rcwnsbh6.js") })
    ),
    setup: ({ App, props }) => {
      return /* @__PURE__ */ jsx(App, { ...props });
    }
  })
);
