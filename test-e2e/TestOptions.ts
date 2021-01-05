import * as path from "path";
import {InitOptions} from "./runtime/TestRuntime";

export function getRuntimeOptions(): InitOptions {
    return {
        baseUrl: "http://paf.local",
        testOutDirectory: path.join(__dirname, "../test-out"),
        testFilesDirectory: path.join(__dirname, "files"),
        browserName: "firefox",
    };
}
