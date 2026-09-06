<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { ArrowLeft, BarChart3, ClipboardList, CircleDollarSign, ChevronRight, Package, TrendingUp, Users } from '@lucide/vue'
import { RouterLink } from 'vue-router'
import { getAdminOrders, getAdminOverview, updateAdminOrderStatus, type AdminOrder } from '../services/adminService'

const tab = ref('Resumen')
const tabs = ['Resumen', 'Pedidos', 'Productos']
const statuses = [{ value: 'pending', label: 'Pendiente' }, { value: 'processing', label: 'En proceso' }, { value: 'shipped', label: 'Enviado' }, { value: 'delivered', label: 'Entregado' }, { value: 'cancelled', label: 'Cancelado' }]
const overview = ref({ users: 0, products: 0, orders: 0, sales: 0, averageOrder: 0 })
const orders = ref<AdminOrder[]>([])
const errorMessage = ref('')
const money = (value: number) => `$${value.toLocaleString('es-MX')} MXN`
const statusLabel = (status: string) => statuses.find((item) => item.value === status)?.label ?? status
const load = async () => {
  try {
    const [summary, orderResponse] = await Promise.all([getAdminOverview(), getAdminOrders()])
    overview.value = summary.data
    orders.value = orderResponse.data.data
  } catch (error) { errorMessage.value = error instanceof Error ? error.message : 'No se pudo cargar el panel.' }
}
const changeStatus = async (order: AdminOrder, status: string) => {
  try { order.status = (await updateAdminOrderStatus(order.id, status)).data.status } catch { errorMessage.value = 'No se pudo actualizar el pedido.' }
}
onMounted(load)
</script>

<template>
  <div class="admin-page"><header class="admin-header"><RouterLink to="/" class="admin-brand">grasse<span>.</span><small>ADMIN</small></RouterLink><nav><button v-for="item in tabs" :key="item" :class="{ active: tab === item }" @click="tab = item">{{ item }}</button></nav><RouterLink to="/" class="admin-user"><ArrowLeft :size="14" /> Tienda</RouterLink></header><main class="admin-content"><div class="admin-heading"><div><p class="eyebrow">Panel de control</p><h1>{{ tab }}</h1></div><span class="admin-date">DATOS EN VIVO</span></div><p v-if="errorMessage" class="auth-error" role="alert">{{ errorMessage }}</p>
    <template v-if="tab === 'Resumen'"><div class="admin-stats"><div><CircleDollarSign :size="18" /><span>Ventas</span><strong>{{ money(overview.sales) }}</strong><em>Total procesado</em></div><div><ClipboardList :size="18" /><span>Pedidos</span><strong>{{ overview.orders }}</strong><em>Registrados</em></div><div><Users :size="18" /><span>Clientes</span><strong>{{ overview.users }}</strong><em>Usuarios</em></div><div><BarChart3 :size="18" /><span>Ticket promedio</span><strong>{{ money(overview.averageOrder) }}</strong><em>Pedidos procesados</em></div></div><section class="admin-card"><div class="admin-card-heading"><div><p class="eyebrow">Operación</p><h2>Pedidos recientes</h2></div><button class="text-link" @click="tab = 'Pedidos'">Ver todos <ChevronRight :size="14" /></button></div><div class="order-row" v-for="order in orders.slice(0, 5)" :key="order.id"><div class="order-icon"><Package :size="17" /></div><div class="order-info"><strong>GR-{{ String(order.id).padStart(6, '0') }}</strong><span>{{ order.user.name }} · {{ order.items.length }} artículo(s)</span></div><span class="order-status">{{ statusLabel(order.status) }}</span><b>{{ money(order.total) }}</b></div></section></template>
    <template v-else-if="tab === 'Pedidos'"><section class="admin-card"><div class="admin-card-heading"><div><p class="eyebrow">Gestión</p><h2>Pedidos reales</h2></div><TrendingUp :size="17" /></div><p v-if="!orders.length">No hay pedidos registrados.</p><div class="order-row" v-for="order in orders" :key="order.id"><div class="order-icon"><Package :size="17" /></div><div class="order-info"><strong>GR-{{ String(order.id).padStart(6, '0') }}</strong><span>{{ order.user.name }} · {{ order.user.email }} · {{ new Date(order.created_at).toLocaleDateString('es-MX') }}</span></div><select :value="order.status" @change="changeStatus(order, ($event.target as HTMLSelectElement).value)"><option v-for="status in statuses" :key="status.value" :value="status.value">{{ status.label }}</option></select><b>{{ money(order.total) }}</b></div></section></template>
    <template v-else><section class="admin-card"><p class="eyebrow">Catálogo</p><h2>{{ overview.products }} productos registrados</h2><p>CRUD de productos y stock sigue pendiente.</p></section></template>
  </main></div>
</template>
