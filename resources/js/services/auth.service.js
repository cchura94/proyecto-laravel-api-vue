import axios from "./axios-interceptor";

export function login(datos) {
    return axios.post("/v1/auth/login", datos);
}

export function registro(datos) {
    return axios.post("/v1/auth/register", datos);
}