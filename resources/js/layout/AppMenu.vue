<script setup>
import { ref } from 'vue';

import AppMenuItem from './AppMenuItem.vue';

const model = ref([
    {
        label: 'Admin',
        items: [
            { label: 'Administrador', icon: 'pi pi-fw pi-home', to: '/admin' },
            { label: 'Perfil', icon: 'pi pi-fw pi-user', to: '/admin/perfil' }
        ]
    },
    {
        label: 'Gestión',
        items: [
            { label: 'Usuarios', icon: 'pi pi-fw pi-id-card', to: '/admin/usuario' },
            { label: 'Categoria', icon: 'pi pi-fw pi-check-square', to: '/admin/categoria' },
            { label: 'Producto', icon: 'pi pi-fw pi-bookmark', to: '/admin/producto' },
            { label: 'Invalid State', icon: 'pi pi-fw pi-exclamation-circle', to: '/uikit/invalidstate' },
           
        ]
    },
    {
        label: 'Pedidos',
        items: [
            { label: 'Nuevo Pedido', icon: 'pi pi-fw pi-eye', to: '/admin/pedido/nuevo', badge: 'NEW' },
            { label: 'Lista Pedidos', icon: 'pi pi-fw pi-globe', to: '/admin/pedido' }
        ]
    }
]);
</script>

<template>
    <ul class="layout-menu">
        <template v-for="(item, i) in model" :key="item">
            <app-menu-item v-if="!item.separator" :item="item" :index="i"></app-menu-item>
            <li v-if="item.separator" class="menu-separator"></li>
        </template>
        
    </ul>
</template>

<style lang="scss" scoped></style>
