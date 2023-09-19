<template>
    <h1>Productos</h1>

    <Toolbar class="mb-4">
        <template #start>
            <Button
                label="Nuevo Producto"
                icon="pi pi-plus"
                severity="success"
                class="mr-2"
                @click="abrirNuevo"
            />
        </template>
        <template #end>
            <FileUpload
                mode="basic"
                accept="image/*"
                :maxFileSize="1000000"
                label="Import"
                chooseLabel="Import"
                class="mr-2 inline-block"
            />
            <Button
                label="Export"
                icon="pi pi-upload"
                severity="help"
                @click="exportCSV($event)"
            />
        </template>
    </Toolbar>

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
                <h4 class="m-0">Gestión Productos</h4>
                <span class="p-input-icon-left">
                    <i class="pi pi-search" />
                    <InputText placeholder="Buscar..." v-model="buscar" @keypress.enter="getProductos" />
                </span>
            </div>
        </template>

        <Column
            field="id"
            header="ID"
            sortable
            style="min-width: 2rem"
        ></Column>
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
        <Column field="precio" header="Precio" sortable style="min-width: 8rem">
            <template #body="slotProps">
                {{ formatCurrency(slotProps.data.precio) }}
            </template>
        </Column>

        <Column
            field="categoria_id"
            header="Categoria"
            sortable
            style="min-width: 10rem"
        ></Column>

        <Column :exportable="false" style="min-width: 8rem">
            <template #body="slotProps">
                <Button
                    icon="pi pi-pencil"
                    outlined
                    rounded
                    class="mr-2"
                    @click="editProduct(slotProps.data)"
                />
                <Button
                    icon="pi pi-trash"
                    outlined
                    rounded
                    severity="danger"
                    @click="confirmDeleteProduct(slotProps.data)"
                />
            </template>
        </Column>
    </DataTable>

    <!--
    <input type="text" placeholder="nombre" v-model="producto.nombre">
    <br>
    <input type="number" placeholder="precio" v-model="producto.precio">
    <br>
    <input type="number" placeholder="stock" v-model="producto.stock">
    <br>
    <input type="number" placeholder="categoria_id" v-model="producto.categoria_id">
    <br>
    <input type="text" placeholder="descripcion..." v-model="producto.descripcion">
    <br>
    <button @click="guardarProducto">Guardar</button>
    <br>
    -->
    PRODUCTO: {{ productos }} TOTAL: {{ totalRecords }}

    <Dialog
        v-model:visible="productDialog"
        :style="{ width: '450px' }"
        header="Detalles del producto"
        :modal="true"
        class="p-fluid"
    >
        <img
            v-if="producto.imagen"
            :src="`http://127.0.0.1:8000/${producto.imagen}`"
            :alt="producto.image"
            class="block m-auto pb-3"
        />
        <div class="field">
            <label for="name">Name</label>
            <InputText
                id="name"
                v-model.trim="producto.nombre"
                required="true"
                autofocus
                :class="{ 'p-invalid': submitted && !producto.nombre }"
            />
            <small class="p-error" v-if="submitted && !producto.nombre"
                >El nombre es obligatorio.</small
            >
        </div>
        <div class="field">
            <label for="description">Descripción</label>
            <Textarea
                id="description"
                v-model="producto.descripcion"
                required="true"
                rows="3"
                cols="20"
            />
        </div>

        <div class="field">
            <label class="mb-3">Categoria</label>
            <div class="formgrid grid">
                <div class="field-radiobutton col-6">
                    <RadioButton
                        id="category1"
                        name="category"
                        value="1"
                        v-model="producto.categoria_id"
                    />
                    <label for="category1">Accessories</label>
                </div>
                <div class="field-radiobutton col-6">
                    <RadioButton
                        id="category2"
                        name="category"
                        value="2"
                        v-model="producto.categoria_id"
                    />
                    <label for="category2">Clothing</label>
                </div>
                <div class="field-radiobutton col-6">
                    <RadioButton
                        id="category3"
                        name="category"
                        value="3"
                        v-model="producto.categoria_id"
                    />
                    <label for="category3">Electronics</label>
                </div>
                <div class="field-radiobutton col-6">
                    <RadioButton
                        id="category4"
                        name="category"
                        value="4"
                        v-model="producto.categoria_id"
                    />
                    <label for="category4">Fitness</label>
                </div>
            </div>
        </div>

        <div class="formgrid grid">
            <div class="field col">
                <label for="price">Precio</label>
                <InputNumber
                    id="price"
                    v-model="producto.precio"
                    mode="currency"
                    currency="USD"
                    locale="en-US"
                />
            </div>
            <div class="field col">
                <label for="quantity">Cantidad</label>
                <InputNumber
                    id="quantity"
                    v-model="producto.cantidad"
                    integeronly
                />
            </div>
        </div>
        <template #footer>
            <Button
                label="Cancelar"
                icon="pi pi-times"
                text
                @click="hideDialog"
            />
            <Button
                label="Guardar"
                icon="pi pi-check"
                text
                @click="guardarProducto"
            />
        </template>
    </Dialog>
</template>

<script setup>
import { ref, onMounted } from "vue";
import productoService from "../../../services/producto.service";

const productos = ref([]);
const producto = ref({
    nombre: "",
    precio: "",
    stock: 0,
    categoria_id: "",
    descripcion: "",
});
const totalRecords = ref(0);
const submitted = ref(false);
const productDialog = ref(false);
const loading = ref(false);
const lazyParams = ref({});
const buscar = ref("")

onMounted(() => {
    getProductos();
});

const onPage = (event) => {
    lazyParams.value = event;
    getProductos();
}

const getProductos = async () => {
    try {
        loading.value = true;
        let page = lazyParams.value.page + 1;
        let limit = lazyParams.value.rows;

        const { data } = await productoService.listar(page, limit, buscar.value);
        productos.value = data.data;
        totalRecords.value = data.total;

        loading.value = false
    } catch (error) {
        console.log("error: ", error);
    }
};

const guardarProducto = async () => {
    try {
        const { data } = await productoService.guardar(producto.value);

        hideDialog();
        getProductos();
    } catch (error) {
        console.log("error: ", error);
    }
};

const abrirNuevo = () => {
    producto.value = {
        nombre: "",
        precio: "",
        stock: 0,
        categoria_id: "",
        descripcion: "",
    };
    submitted.value = false;
    productDialog.value = true;
};

const hideDialog = () => {
    productDialog.value = false;
    submitted.value = false;
};

const exportCSV = () => {
    dt.value.exportCSV();
};

const formatCurrency = (value) => {
    if (value)
        return value.toLocaleString("es-US", {
            style: "currency",
            currency: "USD",
        });
    return;
};
</script>
