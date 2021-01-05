import fetch, {Response, RequestInit} from "node-fetch";

type HttpMethod = 'get' | 'post' | 'put' | 'delete' | string;
type RequestBody = string | Object;

interface ResponseError extends Error {
    response?: Response
}

export default class ApiAdapter {

    private readonly apiBase: URL;

    constructor(baseUrl: string | URL) {
        this.apiBase = baseUrl instanceof URL ? baseUrl : new URL(baseUrl);
    }

    public async get(path: string, options?: RequestInit): Promise<any> {
        return this.request('get', path, options);
    }

    public async post(path: string, body: RequestBody, options?: RequestInit): Promise<Response> {
        return this.request('post', path, options, body);
    }

    public async put(path: string, body: RequestBody, options?: RequestInit): Promise<Response> {
        return this.request('put', path, options, body);
    }

    public async delete(path: string, body: RequestBody, options?: RequestInit): Promise<Response> {
        return this.request('delete', path, options);
    }

    public async request(method: HttpMethod, path: string, opts?: RequestInit, body?: RequestBody): Promise<Response> {
        let options: RequestInit = opts ? {...opts} : {};
        options.method = method;
        if (typeof body === "object") {
            body = JSON.stringify(body);
        }
        if (body && typeof body !== "string") {
            console.warn("Body is not a string");
            body = undefined;
        }
        if (typeof body === "string") {
            options.body = body;
        }

        let url = new URL(this.apiBase.href);
        url.pathname = url.pathname + '/' + path;

        let response = await fetch(url.href, options);
        if (response.status >= 400) {
            let error: ResponseError = new Error("Server returned response with code " + response.status
                + " to request on " + url.href);
            error.response = response;
            throw error;
        }
        return response;
    }
};
