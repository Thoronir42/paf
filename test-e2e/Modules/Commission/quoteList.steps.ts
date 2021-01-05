import {binding, then} from "cucumber-tsflow/dist";
import BrowserHelpers from "../../Common/browser.helpers";
import QuoteListPage from "./QuoteListPage";

@binding([BrowserHelpers])
export default class QuoteListSteps {
    private quoteListPage: QuoteListPage;

    public constructor(private browser: BrowserHelpers) {
        this.quoteListPage = new QuoteListPage(browser);
    }

    @then(/^I (accept|reject) quote "([^"]+)"$/)
    public async function(resolution: string, name: string) {
        let quote = await this.quoteListPage.getQuoteByName(name);
        await quote.findElement({xpath: '//a[contains(@href, "' + resolution + '")]'}).click();
    }
}
