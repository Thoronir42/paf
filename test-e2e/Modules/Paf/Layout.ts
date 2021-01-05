import BrowserHelpers from "../../Common/browser.helpers";

class Layout {
    constructor(private browser: BrowserHelpers) {
    }

    get navSignInButton() {
        return this.browser.findElement('[href*="sign-in"]');
    }

    get navSignOutButton() {
        return this.browser.findElement('[href*="sign-out"]');
    }

    public getNavItemByLabel(label: string) {
        return this.browser.findElement('//nav//a[contains(.,"' + label + '")]')
    }
}

export default Layout;
