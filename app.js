const { app, BrowserWindow } = require('electron');
const path = require('path');
require("dotenv").config({
  path: path.join(__dirname, ".env")
});

const createWindow = () => {
  // Create the browser window.
  const mainWindow = new BrowserWindow({
    frame: false,
    titleBarStyle: 'hidden',
    titleBarOverlay: {
      color: '#ff000000',        // warna background titlebar
      symbolColor: '#ffffff',  // warna icon silang/minimize/maximize
      height: 102
    },
    autoHideMenuBar: true,
    menuBarVisible: false,
    icon: path.join(__dirname, "build", "icon.ico"),
    webPreferences: {
      preload: path.join(__dirname, 'preload.js'),
      nodeIntegration: false,
      contextIsolation: true,
    },
  });

  // and load the index.html of the app.
  mainWindow.loadFile(path.join(__dirname, 'page/index.html'));

  mainWindow.maximize();

  mainWindow.webContents.on("did-finish-load", () => {
    mainWindow.webContents.setZoomFactor(0.85);
  });

  // Open the DevTools.
  // mainWindow.webContents.openDevTools();
};

// This method will be called when Electron has finished
// initialization and is ready to create browser windows.
// Some APIs can only be used after this event occurs.
app.whenReady().then(() => {
  createWindow();

  // On OS X it's common to re-create a window in the app when the
  // dock icon is clicked and there are no other windows open.
  app.on('activate', () => {
    if (BrowserWindow.getAllWindows().length === 0) {
      createWindow();
    }
  });
});

// Quit when all windows are closed, except on macOS. There, it's common
// for applications and their menu bar to stay active until the user quits
// explicitly with Cmd + Q.
app.on('window-all-closed', () => {
  if (process.platform !== 'darwin') {
    app.quit();
  }
});

// In this file you can include the rest of your app's specific main process
// code. You can also put them in separate files and import them here.
