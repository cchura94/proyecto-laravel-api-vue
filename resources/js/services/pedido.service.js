import {http} from "./axios-interceptor";

export default {
    listar(page=1, limit=10, q="", order=""){
        return http().get(`/pedido?page=${page}&limit=${limit}&q=${q}&orderby=${order}`);
    },
    guardar(datos){
        return http().post("/pedido", datos);
    },
    mostrar(id){
        return http().get(`/pedido/${id}`);
    }
}