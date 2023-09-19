import {http} from "./axios-interceptor";

export default {
    listar(page=1, limit=10, q="", order=""){
        return http().get(`/producto?page=${page}&limit=${limit}&q=${q}&orderby=${order}`);
    },
    guardar(datos){
        return http().post("/producto", datos);
    },
    mostrar(id){
        return http().get(`/producto/${id}`);
    },
    modificar(id, datos){
        return http().put(`/producto/${id}`, datos);
    },
    eliminar(id){
        return http().delete(`/producto/${id}`);
    },
}