import * as path from "path";
import {InitOptions} from "./runtime/TestRuntime";

// Indefinite token for 'testing-robot' user
const authToken = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6IlpoZDhrRSIsInJvbGVzIjpbInVzZXIiXSwic291cmNlIjoiY2xpIn0.yvlBQEmsto8NzSRyskpODfQ1c3_cUpIuhju7fE5iE4c';

export function getRuntimeOptions(): InitOptions {
    return {
        baseUrl: "http://paf.local",
        testOutDirectory: path.join(__dirname, "../test-out"),
        testFilesDirectory: path.join(__dirname, "files"),
        browserName: "firefox",
        authorizationHeader: 'Bearer ' + authToken,
    };
}
