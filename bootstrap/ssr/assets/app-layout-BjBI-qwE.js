import { jsx, jsxs, Fragment } from "react/jsx-runtime";
import * as React from "react";
import { useSyncExternalStore, Fragment as Fragment$1, useCallback } from "react";
import { cva } from "class-variance-authority";
import { c as cn, A as AppLogoIcon, B as Button, r as resolveUrl, i as isSameUrl } from "./app-logo-icon-CjPV8BY0.js";
import * as TooltipPrimitive from "@radix-ui/react-tooltip";
import { Slot } from "@radix-ui/react-slot";
import { XIcon, ChevronRight, Settings, LogOut, Menu, LayoutGrid, Folder, BookOpen, Search } from "lucide-react";
import { Link, router, usePage } from "@inertiajs/react";
import * as AvatarPrimitive from "@radix-ui/react-avatar";
import * as DropdownMenuPrimitive from "@radix-ui/react-dropdown-menu";
import * as NavigationMenuPrimitive from "@radix-ui/react-navigation-menu";
import * as SheetPrimitive from "@radix-ui/react-dialog";
import { q as queryParams, b as logout, d as dashboard } from "./index-DF-gy-JN.js";
const MOBILE_BREAKPOINT = 768;
const mql = window.matchMedia(`(max-width: ${MOBILE_BREAKPOINT - 1}px)`);
function mediaQueryListener(callback) {
  mql.addEventListener("change", callback);
  return () => {
    mql.removeEventListener("change", callback);
  };
}
function isSmallerThanBreakpoint() {
  return mql.matches;
}
function useIsMobile() {
  return useSyncExternalStore(mediaQueryListener, isSmallerThanBreakpoint);
}
function Sheet({ ...props }) {
  return /* @__PURE__ */ jsx(SheetPrimitive.Root, { "data-slot": "sheet", ...props });
}
function SheetTrigger({
  ...props
}) {
  return /* @__PURE__ */ jsx(SheetPrimitive.Trigger, { "data-slot": "sheet-trigger", ...props });
}
function SheetPortal({
  ...props
}) {
  return /* @__PURE__ */ jsx(SheetPrimitive.Portal, { "data-slot": "sheet-portal", ...props });
}
function SheetOverlay({
  className,
  ...props
}) {
  return /* @__PURE__ */ jsx(
    SheetPrimitive.Overlay,
    {
      "data-slot": "sheet-overlay",
      className: cn(
        "data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 fixed inset-0 z-50 bg-black/80",
        className
      ),
      ...props
    }
  );
}
function SheetContent({
  className,
  children,
  side = "right",
  ...props
}) {
  return /* @__PURE__ */ jsxs(SheetPortal, { children: [
    /* @__PURE__ */ jsx(SheetOverlay, {}),
    /* @__PURE__ */ jsxs(
      SheetPrimitive.Content,
      {
        "data-slot": "sheet-content",
        className: cn(
          "bg-background data-[state=open]:animate-in data-[state=closed]:animate-out fixed z-50 flex flex-col gap-4 shadow-lg transition ease-in-out data-[state=closed]:duration-300 data-[state=open]:duration-500",
          side === "right" && "data-[state=closed]:slide-out-to-right data-[state=open]:slide-in-from-right inset-y-0 right-0 h-full w-3/4 border-l sm:max-w-sm",
          side === "left" && "data-[state=closed]:slide-out-to-left data-[state=open]:slide-in-from-left inset-y-0 left-0 h-full w-3/4 border-r sm:max-w-sm",
          side === "top" && "data-[state=closed]:slide-out-to-top data-[state=open]:slide-in-from-top inset-x-0 top-0 h-auto border-b",
          side === "bottom" && "data-[state=closed]:slide-out-to-bottom data-[state=open]:slide-in-from-bottom inset-x-0 bottom-0 h-auto border-t",
          className
        ),
        ...props,
        children: [
          children,
          /* @__PURE__ */ jsxs(SheetPrimitive.Close, { className: "ring-offset-background focus:ring-ring data-[state=open]:bg-secondary absolute top-4 right-4 rounded-xs opacity-70 transition-opacity hover:opacity-100 focus:ring-2 focus:ring-offset-2 focus:outline-hidden disabled:pointer-events-none", children: [
            /* @__PURE__ */ jsx(XIcon, { className: "size-4" }),
            /* @__PURE__ */ jsx("span", { className: "sr-only", children: "Close" })
          ] })
        ]
      }
    )
  ] });
}
function SheetHeader({ className, ...props }) {
  return /* @__PURE__ */ jsx(
    "div",
    {
      "data-slot": "sheet-header",
      className: cn("flex flex-col gap-1.5 p-4", className),
      ...props
    }
  );
}
function SheetTitle({
  className,
  ...props
}) {
  return /* @__PURE__ */ jsx(
    SheetPrimitive.Title,
    {
      "data-slot": "sheet-title",
      className: cn("text-foreground font-semibold", className),
      ...props
    }
  );
}
function TooltipProvider({
  delayDuration = 0,
  ...props
}) {
  return /* @__PURE__ */ jsx(
    TooltipPrimitive.Provider,
    {
      "data-slot": "tooltip-provider",
      delayDuration,
      ...props
    }
  );
}
function Tooltip({
  ...props
}) {
  return /* @__PURE__ */ jsx(TooltipProvider, { children: /* @__PURE__ */ jsx(TooltipPrimitive.Root, { "data-slot": "tooltip", ...props }) });
}
function TooltipTrigger({
  ...props
}) {
  return /* @__PURE__ */ jsx(TooltipPrimitive.Trigger, { "data-slot": "tooltip-trigger", ...props });
}
function TooltipContent({
  className,
  sideOffset = 4,
  children,
  ...props
}) {
  return /* @__PURE__ */ jsx(TooltipPrimitive.Portal, { children: /* @__PURE__ */ jsxs(
    TooltipPrimitive.Content,
    {
      "data-slot": "tooltip-content",
      sideOffset,
      className: cn(
        "bg-primary text-primary-foreground animate-in fade-in-0 zoom-in-95 data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=closed]:zoom-out-95 data-[side=bottom]:slide-in-from-top-2 data-[side=left]:slide-in-from-right-2 data-[side=right]:slide-in-from-left-2 data-[side=top]:slide-in-from-bottom-2 z-50 max-w-sm rounded-md px-3 py-1.5 text-xs",
        className
      ),
      ...props,
      children: [
        children,
        /* @__PURE__ */ jsx(TooltipPrimitive.Arrow, { className: "bg-primary fill-primary z-50 size-2.5 translate-y-[calc(-50%_-_2px)] rotate-45 rounded-[2px]" })
      ]
    }
  ) });
}
const SIDEBAR_COOKIE_NAME = "sidebar_state";
const SIDEBAR_COOKIE_MAX_AGE = 60 * 60 * 24 * 7;
const SIDEBAR_WIDTH = "16rem";
const SIDEBAR_WIDTH_ICON = "3rem";
const SIDEBAR_KEYBOARD_SHORTCUT = "b";
const SidebarContext = React.createContext(null);
function SidebarProvider({
  defaultOpen = true,
  open: openProp,
  onOpenChange: setOpenProp,
  className,
  style,
  children,
  ...props
}) {
  const isMobile = useIsMobile();
  const [openMobile, setOpenMobile] = React.useState(false);
  const [_open, _setOpen] = React.useState(defaultOpen);
  const open = openProp ?? _open;
  const setOpen = React.useCallback(
    (value) => {
      const openState = typeof value === "function" ? value(open) : value;
      if (setOpenProp) {
        setOpenProp(openState);
      } else {
        _setOpen(openState);
      }
      document.cookie = `${SIDEBAR_COOKIE_NAME}=${openState}; path=/; max-age=${SIDEBAR_COOKIE_MAX_AGE}`;
    },
    [setOpenProp, open]
  );
  const toggleSidebar = React.useCallback(() => {
    return isMobile ? setOpenMobile((open2) => !open2) : setOpen((open2) => !open2);
  }, [isMobile, setOpen, setOpenMobile]);
  React.useEffect(() => {
    const handleKeyDown = (event) => {
      if (event.key === SIDEBAR_KEYBOARD_SHORTCUT && (event.metaKey || event.ctrlKey)) {
        event.preventDefault();
        toggleSidebar();
      }
    };
    window.addEventListener("keydown", handleKeyDown);
    return () => window.removeEventListener("keydown", handleKeyDown);
  }, [toggleSidebar]);
  const state = open ? "expanded" : "collapsed";
  const contextValue = React.useMemo(
    () => ({
      state,
      open,
      setOpen,
      isMobile,
      openMobile,
      setOpenMobile,
      toggleSidebar
    }),
    [state, open, setOpen, isMobile, openMobile, setOpenMobile, toggleSidebar]
  );
  return /* @__PURE__ */ jsx(SidebarContext.Provider, { value: contextValue, children: /* @__PURE__ */ jsx(TooltipProvider, { delayDuration: 0, children: /* @__PURE__ */ jsx(
    "div",
    {
      "data-slot": "sidebar-wrapper",
      style: {
        "--sidebar-width": SIDEBAR_WIDTH,
        "--sidebar-width-icon": SIDEBAR_WIDTH_ICON,
        ...style
      },
      className: cn(
        "group/sidebar-wrapper has-data-[variant=inset]:bg-sidebar flex min-h-svh w-full",
        className
      ),
      ...props,
      children
    }
  ) }) });
}
function SidebarInset({ className, ...props }) {
  return /* @__PURE__ */ jsx(
    "main",
    {
      "data-slot": "sidebar-inset",
      className: cn(
        "bg-background relative flex max-w-full min-h-svh flex-1 flex-col",
        "peer-data-[variant=inset]:min-h-[calc(100svh-(--spacing(4)))] md:peer-data-[variant=inset]:m-2 md:peer-data-[variant=inset]:ml-0 md:peer-data-[variant=inset]:rounded-xl md:peer-data-[variant=inset]:shadow-sm md:peer-data-[variant=inset]:peer-data-[state=collapsed]:ml-0",
        className
      ),
      ...props
    }
  );
}
cva(
  "peer/menu-button flex w-full items-center gap-2 overflow-hidden rounded-md p-2 text-left text-sm outline-hidden ring-sidebar-ring transition-[width,height,padding] hover:bg-sidebar-accent hover:text-sidebar-accent-foreground focus-visible:ring-2 active:bg-sidebar-accent active:text-sidebar-accent-foreground disabled:pointer-events-none disabled:opacity-50 group-has-data-[sidebar=menu-action]/menu-item:pr-8 aria-disabled:pointer-events-none aria-disabled:opacity-50 data-[active=true]:bg-sidebar-accent data-[active=true]:font-medium data-[active=true]:text-sidebar-accent-foreground data-[state=open]:hover:bg-sidebar-accent data-[state=open]:hover:text-sidebar-accent-foreground group-data-[collapsible=icon]:size-8! group-data-[collapsible=icon]:p-2! [&>span:last-child]:truncate [&>svg]:size-4 [&>svg]:shrink-0",
  {
    variants: {
      variant: {
        default: "hover:bg-sidebar-accent hover:text-sidebar-accent-foreground",
        outline: "bg-background shadow-[0_0_0_1px_hsl(var(--sidebar-border))] hover:bg-sidebar-accent hover:text-sidebar-accent-foreground hover:shadow-[0_0_0_1px_hsl(var(--sidebar-accent))]"
      },
      size: {
        default: "h-8 text-sm",
        sm: "h-7 text-xs",
        lg: "h-12 text-sm group-data-[collapsible=icon]:p-0!"
      }
    },
    defaultVariants: {
      variant: "default",
      size: "default"
    }
  }
);
function AppContent({
  variant = "header",
  children,
  ...props
}) {
  if (variant === "sidebar") {
    return /* @__PURE__ */ jsx(SidebarInset, { ...props, children });
  }
  return /* @__PURE__ */ jsx(
    "main",
    {
      className: "mx-auto flex h-full w-full max-w-7xl flex-1 flex-col gap-4 rounded-xl",
      ...props,
      children
    }
  );
}
function Breadcrumb({ ...props }) {
  return /* @__PURE__ */ jsx("nav", { "aria-label": "breadcrumb", "data-slot": "breadcrumb", ...props });
}
function BreadcrumbList({ className, ...props }) {
  return /* @__PURE__ */ jsx(
    "ol",
    {
      "data-slot": "breadcrumb-list",
      className: cn(
        "text-muted-foreground flex flex-wrap items-center gap-1.5 text-sm break-words sm:gap-2.5",
        className
      ),
      ...props
    }
  );
}
function BreadcrumbItem({ className, ...props }) {
  return /* @__PURE__ */ jsx(
    "li",
    {
      "data-slot": "breadcrumb-item",
      className: cn("inline-flex items-center gap-1.5", className),
      ...props
    }
  );
}
function BreadcrumbLink({
  asChild,
  className,
  ...props
}) {
  const Comp = asChild ? Slot : "a";
  return /* @__PURE__ */ jsx(
    Comp,
    {
      "data-slot": "breadcrumb-link",
      className: cn("hover:text-foreground transition-colors", className),
      ...props
    }
  );
}
function BreadcrumbPage({ className, ...props }) {
  return /* @__PURE__ */ jsx(
    "span",
    {
      "data-slot": "breadcrumb-page",
      role: "link",
      "aria-disabled": "true",
      "aria-current": "page",
      className: cn("text-foreground font-normal", className),
      ...props
    }
  );
}
function BreadcrumbSeparator({
  children,
  className,
  ...props
}) {
  return /* @__PURE__ */ jsx(
    "li",
    {
      "data-slot": "breadcrumb-separator",
      role: "presentation",
      "aria-hidden": "true",
      className: cn("[&>svg]:size-3.5", className),
      ...props,
      children: children ?? /* @__PURE__ */ jsx(ChevronRight, {})
    }
  );
}
function Breadcrumbs({
  breadcrumbs
}) {
  return /* @__PURE__ */ jsx(Fragment, { children: breadcrumbs.length > 0 && /* @__PURE__ */ jsx(Breadcrumb, { children: /* @__PURE__ */ jsx(BreadcrumbList, { children: breadcrumbs.map((item, index) => {
    const isLast = index === breadcrumbs.length - 1;
    return /* @__PURE__ */ jsxs(Fragment$1, { children: [
      /* @__PURE__ */ jsx(BreadcrumbItem, { children: isLast ? /* @__PURE__ */ jsx(BreadcrumbPage, { children: item.title }) : /* @__PURE__ */ jsx(BreadcrumbLink, { asChild: true, children: /* @__PURE__ */ jsx(Link, { href: item.href, children: item.title }) }) }),
      !isLast && /* @__PURE__ */ jsx(BreadcrumbSeparator, {})
    ] }, index);
  }) }) }) });
}
function Icon({
  iconNode: IconComponent,
  className,
  ...props
}) {
  return /* @__PURE__ */ jsx(IconComponent, { className: cn("h-4 w-4", className), ...props });
}
function Avatar({
  className,
  ...props
}) {
  return /* @__PURE__ */ jsx(
    AvatarPrimitive.Root,
    {
      "data-slot": "avatar",
      className: cn(
        "relative flex size-8 shrink-0 overflow-hidden rounded-full",
        className
      ),
      ...props
    }
  );
}
function AvatarImage({
  className,
  ...props
}) {
  return /* @__PURE__ */ jsx(
    AvatarPrimitive.Image,
    {
      "data-slot": "avatar-image",
      className: cn("aspect-square size-full", className),
      ...props
    }
  );
}
function AvatarFallback({
  className,
  ...props
}) {
  return /* @__PURE__ */ jsx(
    AvatarPrimitive.Fallback,
    {
      "data-slot": "avatar-fallback",
      className: cn(
        "bg-muted flex size-full items-center justify-center rounded-full",
        className
      ),
      ...props
    }
  );
}
function DropdownMenu({
  ...props
}) {
  return /* @__PURE__ */ jsx(DropdownMenuPrimitive.Root, { "data-slot": "dropdown-menu", ...props });
}
function DropdownMenuTrigger({
  ...props
}) {
  return /* @__PURE__ */ jsx(
    DropdownMenuPrimitive.Trigger,
    {
      "data-slot": "dropdown-menu-trigger",
      ...props
    }
  );
}
function DropdownMenuContent({
  className,
  sideOffset = 4,
  ...props
}) {
  return /* @__PURE__ */ jsx(DropdownMenuPrimitive.Portal, { children: /* @__PURE__ */ jsx(
    DropdownMenuPrimitive.Content,
    {
      "data-slot": "dropdown-menu-content",
      sideOffset,
      className: cn(
        "bg-popover text-popover-foreground data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95 data-[side=bottom]:slide-in-from-top-2 data-[side=left]:slide-in-from-right-2 data-[side=right]:slide-in-from-left-2 data-[side=top]:slide-in-from-bottom-2 z-50 min-w-[8rem] overflow-hidden rounded-md border p-1 shadow-md",
        className
      ),
      ...props
    }
  ) });
}
function DropdownMenuGroup({
  ...props
}) {
  return /* @__PURE__ */ jsx(DropdownMenuPrimitive.Group, { "data-slot": "dropdown-menu-group", ...props });
}
function DropdownMenuItem({
  className,
  inset,
  variant = "default",
  ...props
}) {
  return /* @__PURE__ */ jsx(
    DropdownMenuPrimitive.Item,
    {
      "data-slot": "dropdown-menu-item",
      "data-inset": inset,
      "data-variant": variant,
      className: cn(
        "focus:bg-accent focus:text-accent-foreground data-[variant=destructive]:text-destructive-foreground data-[variant=destructive]:focus:bg-destructive/10 dark:data-[variant=destructive]:focus:bg-destructive/40 data-[variant=destructive]:focus:text-destructive-foreground data-[variant=destructive]:*:[svg]:!text-destructive-foreground [&_svg:not([class*='text-'])]:text-muted-foreground relative flex cursor-default items-center gap-2 rounded-sm px-2 py-1.5 text-sm outline-hidden select-none data-[disabled]:pointer-events-none data-[disabled]:opacity-50 data-[inset]:pl-8 [&_svg]:pointer-events-none [&_svg]:shrink-0 [&_svg:not([class*='size-'])]:size-4",
        className
      ),
      ...props
    }
  );
}
function DropdownMenuLabel({
  className,
  inset,
  ...props
}) {
  return /* @__PURE__ */ jsx(
    DropdownMenuPrimitive.Label,
    {
      "data-slot": "dropdown-menu-label",
      "data-inset": inset,
      className: cn(
        "px-2 py-1.5 text-sm font-medium data-[inset]:pl-8",
        className
      ),
      ...props
    }
  );
}
function DropdownMenuSeparator({
  className,
  ...props
}) {
  return /* @__PURE__ */ jsx(
    DropdownMenuPrimitive.Separator,
    {
      "data-slot": "dropdown-menu-separator",
      className: cn("bg-border -mx-1 my-1 h-px", className),
      ...props
    }
  );
}
function NavigationMenu({
  className,
  children,
  viewport = true,
  ...props
}) {
  return /* @__PURE__ */ jsxs(
    NavigationMenuPrimitive.Root,
    {
      "data-slot": "navigation-menu",
      "data-viewport": viewport,
      className: cn(
        "group/navigation-menu relative flex max-w-max flex-1 items-center justify-center",
        className
      ),
      ...props,
      children: [
        children,
        viewport && /* @__PURE__ */ jsx(NavigationMenuViewport, {})
      ]
    }
  );
}
function NavigationMenuList({
  className,
  ...props
}) {
  return /* @__PURE__ */ jsx(
    NavigationMenuPrimitive.List,
    {
      "data-slot": "navigation-menu-list",
      className: cn(
        "group flex flex-1 list-none items-center justify-center gap-1",
        className
      ),
      ...props
    }
  );
}
function NavigationMenuItem({
  className,
  ...props
}) {
  return /* @__PURE__ */ jsx(
    NavigationMenuPrimitive.Item,
    {
      "data-slot": "navigation-menu-item",
      className: cn("relative", className),
      ...props
    }
  );
}
const navigationMenuTriggerStyle = cva(
  "group inline-flex h-9 w-max items-center justify-center rounded-md bg-background px-4 py-2 text-sm font-medium hover:bg-accent hover:text-accent-foreground focus:bg-accent focus:text-accent-foreground disabled:pointer-events-none disabled:opacity-50 data-[active=true]:bg-accent/50 data-[state=open]:bg-accent/50 data-[active=true]:text-accent-foreground ring-ring/10 dark:ring-ring/20 dark:outline-ring/40 outline-ring/50 transition-[color,box-shadow] focus-visible:ring-4 focus-visible:outline-1"
);
function NavigationMenuViewport({
  className,
  ...props
}) {
  return /* @__PURE__ */ jsx(
    "div",
    {
      className: cn(
        "absolute top-full left-0 isolate z-50 flex justify-center"
      ),
      children: /* @__PURE__ */ jsx(
        NavigationMenuPrimitive.Viewport,
        {
          "data-slot": "navigation-menu-viewport",
          className: cn(
            "origin-top-center bg-popover text-popover-foreground data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-90 relative mt-1.5 h-[var(--radix-navigation-menu-viewport-height)] w-full overflow-hidden rounded-md border shadow md:w-[var(--radix-navigation-menu-viewport-width)]",
            className
          ),
          ...props
        }
      )
    }
  );
}
function useInitials() {
  return useCallback((fullName) => {
    const names = fullName.trim().split(" ");
    if (names.length === 0) return "";
    if (names.length === 1) return names[0].charAt(0).toUpperCase();
    const firstInitial = names[0].charAt(0);
    const lastInitial = names[names.length - 1].charAt(0);
    return `${firstInitial}${lastInitial}`.toUpperCase();
  }, []);
}
function UserInfo({
  user,
  showEmail = false
}) {
  const getInitials = useInitials();
  return /* @__PURE__ */ jsxs(Fragment, { children: [
    /* @__PURE__ */ jsxs(Avatar, { className: "h-8 w-8 overflow-hidden rounded-full", children: [
      /* @__PURE__ */ jsx(AvatarImage, { src: user.avatar, alt: user.name }),
      /* @__PURE__ */ jsx(AvatarFallback, { className: "rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white", children: getInitials(user.name) })
    ] }),
    /* @__PURE__ */ jsxs("div", { className: "grid flex-1 text-left text-sm leading-tight", children: [
      /* @__PURE__ */ jsx("span", { className: "truncate font-medium", children: user.name }),
      showEmail && /* @__PURE__ */ jsx("span", { className: "truncate text-xs text-muted-foreground", children: user.email })
    ] })
  ] });
}
function useMobileNavigation() {
  return useCallback(() => {
    document.body.style.removeProperty("pointer-events");
  }, []);
}
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
({
  edit: Object.assign(edit, edit),
  update: Object.assign(update, update),
  destroy: Object.assign(destroy, destroy)
});
function UserMenuContent({ user }) {
  const cleanup = useMobileNavigation();
  const handleLogout = () => {
    cleanup();
    router.flushAll();
  };
  return /* @__PURE__ */ jsxs(Fragment, { children: [
    /* @__PURE__ */ jsx(DropdownMenuLabel, { className: "p-0 font-normal", children: /* @__PURE__ */ jsx("div", { className: "flex items-center gap-2 px-1 py-1.5 text-left text-sm", children: /* @__PURE__ */ jsx(UserInfo, { user, showEmail: true }) }) }),
    /* @__PURE__ */ jsx(DropdownMenuSeparator, {}),
    /* @__PURE__ */ jsx(DropdownMenuGroup, { children: /* @__PURE__ */ jsx(DropdownMenuItem, { asChild: true, children: /* @__PURE__ */ jsxs(
      Link,
      {
        className: "block w-full",
        href: edit(),
        as: "button",
        prefetch: true,
        onClick: cleanup,
        children: [
          /* @__PURE__ */ jsx(Settings, { className: "mr-2" }),
          "Settings"
        ]
      }
    ) }) }),
    /* @__PURE__ */ jsx(DropdownMenuSeparator, {}),
    /* @__PURE__ */ jsx(DropdownMenuItem, { asChild: true, children: /* @__PURE__ */ jsxs(
      Link,
      {
        className: "block w-full",
        href: logout(),
        as: "button",
        onClick: handleLogout,
        "data-test": "logout-button",
        children: [
          /* @__PURE__ */ jsx(LogOut, { className: "mr-2" }),
          "Log out"
        ]
      }
    ) })
  ] });
}
function AppLogo() {
  return /* @__PURE__ */ jsxs(Fragment, { children: [
    /* @__PURE__ */ jsx("div", { className: "flex aspect-square size-8 items-center justify-center rounded-md bg-sidebar-primary text-sidebar-primary-foreground", children: /* @__PURE__ */ jsx(AppLogoIcon, { className: "size-5 fill-current text-white dark:text-black" }) }),
    /* @__PURE__ */ jsx("div", { className: "ml-1 grid flex-1 text-left text-sm", children: /* @__PURE__ */ jsx("span", { className: "mb-0.5 truncate leading-tight font-semibold", children: "Laravel Starter Kit" }) })
  ] });
}
const mainNavItems = [
  {
    title: "Dashboard",
    href: dashboard(),
    icon: LayoutGrid
  }
];
const rightNavItems = [
  {
    title: "Repository",
    href: "https://github.com/laravel/react-starter-kit",
    icon: Folder
  },
  {
    title: "Documentation",
    href: "https://laravel.com/docs/starter-kits#react",
    icon: BookOpen
  }
];
const activeItemStyles = "text-neutral-900 dark:bg-neutral-800 dark:text-neutral-100";
function AppHeader({ breadcrumbs = [] }) {
  const page = usePage();
  const { auth } = page.props;
  const getInitials = useInitials();
  return /* @__PURE__ */ jsxs(Fragment, { children: [
    /* @__PURE__ */ jsx("div", { className: "border-b border-sidebar-border/80", children: /* @__PURE__ */ jsxs("div", { className: "mx-auto flex h-16 items-center px-4 md:max-w-7xl", children: [
      /* @__PURE__ */ jsx("div", { className: "lg:hidden", children: /* @__PURE__ */ jsxs(Sheet, { children: [
        /* @__PURE__ */ jsx(SheetTrigger, { asChild: true, children: /* @__PURE__ */ jsx(
          Button,
          {
            variant: "ghost",
            size: "icon",
            className: "mr-2 h-[34px] w-[34px]",
            children: /* @__PURE__ */ jsx(Menu, { className: "h-5 w-5" })
          }
        ) }),
        /* @__PURE__ */ jsxs(
          SheetContent,
          {
            side: "left",
            className: "flex h-full w-64 flex-col items-stretch justify-between bg-sidebar",
            children: [
              /* @__PURE__ */ jsx(SheetTitle, { className: "sr-only", children: "Navigation Menu" }),
              /* @__PURE__ */ jsx(SheetHeader, { className: "flex justify-start text-left", children: /* @__PURE__ */ jsx(AppLogoIcon, { className: "h-6 w-6 fill-current text-black dark:text-white" }) }),
              /* @__PURE__ */ jsx("div", { className: "flex h-full flex-1 flex-col space-y-4 p-4", children: /* @__PURE__ */ jsxs("div", { className: "flex h-full flex-col justify-between text-sm", children: [
                /* @__PURE__ */ jsx("div", { className: "flex flex-col space-y-4", children: mainNavItems.map((item) => /* @__PURE__ */ jsxs(
                  Link,
                  {
                    href: item.href,
                    className: "flex items-center space-x-2 font-medium",
                    children: [
                      item.icon && /* @__PURE__ */ jsx(
                        Icon,
                        {
                          iconNode: item.icon,
                          className: "h-5 w-5"
                        }
                      ),
                      /* @__PURE__ */ jsx("span", { children: item.title })
                    ]
                  },
                  item.title
                )) }),
                /* @__PURE__ */ jsx("div", { className: "flex flex-col space-y-4", children: rightNavItems.map((item) => /* @__PURE__ */ jsxs(
                  "a",
                  {
                    href: resolveUrl(item.href),
                    target: "_blank",
                    rel: "noopener noreferrer",
                    className: "flex items-center space-x-2 font-medium",
                    children: [
                      item.icon && /* @__PURE__ */ jsx(
                        Icon,
                        {
                          iconNode: item.icon,
                          className: "h-5 w-5"
                        }
                      ),
                      /* @__PURE__ */ jsx("span", { children: item.title })
                    ]
                  },
                  item.title
                )) })
              ] }) })
            ]
          }
        )
      ] }) }),
      /* @__PURE__ */ jsx(
        Link,
        {
          href: dashboard(),
          prefetch: true,
          className: "flex items-center space-x-2",
          children: /* @__PURE__ */ jsx(AppLogo, {})
        }
      ),
      /* @__PURE__ */ jsx("div", { className: "ml-6 hidden h-full items-center space-x-6 lg:flex", children: /* @__PURE__ */ jsx(NavigationMenu, { className: "flex h-full items-stretch", children: /* @__PURE__ */ jsx(NavigationMenuList, { className: "flex h-full items-stretch space-x-2", children: mainNavItems.map((item, index) => /* @__PURE__ */ jsxs(
        NavigationMenuItem,
        {
          className: "relative flex h-full items-center",
          children: [
            /* @__PURE__ */ jsxs(
              Link,
              {
                href: item.href,
                className: cn(
                  navigationMenuTriggerStyle(),
                  isSameUrl(
                    page.url,
                    item.href
                  ) && activeItemStyles,
                  "h-9 cursor-pointer px-3"
                ),
                children: [
                  item.icon && /* @__PURE__ */ jsx(
                    Icon,
                    {
                      iconNode: item.icon,
                      className: "mr-2 h-4 w-4"
                    }
                  ),
                  item.title
                ]
              }
            ),
            isSameUrl(page.url, item.href) && /* @__PURE__ */ jsx("div", { className: "absolute bottom-0 left-0 h-0.5 w-full translate-y-px bg-black dark:bg-white" })
          ]
        },
        index
      )) }) }) }),
      /* @__PURE__ */ jsxs("div", { className: "ml-auto flex items-center space-x-2", children: [
        /* @__PURE__ */ jsxs("div", { className: "relative flex items-center space-x-1", children: [
          /* @__PURE__ */ jsx(
            Button,
            {
              variant: "ghost",
              size: "icon",
              className: "group h-9 w-9 cursor-pointer",
              children: /* @__PURE__ */ jsx(Search, { className: "!size-5 opacity-80 group-hover:opacity-100" })
            }
          ),
          /* @__PURE__ */ jsx("div", { className: "hidden lg:flex", children: rightNavItems.map((item) => /* @__PURE__ */ jsx(
            TooltipProvider,
            {
              delayDuration: 0,
              children: /* @__PURE__ */ jsxs(Tooltip, { children: [
                /* @__PURE__ */ jsx(TooltipTrigger, { children: /* @__PURE__ */ jsxs(
                  "a",
                  {
                    href: resolveUrl(item.href),
                    target: "_blank",
                    rel: "noopener noreferrer",
                    className: "group ml-1 inline-flex h-9 w-9 items-center justify-center rounded-md bg-transparent p-0 text-sm font-medium text-accent-foreground ring-offset-background transition-colors hover:bg-accent hover:text-accent-foreground focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none disabled:pointer-events-none disabled:opacity-50",
                    children: [
                      /* @__PURE__ */ jsx("span", { className: "sr-only", children: item.title }),
                      item.icon && /* @__PURE__ */ jsx(
                        Icon,
                        {
                          iconNode: item.icon,
                          className: "size-5 opacity-80 group-hover:opacity-100"
                        }
                      )
                    ]
                  }
                ) }),
                /* @__PURE__ */ jsx(TooltipContent, { children: /* @__PURE__ */ jsx("p", { children: item.title }) })
              ] })
            },
            item.title
          )) })
        ] }),
        /* @__PURE__ */ jsxs(DropdownMenu, { children: [
          /* @__PURE__ */ jsx(DropdownMenuTrigger, { asChild: true, children: /* @__PURE__ */ jsx(
            Button,
            {
              variant: "ghost",
              className: "size-10 rounded-full p-1",
              children: /* @__PURE__ */ jsxs(Avatar, { className: "size-8 overflow-hidden rounded-full", children: [
                /* @__PURE__ */ jsx(
                  AvatarImage,
                  {
                    src: auth.user.avatar,
                    alt: auth.user.name
                  }
                ),
                /* @__PURE__ */ jsx(AvatarFallback, { className: "rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white", children: getInitials(auth.user.name) })
              ] })
            }
          ) }),
          /* @__PURE__ */ jsx(DropdownMenuContent, { className: "w-56", align: "end", children: /* @__PURE__ */ jsx(UserMenuContent, { user: auth.user }) })
        ] })
      ] })
    ] }) }),
    breadcrumbs.length > 1 && /* @__PURE__ */ jsx("div", { className: "flex w-full border-b border-sidebar-border/70", children: /* @__PURE__ */ jsx("div", { className: "mx-auto flex h-12 w-full items-center justify-start px-4 text-neutral-500 md:max-w-7xl", children: /* @__PURE__ */ jsx(Breadcrumbs, { breadcrumbs }) }) })
  ] });
}
function AppShell({ children, variant = "header" }) {
  const isOpen = usePage().props.sidebarOpen;
  if (variant === "header") {
    return /* @__PURE__ */ jsx("div", { className: "flex min-h-screen w-full flex-col", children });
  }
  return /* @__PURE__ */ jsx(SidebarProvider, { defaultOpen: isOpen, children });
}
function AppHeaderLayout({
  children,
  breadcrumbs
}) {
  return /* @__PURE__ */ jsxs(AppShell, { children: [
    /* @__PURE__ */ jsx(AppHeader, { breadcrumbs }),
    /* @__PURE__ */ jsx(AppContent, { children })
  ] });
}
const AppLayout = ({ children, breadcrumbs, ...props }) => /* @__PURE__ */ jsx(AppHeaderLayout, { breadcrumbs, ...props, children });
export {
  AppLayout as A,
  edit as e
};
