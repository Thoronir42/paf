import * as path from "path";
import {InitOptions} from "./runtime/TestRuntime";

// Indefinite token for 'testing-robot' user
const authToken = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6Ik5ZRThjNiIsInJvbGVzIjpbInVzZXIiLCJwb3dlci11c2VyIl0sInNvdXJjZSI6ImNsaSJ9.rkhXUekr2eyJJKxZdBvGDLnkm3wWGXvNKHeX2NlFT9Y';

export function getRuntimeOptions(): InitOptions {
    return {
        baseUrl: "http://paf.local",
        testOutDirectory: path.join(__dirname, "../test-out"),
        testFilesDirectory: path.join(__dirname, "files"),
        browserName: "firefox",
        authorizationHeader: 'Bearer ' + authToken,
    };
}
