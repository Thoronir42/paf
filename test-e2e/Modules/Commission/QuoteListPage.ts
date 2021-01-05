import BrowserHelpers from "../../Common/browser.helpers";


export default class QuoteListPage {
    constructor(private browser: BrowserHelpers) {

    }

    public async getQuoteByName(name: string) {
        let selector = '//div[contains(@class, "quote-overview") and .//h2[.="' + name + '"]]';
        let quotes = await this.browser.driver.findElements({xpath: selector});
        if (!quotes.length) {
            throw new Error('No .quote-overview with caption "' + name +'" found.');
        }

        if (quotes.length > 1) {
            throw new Error('Multiple .quote-overview with caption "' + name +'" found.');
        }

        return quotes[0];
    }
}
