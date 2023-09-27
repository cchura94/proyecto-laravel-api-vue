import {http} from "./axios-interceptor";

export default {
    buscarCliente(q=""){
        return http().get(`/cliente/buscar?q=${q}`);
    },
    guardar(datos){
        return http().post("/cliente", datos);
    },
    mostrar(id){
        return http().get(`/cliente/${id}`);
    },
    modificar(id, datos){
        return http().put(`/cliente/${id}`, datos);
    },
    eliminar(id){
        return http().delete(`/cliente/${id}`);
    }
}