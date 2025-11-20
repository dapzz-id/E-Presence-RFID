const { contextBridge } = require("electron");

contextBridge.exposeInMainWorld("env", {
    APP_URL: process.env.APP_URL,
    APP_NAME: process.env.APP_NAME
});
