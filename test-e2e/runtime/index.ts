import {ThenableWebDriver} from "selenium-webdriver";

export type BrowserName = 'firefox' | 'phantomjs' | 'chrome' | string;

/**
 * Create the Selenium browser driver
 *
 * Uses dynamic importing so that only chosen browser driver code gets included.
 *
 * @returns Promise containing object with {@link ThenableWebDriver} so that the driver type does not
 *          get unwrapped in the {@link Promise} chain.
 */
export function getDriverInstance(browserName: BrowserName = 'firefox'): Promise<{ driver: ThenableWebDriver }> {
    switch (browserName) {
        case 'firefox':
            return import("./firefoxDriver").then((module) => ({driver: module.createFirefoxDriver()}));
        case 'phantomjs':
            return import("./phantomDriver").then((module) => ({driver: module.createPhantomJsDriver()}));
        case 'chrome':
            return import("./chromeDriver").then((module) => ({driver: module.createChromeDriver()}));
    }

    throw new Error(`Driver of name '${browserName}' is not supported`);
}
