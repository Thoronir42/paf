import {ThenableWebDriver} from "selenium-webdriver";
import {BrowserName, getDriverInstance} from ".";
import * as fs from "fs-extra";
import * as path from "path";
import ApiAdapter from "./ApiAdapter";

let _instance: TestRuntime;

export type InitOptions = {
    baseUrl: string,
    testOutDirectory: string,
    testFilesDirectory: string,
    browserName?: BrowserName,
    authorizationHeader?: string,
}

export default class TestRuntime {
    private _api: ApiAdapter;
    private _browserDriver: ThenableWebDriver;

    private constructor(public readonly options: InitOptions) {
    }

    public async captureScreenshot(name: string): Promise<{ filePath, data }> {
        if (!this._browserDriver) {
            console.error("Can not take screenshot, browserDriver not initialized");
            return null;
        }
        let fileDataB64 = await this.browserDriver.takeScreenshot();
        let filePath = path.join(this.options.testOutDirectory, 'screenshots', name + ".png");

        let data = Buffer.from(fileDataB64, 'base64');
        await new Promise((resolve, reject) => fs.writeFile(filePath, data, (err) => err ? reject(err) : resolve(undefined)));

        return {filePath, data};
    }

    public async ensureBrowserDriverInitialized(): Promise<void> {
        if (this._browserDriver) {
            return;
        }
        let browser = await getDriverInstance(this.options.browserName || "firefox");
        this._browserDriver = browser.driver;
    }

    public async shutdown() {
        if (this._browserDriver) {
            await this._browserDriver.close();
        }
    }

    public get browserDriver(): ThenableWebDriver {
        if (!this._browserDriver) {
            throw new Error("Browser driver not initialized");
        }
        return this._browserDriver;
    }

    public get api(): ApiAdapter {
        if (!this._api) {
            let url = new URL('api', this.options.baseUrl);
            this._api = new ApiAdapter(url.href, this.options.authorizationHeader);

        }

        return this._api
    }

    public static initialize(options: InitOptions): TestRuntime {
        if (_instance) {
            throw new Error("Already initialized");
        }
        return _instance = new TestRuntime(options);
    }

    public static get instance(): TestRuntime {
        if (!_instance) {
            throw new Error("TestRuntime instance not initialized. Call TestRuntime.initialize first");
        }
        return _instance;
    }
};
