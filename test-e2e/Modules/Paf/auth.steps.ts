import {binding, given, when} from "cucumber-tsflow";
import Layout from "./Layout";
import BrowserHelpers from "../../Common/browser.helpers";
import SignPage from "./SignPage";

@binding([BrowserHelpers])
class AuthSteps {

    private layout: Layout;
    private signPage: SignPage;

    constructor(
        private browser: BrowserHelpers,
    ) {
        this.layout = new Layout(browser);
        this.signPage = new SignPage(browser);
    }

    @when('I sign in as {string}')
    public async signInAs(credentials: string) {
        let [login, password] = credentials.split(':', 2);

        let signedInAs = await this.browser.findElement('.nav-sign-link').getAttribute('title');
        if (signedInAs.includes(login)) {
            return;
        }
        if (signedInAs) {
            await this.layout.navSignOutButton.click();
        }

        await this.layout.navSignInButton.click();
        await this.signPage.signIn(login, password);
    }

    @when('I sign out')
    public async whenSignOut() {
        await this.layout.navSignOutButton.click();
    }
}

export default AuthSteps;
