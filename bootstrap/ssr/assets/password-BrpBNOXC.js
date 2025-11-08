import { jsxs, jsx, Fragment } from "react/jsx-runtime";
import { q as queryParams } from "./index-DF-gy-JN.js";
import { I as InputError } from "./input-error-LHj2Yd89.js";
import { A as AppLayout } from "./app-layout-BjBI-qwE.js";
import { a as edit$1, S as SettingsLayout, H as HeadingSmall } from "./layout-CksbvRJK.js";
import { Transition } from "@headlessui/react";
import { Head, Form } from "@inertiajs/react";
import { useRef } from "react";
import { B as Button } from "./app-logo-icon-CjPV8BY0.js";
import { I as Input } from "./input-B2XqrLJF.js";
import { L as Label } from "./label-BBu1QntT.js";
import "class-variance-authority";
import "@radix-ui/react-tooltip";
import "@radix-ui/react-slot";
import "lucide-react";
import "@radix-ui/react-avatar";
import "@radix-ui/react-dropdown-menu";
import "@radix-ui/react-navigation-menu";
import "@radix-ui/react-dialog";
import "@radix-ui/react-separator";
import "./index-BFo0K_hG.js";
import "clsx";
import "tailwind-merge";
import "@radix-ui/react-label";
const edit = (options) => ({
  url: edit.url(options),
  method: "get"
});
edit.definition = {
  methods: ["get", "head"],
  url: "/settings/password"
};
edit.url = (options) => {
  return edit.definition.url + queryParams(options);
};
edit.get = (options) => ({
  url: edit.url(options),
  method: "get"
});
edit.head = (options) => ({
  url: edit.url(options),
  method: "head"
});
const editForm = (options) => ({
  action: edit.url(options),
  method: "get"
});
editForm.get = (options) => ({
  action: edit.url(options),
  method: "get"
});
editForm.head = (options) => ({
  action: edit.url({
    [options?.mergeQuery ? "mergeQuery" : "query"]: {
      _method: "HEAD",
      ...options?.query ?? options?.mergeQuery ?? {}
    }
  }),
  method: "get"
});
edit.form = editForm;
const update = (options) => ({
  url: update.url(options),
  method: "put"
});
update.definition = {
  methods: ["put"],
  url: "/settings/password"
};
update.url = (options) => {
  return update.definition.url + queryParams(options);
};
update.put = (options) => ({
  url: update.url(options),
  method: "put"
});
const updateForm = (options) => ({
  action: update.url({
    [options?.mergeQuery ? "mergeQuery" : "query"]: {
      _method: "PUT",
      ...options?.query ?? options?.mergeQuery ?? {}
    }
  }),
  method: "post"
});
updateForm.put = (options) => ({
  action: update.url({
    [options?.mergeQuery ? "mergeQuery" : "query"]: {
      _method: "PUT",
      ...options?.query ?? options?.mergeQuery ?? {}
    }
  }),
  method: "post"
});
update.form = updateForm;
const PasswordController = { update };
const breadcrumbs = [
  {
    title: "Password settings",
    href: edit$1().url
  }
];
function Password() {
  const passwordInput = useRef(null);
  const currentPasswordInput = useRef(null);
  return /* @__PURE__ */ jsxs(AppLayout, { breadcrumbs, children: [
    /* @__PURE__ */ jsx(Head, { title: "Password settings" }),
    /* @__PURE__ */ jsx(SettingsLayout, { children: /* @__PURE__ */ jsxs("div", { className: "space-y-6", children: [
      /* @__PURE__ */ jsx(
        HeadingSmall,
        {
          title: "Update password",
          description: "Ensure your account is using a long, random password to stay secure"
        }
      ),
      /* @__PURE__ */ jsx(
        Form,
        {
          ...PasswordController.update.form(),
          options: {
            preserveScroll: true
          },
          resetOnError: [
            "password",
            "password_confirmation",
            "current_password"
          ],
          resetOnSuccess: true,
          onError: (errors) => {
            if (errors.password) {
              passwordInput.current?.focus();
            }
            if (errors.current_password) {
              currentPasswordInput.current?.focus();
            }
          },
          className: "space-y-6",
          children: ({ errors, processing, recentlySuccessful }) => /* @__PURE__ */ jsxs(Fragment, { children: [
            /* @__PURE__ */ jsxs("div", { className: "grid gap-2", children: [
              /* @__PURE__ */ jsx(Label, { htmlFor: "current_password", children: "Current password" }),
              /* @__PURE__ */ jsx(
                Input,
                {
                  id: "current_password",
                  ref: currentPasswordInput,
                  name: "current_password",
                  type: "password",
                  className: "mt-1 block w-full",
                  autoComplete: "current-password",
                  placeholder: "Current password"
                }
              ),
              /* @__PURE__ */ jsx(
                InputError,
                {
                  message: errors.current_password
                }
              )
            ] }),
            /* @__PURE__ */ jsxs("div", { className: "grid gap-2", children: [
              /* @__PURE__ */ jsx(Label, { htmlFor: "password", children: "New password" }),
              /* @__PURE__ */ jsx(
                Input,
                {
                  id: "password",
                  ref: passwordInput,
                  name: "password",
                  type: "password",
                  className: "mt-1 block w-full",
                  autoComplete: "new-password",
                  placeholder: "New password"
                }
              ),
              /* @__PURE__ */ jsx(InputError, { message: errors.password })
            ] }),
            /* @__PURE__ */ jsxs("div", { className: "grid gap-2", children: [
              /* @__PURE__ */ jsx(Label, { htmlFor: "password_confirmation", children: "Confirm password" }),
              /* @__PURE__ */ jsx(
                Input,
                {
                  id: "password_confirmation",
                  name: "password_confirmation",
                  type: "password",
                  className: "mt-1 block w-full",
                  autoComplete: "new-password",
                  placeholder: "Confirm password"
                }
              ),
              /* @__PURE__ */ jsx(
                InputError,
                {
                  message: errors.password_confirmation
                }
              )
            ] }),
            /* @__PURE__ */ jsxs("div", { className: "flex items-center gap-4", children: [
              /* @__PURE__ */ jsx(
                Button,
                {
                  disabled: processing,
                  "data-test": "update-password-button",
                  children: "Save password"
                }
              ),
              /* @__PURE__ */ jsx(
                Transition,
                {
                  show: recentlySuccessful,
                  enter: "transition ease-in-out",
                  enterFrom: "opacity-0",
                  leave: "transition ease-in-out",
                  leaveTo: "opacity-0",
                  children: /* @__PURE__ */ jsx("p", { className: "text-sm text-neutral-600", children: "Saved" })
                }
              )
            ] })
          ] })
        }
      )
    ] }) })
  ] });
}
export {
  Password as default
};
