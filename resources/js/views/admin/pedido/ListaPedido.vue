<template>
    <div class="card">
        <DataTable :value="pedidos" tableStyle="min-width: 50rem">
            <Column field="id" header="ID"></Column>
            <Column field="fecha" header="FECHA"></Column>
            <Column field="observacion" header="OBS"></Column>
            <Column field="cliente.nombre_completo" header="cliente"></Column>

            <Column :exportable="false" style="min-width: 8rem">
                <template #body="slotProps">
                    <Button
                        icon="pi pi-box"
                        outlined
                        rounded
                        class="mr-2"
                        severity="info"
                        @click="dialogDetalle(slotProps.data)"
                    />
                </template>
            </Column>
        </DataTable>
    </div>

    <Dialog
        v-model:visible="dialog_visible"
        modal
        header="Header"
        :style="{ width: '50vw' }"
    >
        <div class="p-col-12 recibo" v-if="pedido.id">
            <div class="p-grid p-justify-between">
                <div class="p-col-12 p-text-center">
                    <h2>Recibo de Compra</h2>
                </div>
            </div>
            <div class="p-grid p-justify-between p-mb-2">
                <div class="p-col-6">ID: #{{pedido.id}}</div>
                <div class="p-col-6 p-text-right">
                    Fecha: {{pedido.fecha}}
                </div>
                <div class="p-col-6">Código de Pedido: {{pedido.cod_pedido}}</div>
                <div class="p-col-6 p-text-right">Estado: {{pedido.estado}}</div>
                <div class="p-col-6">Observación: {{pedido.observacion}}</div>
                <div class="p-col-6 p-text-right">Cliente: {{pedido.cliente.nombre_completo}}</div>
                <div class="p-col-6">Cédula/NIT: {{ pedido.cliente.ci_nit }}</div>
                <div class="p-col-6 p-text-right">Teléfono: {{ pedido.cliente.telefono }}</div>
            </div>
            <div class="p-grid p-justify-between p-mb-2">
                <div class="p-col-12">
                    <h3>Productos:</h3>
                    <ul >
                        <li v-for="prod in pedido.productos" :key="prod.id">{{ prod.nombre }} - {{prod.precio}}</li>
                    </ul>
                </div>
            </div>
            <div class="p-grid p-justify-end">
                <div class="p-col-6 p-text-right">Total: $1,350.00</div>
            </div>
        </div>
    </Dialog>
</template>

<script setup>
import { onMounted, ref } from "vue";
import pedidoService from "../../../services/pedido.service";

const pedidos = ref([]);
const pedido = ref({});
const dialog_visible = ref(false);

onMounted(() => {
    listarPedidos();
});
const listarPedidos = async () => {
    const { data } = await pedidoService.listar();
    pedidos.value = data;
};

const dialogDetalle = (ped) => {
    pedido.value = ped;
    dialog_visible.value = true;
};
</script>

<style scoped>
.recibo {
    width: 300px;
    padding: 20px;
    margin: 0 auto;
}
</style>
