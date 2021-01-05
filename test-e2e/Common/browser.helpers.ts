import {binding} from "cucumber-tsflow";
import {By, ThenableWebDriver, until, WebElementPromise} from "selenium-webdriver";
import TestRuntime from "../runtime/TestRuntime";
import {before} from "cucumber-tsflow/dist";

type CssElementSelector = string;
type XPathElementSelector = string;
type ElementSelector = By | CssElementSelector | XPathElementSelector;

@binding()
class BrowserHelpers {
    public readonly driver: ThenableWebDriver;
    /** Timeout in milliseconds */
    private defaultTimeout: number = 5000;

    constructor() {
        this.driver = TestRuntime.instance.browserDriver;
    }

    @before()
    public async goToHomepage() {
        await this.loadPage('');
    }

    /**
     * Loads page by a relative or an absolute url
     *
     * If relative url is provided, uses `baseUrl` from TestRuntime options.
     *
     * @param {string} path - url to load
     * @param {number} [waitInMilliseconds] - number of seconds to wait for page to load
     * @returns {Promise} resolved when url has loaded otherwise rejects
     * @example
     *      helpers.loadPage('http://www.google.com');
     *      helpers.loadPage('/');
     */
    public async loadPage(path: string, waitInMilliseconds?: number) {
        if (!path.startsWith("http")) {
            let url = new URL(path, TestRuntime.instance.options.baseUrl);
            path = url.href;
        }

        await this.driver.get(path);
        await this.driver.wait(until.elementLocated(By.css('body')), (waitInMilliseconds || this.defaultTimeout));
    }

    /**
     * Returns the value of an attribute on an element
     *
     * @param {ElementSelector} selector
     * @param {string} attributeName
     * @returns {string} the value of the attribute or empty string if not found
     * @example
     *      helpers.getAttributeValue('body', 'class');
     */
    public async getAttributeValue(selector: ElementSelector, attributeName: string): Promise<string> {
        let el = await this.findElement(selector);
        return el.getAttribute(attributeName);
    }

    /**
     * Retrieves DOM element
     *
     * If the element is not present, waits for it's insertion up to specified or default timeout.
     *
     * @param {ElementSelector} elementSelector
     * @param {number} [waitInMilliseconds] - number of milliseconds to wait for the element
     * @returns {WebElementPromise} the matched element
     * @example
     *      helpers.findElement('#login-button', 5000);
     *      helpers.findElement('//span[contains(text(), "Hi!")]');
     */
    public findElement(elementSelector: ElementSelector, waitInMilliseconds?: number): WebElementPromise {
        let selector: By;
        if (elementSelector instanceof By) {
            selector = elementSelector;
        } else {
            // if the locator starts with '//' assume xpath, otherwise css
            selector = (elementSelector.indexOf('//') === 0)
                ? By.xpath(elementSelector)
                : By.css(elementSelector);
        }

        let timeout = waitInMilliseconds || this.defaultTimeout;
        let message = 'Element identified by "' + selector + '" could not be found within specified timeout of '
            + timeout + 'ms';
        return this.driver.wait(until.elementLocated(selector), timeout, message);
    }

    public clearCookies(): Promise<void> {
        return this.driver.manage().deleteAllCookies();
    }

    public clearStorages() {
        // console.warn("TODO: Implement storages clearing");
        return this.driver.executeScript('window.localStorage.clear(); window.sessionStorage.clear();')
    }

    public async clearCookiesAndStorages() {
        await this.clearCookies();
        await this.clearStorages();
    }

    async refresh() {
        await this.driver.navigate().refresh();
    }
}

export default BrowserHelpers;
