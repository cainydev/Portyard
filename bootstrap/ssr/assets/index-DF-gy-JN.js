let urlDefaults = {};
const queryParams = (options) => {
  if (!options || !options.query && !options.mergeQuery) {
    return "";
  }
  const query = options.query ?? options.mergeQuery;
  const includeExisting = options.mergeQuery !== void 0;
  const getValue = (value) => {
    if (value === true) {
      return "1";
    }
    if (value === false) {
      return "0";
    }
    return value.toString();
  };
  const params = new URLSearchParams(
    includeExisting && typeof window !== "undefined" ? window.location.search : ""
  );
  for (const key in query) {
    if (query[key] === void 0 || query[key] === null) {
      params.delete(key);
      continue;
    }
    if (Array.isArray(query[key])) {
      if (params.has(`${key}[]`)) {
        params.delete(`${key}[]`);
      }
      query[key].forEach((value) => {
        params.append(`${key}[]`, value.toString());
      });
    } else if (typeof query[key] === "object") {
      params.forEach((_, paramKey) => {
        if (paramKey.startsWith(`${key}[`)) {
          params.delete(paramKey);
        }
      });
      for (const subKey in query[key]) {
        if (typeof query[key][subKey] === "undefined") {
          continue;
        }
        if (["string", "number", "boolean"].includes(
          typeof query[key][subKey]
        )) {
          params.set(
            `${key}[${subKey}]`,
            getValue(query[key][subKey])
          );
        }
      }
    } else {
      params.set(key, getValue(query[key]));
    }
  }
  const str = params.toString();
  return str.length > 0 ? `?${str}` : "";
};
const applyUrlDefaults = (existing) => {
  const existingParams = { ...existing ?? {} };
  for (const key in urlDefaults) {
    if (existingParams[key] === void 0 && urlDefaults[key] !== void 0) {
      existingParams[key] = urlDefaults[key];
    }
  }
  return existingParams;
};
const login = (options) => ({
  url: login.url(options),
  method: "get"
});
login.definition = {
  methods: ["get", "head"],
  url: "/login"
};
login.url = (options) => {
  return login.definition.url + queryParams(options);
};
login.get = (options) => ({
  url: login.url(options),
  method: "get"
});
login.head = (options) => ({
  url: login.url(options),
  method: "head"
});
const loginForm = (options) => ({
  action: login.url(options),
  method: "get"
});
loginForm.get = (options) => ({
  action: login.url(options),
  method: "get"
});
loginForm.head = (options) => ({
  action: login.url({
    [options?.mergeQuery ? "mergeQuery" : "query"]: {
      _method: "HEAD",
      ...options?.query ?? options?.mergeQuery ?? {}
    }
  }),
  method: "get"
});
login.form = loginForm;
const logout = (options) => ({
  url: logout.url(options),
  method: "post"
});
logout.definition = {
  methods: ["post"],
  url: "/logout"
};
logout.url = (options) => {
  return logout.definition.url + queryParams(options);
};
logout.post = (options) => ({
  url: logout.url(options),
  method: "post"
});
const logoutForm = (options) => ({
  action: logout.url(options),
  method: "post"
});
logoutForm.post = (options) => ({
  action: logout.url(options),
  method: "post"
});
logout.form = logoutForm;
const register = (options) => ({
  url: register.url(options),
  method: "get"
});
register.definition = {
  methods: ["get", "head"],
  url: "/register"
};
register.url = (options) => {
  return register.definition.url + queryParams(options);
};
register.get = (options) => ({
  url: register.url(options),
  method: "get"
});
register.head = (options) => ({
  url: register.url(options),
  method: "head"
});
const registerForm = (options) => ({
  action: register.url(options),
  method: "get"
});
registerForm.get = (options) => ({
  action: register.url(options),
  method: "get"
});
registerForm.head = (options) => ({
  action: register.url({
    [options?.mergeQuery ? "mergeQuery" : "query"]: {
      _method: "HEAD",
      ...options?.query ?? options?.mergeQuery ?? {}
    }
  }),
  method: "get"
});
register.form = registerForm;
const home = (options) => ({
  url: home.url(options),
  method: "get"
});
home.definition = {
  methods: ["get", "head"],
  url: "/"
};
home.url = (options) => {
  return home.definition.url + queryParams(options);
};
home.get = (options) => ({
  url: home.url(options),
  method: "get"
});
home.head = (options) => ({
  url: home.url(options),
  method: "head"
});
const homeForm = (options) => ({
  action: home.url(options),
  method: "get"
});
homeForm.get = (options) => ({
  action: home.url(options),
  method: "get"
});
homeForm.head = (options) => ({
  action: home.url({
    [options?.mergeQuery ? "mergeQuery" : "query"]: {
      _method: "HEAD",
      ...options?.query ?? options?.mergeQuery ?? {}
    }
  }),
  method: "get"
});
home.form = homeForm;
const dashboard = (options) => ({
  url: dashboard.url(options),
  method: "get"
});
dashboard.definition = {
  methods: ["get", "head"],
  url: "/dashboard"
};
dashboard.url = (options) => {
  return dashboard.definition.url + queryParams(options);
};
dashboard.get = (options) => ({
  url: dashboard.url(options),
  method: "get"
});
dashboard.head = (options) => ({
  url: dashboard.url(options),
  method: "head"
});
const dashboardForm = (options) => ({
  action: dashboard.url(options),
  method: "get"
});
dashboardForm.get = (options) => ({
  action: dashboard.url(options),
  method: "get"
});
dashboardForm.head = (options) => ({
  action: dashboard.url({
    [options?.mergeQuery ? "mergeQuery" : "query"]: {
      _method: "HEAD",
      ...options?.query ?? options?.mergeQuery ?? {}
    }
  }),
  method: "get"
});
dashboard.form = dashboardForm;
export {
  applyUrlDefaults as a,
  logout as b,
  dashboard as d,
  home as h,
  login as l,
  queryParams as q,
  register as r
};
