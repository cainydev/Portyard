import { q as queryParams, a as applyUrlDefaults } from "./index-DF-gy-JN.js";
const notice = (options) => ({
  url: notice.url(options),
  method: "get"
});
notice.definition = {
  methods: ["get", "head"],
  url: "/email/verify"
};
notice.url = (options) => {
  return notice.definition.url + queryParams(options);
};
notice.get = (options) => ({
  url: notice.url(options),
  method: "get"
});
notice.head = (options) => ({
  url: notice.url(options),
  method: "head"
});
const noticeForm = (options) => ({
  action: notice.url(options),
  method: "get"
});
noticeForm.get = (options) => ({
  action: notice.url(options),
  method: "get"
});
noticeForm.head = (options) => ({
  action: notice.url({
    [options?.mergeQuery ? "mergeQuery" : "query"]: {
      _method: "HEAD",
      ...options?.query ?? options?.mergeQuery ?? {}
    }
  }),
  method: "get"
});
notice.form = noticeForm;
const verify = (args, options) => ({
  url: verify.url(args, options),
  method: "get"
});
verify.definition = {
  methods: ["get", "head"],
  url: "/email/verify/{id}/{hash}"
};
verify.url = (args, options) => {
  if (Array.isArray(args)) {
    args = {
      id: args[0],
      hash: args[1]
    };
  }
  args = applyUrlDefaults(args);
  const parsedArgs = {
    id: args.id,
    hash: args.hash
  };
  return verify.definition.url.replace("{id}", parsedArgs.id.toString()).replace("{hash}", parsedArgs.hash.toString()).replace(/\/+$/, "") + queryParams(options);
};
verify.get = (args, options) => ({
  url: verify.url(args, options),
  method: "get"
});
verify.head = (args, options) => ({
  url: verify.url(args, options),
  method: "head"
});
const verifyForm = (args, options) => ({
  action: verify.url(args, options),
  method: "get"
});
verifyForm.get = (args, options) => ({
  action: verify.url(args, options),
  method: "get"
});
verifyForm.head = (args, options) => ({
  action: verify.url(args, {
    [options?.mergeQuery ? "mergeQuery" : "query"]: {
      _method: "HEAD",
      ...options?.query ?? options?.mergeQuery ?? {}
    }
  }),
  method: "get"
});
verify.form = verifyForm;
const send = (options) => ({
  url: send.url(options),
  method: "post"
});
send.definition = {
  methods: ["post"],
  url: "/email/verification-notification"
};
send.url = (options) => {
  return send.definition.url + queryParams(options);
};
send.post = (options) => ({
  url: send.url(options),
  method: "post"
});
const sendForm = (options) => ({
  action: send.url(options),
  method: "post"
});
sendForm.post = (options) => ({
  action: send.url(options),
  method: "post"
});
send.form = sendForm;
({
  notice: Object.assign(notice, notice),
  verify: Object.assign(verify, verify),
  send: Object.assign(send, send)
});
export {
  send as s
};
