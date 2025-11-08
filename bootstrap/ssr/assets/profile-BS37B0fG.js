import { jsxs, jsx, Fragment } from "react/jsx-runtime";
import { q as queryParams } from "./index-DF-gy-JN.js";
import { s as send } from "./index-CDdxn9AH.js";
import { Transition } from "@headlessui/react";
import { Form, usePage, Head, Link } from "@inertiajs/react";
import { H as HeadingSmall, S as SettingsLayout } from "./layout-CksbvRJK.js";
import { I as InputError } from "./input-error-LHj2Yd89.js";
import { B as Button } from "./app-logo-icon-CjPV8BY0.js";
import { D as Dialog, a as DialogTrigger, b as DialogContent, c as DialogTitle, d as DialogDescription, e as DialogFooter, f as DialogClose } from "./dialog-CwlQvWxw.js";
import { I as Input } from "./input-B2XqrLJF.js";
import { L as Label } from "./label-BBu1QntT.js";
import { useRef } from "react";
import { A as AppLayout, e as edit$1 } from "./app-layout-BjBI-qwE.js";
import "@radix-ui/react-separator";
import "./index-BFo0K_hG.js";
import "@radix-ui/react-slot";
import "class-variance-authority";
import "clsx";
import "tailwind-merge";
import "@radix-ui/react-dialog";
import "lucide-react";
import "@radix-ui/react-label";
import "@radix-ui/react-tooltip";
import "@radix-ui/react-avatar";
import "@radix-ui/react-dropdown-menu";
import "@radix-ui/react-navigation-menu";
const edit = (options) => ({
  url: edit.url(options),
  method: "get"
});
edit.definition = {
  methods: ["get", "head"],
  url: "/settings/profile"
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
  method: "patch"
});
update.definition = {
  methods: ["patch"],
  url: "/settings/profile"
};
update.url = (options) => {
  return update.definition.url + queryParams(options);
};
update.patch = (options) => ({
  url: update.url(options),
  method: "patch"
});
const updateForm = (options) => ({
  action: update.url({
    [options?.mergeQuery ? "mergeQuery" : "query"]: {
      _method: "PATCH",
      ...options?.query ?? options?.mergeQuery ?? {}
    }
  }),
  method: "post"
});
updateForm.patch = (options) => ({
  action: update.url({
    [options?.mergeQuery ? "mergeQuery" : "query"]: {
      _method: "PATCH",
      ...options?.query ?? options?.mergeQuery ?? {}
    }
  }),
  method: "post"
});
update.form = updateForm;
const destroy = (options) => ({
  url: destroy.url(options),
  method: "delete"
});
destroy.definition = {
  methods: ["delete"],
  url: "/settings/profile"
};
destroy.url = (options) => {
  return destroy.definition.url + queryParams(options);
};
destroy.delete = (options) => ({
  url: destroy.url(options),
  method: "delete"
});
const destroyForm = (options) => ({
  action: destroy.url({
    [options?.mergeQuery ? "mergeQuery" : "query"]: {
      _method: "DELETE",
      ...options?.query ?? options?.mergeQuery ?? {}
    }
  }),
  method: "post"
});
destroyForm.delete = (options) => ({
  action: destroy.url({
    [options?.mergeQuery ? "mergeQuery" : "query"]: {
      _method: "DELETE",
      ...options?.query ?? options?.mergeQuery ?? {}
    }
  }),
  method: "post"
});
destroy.form = destroyForm;
const ProfileController = { update, destroy };
function DeleteUser() {
  const passwordInput = useRef(null);
  return /* @__PURE__ */ jsxs("div", { className: "space-y-6", children: [
    /* @__PURE__ */ jsx(
      HeadingSmall,
      {
        title: "Delete account",
        description: "Delete your account and all of its resources"
      }
    ),
    /* @__PURE__ */ jsxs("div", { className: "space-y-4 rounded-lg border border-red-100 bg-red-50 p-4 dark:border-red-200/10 dark:bg-red-700/10", children: [
      /* @__PURE__ */ jsxs("div", { className: "relative space-y-0.5 text-red-600 dark:text-red-100", children: [
        /* @__PURE__ */ jsx("p", { className: "font-medium", children: "Warning" }),
        /* @__PURE__ */ jsx("p", { className: "text-sm", children: "Please proceed with caution, this cannot be undone." })
      ] }),
      /* @__PURE__ */ jsxs(Dialog, { children: [
        /* @__PURE__ */ jsx(DialogTrigger, { asChild: true, children: /* @__PURE__ */ jsx(
          Button,
          {
            variant: "destructive",
            "data-test": "delete-user-button",
            children: "Delete account"
          }
        ) }),
        /* @__PURE__ */ jsxs(DialogContent, { children: [
          /* @__PURE__ */ jsx(DialogTitle, { children: "Are you sure you want to delete your account?" }),
          /* @__PURE__ */ jsx(DialogDescription, { children: "Once your account is deleted, all of its resources and data will also be permanently deleted. Please enter your password to confirm you would like to permanently delete your account." }),
          /* @__PURE__ */ jsx(
            Form,
            {
              ...ProfileController.destroy.form(),
              options: {
                preserveScroll: true
              },
              onError: () => passwordInput.current?.focus(),
              resetOnSuccess: true,
              className: "space-y-6",
              children: ({ resetAndClearErrors, processing, errors }) => /* @__PURE__ */ jsxs(Fragment, { children: [
                /* @__PURE__ */ jsxs("div", { className: "grid gap-2", children: [
                  /* @__PURE__ */ jsx(
                    Label,
                    {
                      htmlFor: "password",
                      className: "sr-only",
                      children: "Password"
                    }
                  ),
                  /* @__PURE__ */ jsx(
                    Input,
                    {
                      id: "password",
                      type: "password",
                      name: "password",
                      ref: passwordInput,
                      placeholder: "Password",
                      autoComplete: "current-password"
                    }
                  ),
                  /* @__PURE__ */ jsx(InputError, { message: errors.password })
                ] }),
                /* @__PURE__ */ jsxs(DialogFooter, { className: "gap-2", children: [
                  /* @__PURE__ */ jsx(DialogClose, { asChild: true, children: /* @__PURE__ */ jsx(
                    Button,
                    {
                      variant: "secondary",
                      onClick: () => resetAndClearErrors(),
                      children: "Cancel"
                    }
                  ) }),
                  /* @__PURE__ */ jsx(
                    Button,
                    {
                      variant: "destructive",
                      disabled: processing,
                      asChild: true,
                      children: /* @__PURE__ */ jsx(
                        "button",
                        {
                          type: "submit",
                          "data-test": "confirm-delete-user-button",
                          children: "Delete account"
                        }
                      )
                    }
                  )
                ] })
              ] })
            }
          )
        ] })
      ] })
    ] })
  ] });
}
const breadcrumbs = [
  {
    title: "Profile settings",
    href: edit$1().url
  }
];
function Profile({
  mustVerifyEmail,
  status
}) {
  const { auth } = usePage().props;
  return /* @__PURE__ */ jsxs(AppLayout, { breadcrumbs, children: [
    /* @__PURE__ */ jsx(Head, { title: "Profile settings" }),
    /* @__PURE__ */ jsxs(SettingsLayout, { children: [
      /* @__PURE__ */ jsxs("div", { className: "space-y-6", children: [
        /* @__PURE__ */ jsx(
          HeadingSmall,
          {
            title: "Profile information",
            description: "Update your name and email address"
          }
        ),
        /* @__PURE__ */ jsx(
          Form,
          {
            ...ProfileController.update.form(),
            options: {
              preserveScroll: true
            },
            className: "space-y-6",
            children: ({ processing, recentlySuccessful, errors }) => /* @__PURE__ */ jsxs(Fragment, { children: [
              /* @__PURE__ */ jsxs("div", { className: "grid gap-2", children: [
                /* @__PURE__ */ jsx(Label, { htmlFor: "name", children: "Name" }),
                /* @__PURE__ */ jsx(
                  Input,
                  {
                    id: "name",
                    className: "mt-1 block w-full",
                    defaultValue: auth.user.name,
                    name: "name",
                    required: true,
                    autoComplete: "name",
                    placeholder: "Full name"
                  }
                ),
                /* @__PURE__ */ jsx(
                  InputError,
                  {
                    className: "mt-2",
                    message: errors.name
                  }
                )
              ] }),
              /* @__PURE__ */ jsxs("div", { className: "grid gap-2", children: [
                /* @__PURE__ */ jsx(Label, { htmlFor: "email", children: "Email address" }),
                /* @__PURE__ */ jsx(
                  Input,
                  {
                    id: "email",
                    type: "email",
                    className: "mt-1 block w-full",
                    defaultValue: auth.user.email,
                    name: "email",
                    required: true,
                    autoComplete: "username",
                    placeholder: "Email address"
                  }
                ),
                /* @__PURE__ */ jsx(
                  InputError,
                  {
                    className: "mt-2",
                    message: errors.email
                  }
                )
              ] }),
              mustVerifyEmail && auth.user.email_verified_at === null && /* @__PURE__ */ jsxs("div", { children: [
                /* @__PURE__ */ jsxs("p", { className: "-mt-4 text-sm text-muted-foreground", children: [
                  "Your email address is unverified.",
                  " ",
                  /* @__PURE__ */ jsx(
                    Link,
                    {
                      href: send(),
                      as: "button",
                      className: "text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500",
                      children: "Click here to resend the verification email."
                    }
                  )
                ] }),
                status === "verification-link-sent" && /* @__PURE__ */ jsx("div", { className: "mt-2 text-sm font-medium text-green-600", children: "A new verification link has been sent to your email address." })
              ] }),
              /* @__PURE__ */ jsxs("div", { className: "flex items-center gap-4", children: [
                /* @__PURE__ */ jsx(
                  Button,
                  {
                    disabled: processing,
                    "data-test": "update-profile-button",
                    children: "Save"
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
      ] }),
      /* @__PURE__ */ jsx(DeleteUser, {})
    ] })
  ] });
}
export {
  Profile as default
};
