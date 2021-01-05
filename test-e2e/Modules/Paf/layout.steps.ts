import {binding, given} from "cucumber-tsflow";
import Layout from "./Layout";
import BrowserHelpers from "../../Common/browser.helpers";

@binding([BrowserHelpers])
class AuthSteps {
    private layout: Layout;

    constructor(
        private browser: BrowserHelpers,
    ) {
        this.layout = new Layout(browser);
    }

    @given('I navigate to {string}')
    public async navigateTo(label: string) {
        for (let part of label.split(' > ')) {
            await this.layout.getNavItemByLabel(part).click()
        }
    }
}

export default AuthSteps;
