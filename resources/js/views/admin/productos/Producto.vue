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
            <Button
                label="EXCEL"
                icon="pi pi-upload"
                severity="info"
                @click="generarExcel()"
            />
            <Button
                label="PDF"
                icon="pi pi-upload"
                severity="help"
                @click="generarPdf()"
            />
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
                    <InputText
                        placeholder="Buscar..."
                        v-model="buscar"
                        @keypress.enter="getProductos"
                    />
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
            field="categoria.nombre"
            header="Categoria"
            sortable
            style="min-width: 10rem"
        ></Column>

        <Column :exportable="false" style="min-width: 8rem">
            <template #body="slotProps">
                <Button
                    icon="pi pi-image"
                    outlined
                    rounded
                    class="mr-2"
                    severity="warning"
                    @click="dialogImagenProducto(slotProps.data)"
                />
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
    TOTAL: {{ totalRecords }}

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
            <label for="name">Nombre</label>
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
                <div
                    class="field-radiobutton col-6"
                    v-for="cat in categorias"
                    :key="cat.id"
                >
                    <RadioButton
                        :id="`category${cat.id}`"
                        name="category"
                        :value="cat.id"
                        v-model="producto.categoria_id"
                    />
                    <label :for="`category${cat.id}`">{{ cat.nombre }}</label>
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
                    v-model="producto.stock"
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

    <Dialog
        v-model:visible="dialogImagen"
        modal
        header="Imagen"
        :style="{ width: '50vw' }"
    >
        <FileUpload
            customUpload
            @uploader="actualizarImagen"
            :multiple="false"
            accept="image/*"
            :maxFileSize="1000000"
        >
            <template #empty>
                <p>Arrastrar y Soltar aquí, para subir archivos.</p>
            </template>
        </FileUpload>
    </Dialog>
</template>

<script setup>
import { ref, onMounted } from "vue";
import productoService from "../../../services/producto.service";
import categoriaService from "../../../services/categoria.service";
// import XLSX from 'xlsx'

import { jsPDF } from "jspdf";
import "jspdf-autotable";

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
const buscar = ref("");

const dialogImagen = ref(false);
const categorias = ref([]);
const dt = ref(null);

onMounted(() => {
    getProductos();
    getCategorias();
});

const onPage = (event) => {
    lazyParams.value = event;
    getProductos();
};

const getProductos = async () => {
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

const getCategorias = async () => {
    const { data } = await categoriaService.listar();
    categorias.value = data;
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

const dialogImagenProducto = (prod) => {
    producto.value = prod;
    dialogImagen.value = true;
};

const actualizarImagen = async (e) => {
    console.log(e.files[0]);

    try {
        let fd = new FormData();
        fd.append("imagen", e.files[0]);

        const { data } = await productoService.actualizarImagen(
            producto.value.id,
            fd
        );

        producto.value = {
            nombre: "",
            precio: "",
            stock: 0,
            categoria_id: "",
            descripcion: "",
        };
        dialogImagen.value = false;
        getProductos();
    } catch (error) {
        console.log(error);
    }
};

const generarPdf = () => {
    const doc = new jsPDF();
    const header = [["ID", "Nombre", "Precio", "Stock", "Categoría"]];

    // Títulos
    doc.setFontSize(18);
    doc.text("Reporte de Productos", 20, 20);

    // Espacio después del título
    doc.setFontSize(12);
    doc.text("A continuación se muestra la lista de productos:", 20, 30);

    const data = productos.value.map((producto) => [
        producto.id,
        producto.nombre,
        `$${producto.precio}`,
        producto.stock,
        producto.categoria.nombre,
    ]);

    const tableConfig = {
        head: header,
        body: data,
        theme: "striped",
        styles: { cellPadding: 5, fontSize: 12 },
        startY: 40, // Posición inicial de la tabla (después de los títulos)
    };

    doc.autoTable(tableConfig);
    // Convertir el documento a Blob
    const pdfBlob = doc.output("blob");

    // Crear una URL para el Blob
    const pdfUrl = URL.createObjectURL(pdfBlob);

    // Abrir el PDF en una nueva pestaña
    window.open(pdfUrl, "_blank");

    // Liberar la URL cuando la pestaña se cierre
    window.addEventListener("unload", () => {
        URL.revokeObjectURL(pdfUrl);
    });
};

/*
const generarExcel = () => {
    const data = [
        ['NOmbre', 'Precio'],
        ['Pruebas', 300],
        ['Pruebas2', 400],
        ['Pruebas3', 500]
    ]
    const ws = XLSX.utils.aoa_to_sheet(data);
    const wb = XLSX.utils.book_new();

    XLSX.utils.book_append_sheet(wb, ws, 'Datos');

    XLSX.writeFile(wb, 'datos.xlsx');
}
*/
</script>
