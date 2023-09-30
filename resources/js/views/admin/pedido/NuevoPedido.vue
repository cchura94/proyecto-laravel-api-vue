<template>
    <div class="grid">
        <div class="col-7">
            <DataTable
                ref="dt"
                :value="productos"
                dataKey="id"
                :paginator="true"
                :rows="10"
                lazy
                :totalRecords="totalRecords"
                :loading="loading"
                @page="onPage($event)"
                paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                :rowsPerPageOptions="[5, 10, 25]"
                currentPageReportTemplate="Mostrando {first} al {last} de {totalRecords} productos"
            >
                <template #header>
                    <div
                        class="flex flex-wrap gap-2 align-items-center justify-content-between"
                    >
                        <h4 class="m-0">Productos</h4>
                        <span class="p-input-icon-left">
                            <i class="pi pi-search" />
                            <InputText
                                placeholder="Buscar..."
                                v-model="buscar"
                                @keypress.enter="listaProductos"
                            />
                        </span>
                    </div>
                </template>
                <Column
                    field="nombre"
                    header="NOMBRE"
                    sortable
                    style="min-width: 16rem"
                ></Column>
                <Column header="Imagen">
                    <template #body="slotProps">
                        <img
                            :src="`http://127.0.0.1:8000/${slotProps.data.imagen}`"
                            :alt="slotProps.data.imagen"
                            class="shadow-2 border-round"
                            style="width: 64px"
                            v-if="slotProps.data.imagen"
                        />
                    </template>
                </Column>
                <Column
                    field="precio"
                    header="Precio"
                    sortable
                    style="min-width: 8rem"
                >
                    <template #body="slotProps">
                        {{ formatCurrency(slotProps.data.precio) }}
                    </template>
                </Column>

                <Column
                    field="categoria.nombre"
                    header="Categoria"
                    sortable
                    style="min-width: 10rem"
                ></Column>

                <Column :exportable="false" style="min-width: 8rem">
                    <template #body="slotProps">
                        <Button
                            icon="pi pi-plus"
                            outlined
                            rounded
                            class="mr-2"
                            severity="warning"
                            @click="addCarrito(slotProps.data)"
                        />
                    </template>
                </Column>
            </DataTable>
        </div>
        <div class="col-5">
            <div class="grid">
                <div class="col-12">
                    <div
                        class="text-center p-1 border-round-sm bg-primary font-bold"
                    >
                        <DataTable :value="carrito">
                            <Column field="nombre" header="NOMBRE"></Column>
                            <Column field="precio" header="PRECIO"></Column>
                            <Column :exportable="false" header="C">
                                <template #body="slotProps">
                                    <Button
                                            label="+"
                                            severity="info"
                                            class=""
                                            @click="aumentar(slotProps.data)"
                                        />
                                        {{ slotProps.data.cantidad }}
                                    <Button
                                            label="-"
                                            severity="info"
                                            class=""
                                            @click="reducir(slotProps.data)"
                                        />
                                </template>
                            </Column>
                            <Column :exportable="false" style="min-width: 8rem">
                                <template #body="slotProps">
                                    <Button
                                        icon="pi pi-trash"
                                        outlined
                                        rounded
                                        class="mr-1"
                                        severity="danger"
                                        @click="quitarCarrito(slotProps.data)"
                                    />
                                </template>
                            </Column>
                        </DataTable>
                    </div>
                </div>
                <div class="col-12">
                    <div class="p-1 border-round-sm bg-primary font-bold">
                        <span class="p-input-icon-left">
                            <i class="pi pi-search" />
                            <InputText
                                placeholder="Buscar..."
                                v-model="buscarClie"
                                @keypress.enter="getCliente"
                            />
                        </span>

                        <Button
                            label="Nuevo Cliente"
                            severity="success"
                            class="ml-3"
                            @click="visible = true"
                        />
                    </div>
                    <div v-if="cliente_seleccionado">
                        <h5>NOMBRE CLIENTE: {{cliente_seleccionado.nombre_completo}}</h5>
                        <h5>CI / NIT: {{cliente_seleccionado.ci_nit}}</h5>
                        <h5>TELEFONO: {{cliente_seleccionado.telefono}}</h5>
                    </div>
                </div>
                <div class="col-12">
                    <div
                        class="text-center p-1 border-round-sm bg-primary font-bold"
                    >
                    <Button
                            label="Guardar Pedido"
                            severity="info"
                            class=""
                            @click="guardarPedido"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>

        <Dialog v-model:visible="visible" modal header="Header" :style="{ width: '50vw' }" class="p-fluid">
            <div class="field">
            <label for="name">Nombre</label>
            <InputText
                id="name"
                v-model.trim="cliente.nombre_completo"
                required="true"
                autofocus
                :class="{ 'p-invalid': submitted && !cliente.nombre_completo }"
            />
            <small class="p-error" v-if="submitted && !producto.nombre"
                >El nombre es obligatorio.</small
            >
        </div>
        <div class="field">
            
            <label for="ci_nit">Ci/NIT</label>
            <InputText
                id="ci_nit"
                v-model.trim="cliente.ci_nit"
                required="true"
                autofocus
                :class="{ 'p-invalid': submitted && !cliente.nombre_completo }"
            />
        </div>

        <div class="field">
            
            <label for="ci_nit">TELEFONO</label>
            <InputText
                id="telefono"
                v-model.trim="cliente.telefono"
                required="true"
                autofocus
                :class="{ 'p-invalid': submitted && !cliente.telefono }"
            />
        </div>
        <template #footer>
            <Button
                label="Cancelar"
                icon="pi pi-times"
                text
                @click="visible = false"
            />
            <Button
                label="Guardar"
                icon="pi pi-check"
                text
                @click="guardarCliente"
            />
        </template>
        </Dialog>

</template>

<script setup>
import { ref, onMounted } from "vue";
import productoService from "../../../services/producto.service";
import clienteService from "../../../services/cliente.service"
import pedidoService from "../../../services/pedido.service"

const productos = ref([]);
const totalRecords = ref(0);
const lazyParams = ref({});
const loading = ref(false);
const dt = ref(null);
const buscar = ref("");
const carrito = ref([]);
const cliente_seleccionado = ref(null)
const visible = ref(false)
const cliente = ref({})

const buscarClie = ref("");

onMounted(() => {
    listaProductos();
});

const listaProductos = async () => {
    try {
        loading.value = true;
        let page = lazyParams.value.page + 1;
        let limit = lazyParams.value.rows;

        const { data } = await productoService.listar(
            page,
            limit,
            buscar.value
        );
        productos.value = data.data;
        totalRecords.value = data.total;

        loading.value = false;
    } catch (error) {
        console.log("error: ", error);
    }
};

const onPage = (event) => {
    lazyParams.value = event;
    listaProductos();
};

const formatCurrency = (value) => {
    if (value)
        return value.toLocaleString("es-US", {
            style: "currency",
            currency: "USD",
        });
    return;
};

const addCarrito = (prod) => {
    carrito.value.push({
        id: prod.id,
        nombre: prod.nombre,
        precio: prod.precio,
        stock: prod.stock,
        cantidad: 1,
    });
};

const quitarCarrito = (prod) => {};

const getCliente = async () => {
    const {data} = await clienteService.buscarCliente(buscarClie.value);
    if(data.id){
        cliente_seleccionado.value = data;  
    }
};

const guardarCliente = async () => {
    const {data} = await clienteService.guardar(cliente.value)
    cliente_seleccionado.value = data.cliente;
    visible.value = false;
}

const guardarPedido = async () => {
    if(confirm("¿Está seguro de guardar el Pedido?")){

        try {
            const datos = {
                cliente_id: cliente_seleccionado.value.id,
                productos: carrito.value
            }
    
            const {data} = await pedidoService.guardar(datos)
            console.log(data)
            cliente_seleccionado.value = null;
            carrito.value = [];
            
        } catch (error) {
            alert("Ocurrio un problema al registrare el pedido")
        }

    }
}

const aumentar = (prod) => {
    prod.cantidad++;
}
const reducir = (prod) => {
    prod.cantidad--;
}
</script>
