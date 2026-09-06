# Grasse: Plan del Proyecto

## Resumen

Grasse será un e-commerce de perfumes para México, en español y con precios en MXN. El MVP usará datos de prueba y se desarrollará primero como frontend para validar la experiencia y definir los contratos de la API.

## Arquitectura

- Backend: Laravel como API REST.
- Frontend: Vue 3 SPA con TypeScript y Vite.
- Base de datos: PostgreSQL.
- Autenticación: Laravel Sanctum.
- Pagos: Stripe en modo prueba mediante Checkout y webhooks.
- Un rol administrativo en el MVP.
- El frontend comenzará con datos mock y después se conectará a Laravel mediante servicios HTTP.

## Alcance Del MVP

### Tienda

- Catálogo de perfumes con variantes por presentación.
- Búsqueda, categorías, filtros, ordenamiento y paginación.
- Tarjetas y detalle de producto con nombre, precio, stock, calificación, cantidad de reseñas, descripción e imagen.
- Carrito de compras y wishlist.
- Checkout para usuarios registrados e invitados.
- Gestión de domicilios, incluyendo domicilio predeterminado.
- Pedidos con estados: pendiente, en proceso, enviado, entregado y cancelado.

### Usuarios

- Registro, inicio y cierre de sesión.
- Recuperación y restablecimiento de contraseña por correo.
- Verificación de correo electrónico.
- Edición de perfil y domicilios.
- Historial de pedidos y resumen de actividad.
- Reseñas únicamente para compradores verificados.
- Un usuario puede publicar varios comentarios, pero solo una calificación por producto.

### Administración

- Resumen de ventas y pedidos.
- Visualización y actualización del estado de pedidos.
- CRUD de productos y variantes.
- Gestión y visualización de stock.
- Indicadores de stock: verde para más de 50, amarillo de 26 a 50 y rojo de 0 a 25.
- Ranking de productos más vendidos.

## Orden De Implementación

1. Definir identidad visual y componentes base.
2. Implementar layout, navegación y páginas principales con datos mock.
3. Implementar catálogo, filtros, búsqueda, paginación y detalle de producto.
4. Implementar carrito, wishlist y checkout visual.
5. Implementar pantallas de autenticación y recuperación de contraseña.
6. Implementar perfil, domicilios, pedidos y reseñas.
7. Implementar dashboard administrativo.
8. Documentar contratos de API y conectar el frontend con Laravel.
9. Integrar autenticación real, PostgreSQL, Stripe y webhooks.
10. Ejecutar pruebas funcionales y de seguridad antes del despliegue.

## Criterios De Seguridad

- Las contraseñas y secretos nunca se manejarán desde el frontend.
- Laravel debe recalcular precios, stock y totales; no confiará en valores enviados por Vue.
- Se usarán validaciones, autorización por políticas y rate limiting.
- Los webhooks de Stripe deberán validar su firma.
- La autenticación usará cookies seguras y protección CSRF mediante Sanctum.

## Registro De Cambios

- 2026-08-18: Se definió Grasse como e-commerce de perfumes para México.
- 2026-08-18: Se adoptó Laravel API + Vue SPA con PostgreSQL y Stripe en modo prueba.
- 2026-08-18: Se decidió comenzar por el frontend con datos mock.
- 2026-08-18: Se creó la SPA Vue en `frontend/` y se verificó con `npm run build`.
- 2026-08-18: Se configuró Vue Router con vistas para inicio, catálogo y detalle de producto.
- 2026-08-18: Se conectaron las tarjetas con sus detalles y se añadió un carrito compartido con confirmación visual.
- 2026-08-18: Se amplió el detalle de producto con variantes, stock, notas, calificaciones, comentarios mock, formulario de reseña y productos relacionados.
- 2026-08-18: Se mejoró la legibilidad del detalle y se ajustaron las estrellas para reflejar la calificación real, incluyendo valores parciales.
- 2026-08-18: Se corrigieron los estilos base de la sección de reseñas y productos relacionados.
- 2026-08-18: Se creó vista independiente de carrito con cantidades, eliminación, subtotal y total mock.
- 2026-08-18: Se añadió checkout mock con domicilio, resumen, validación básica y confirmación de pedido.
- 2026-08-18: Se añadieron pantallas mock de login, registro, recuperación y restablecimiento de contraseña.
- 2026-08-18: Se añadió área mock de usuario con resumen, perfil, domicilios, pedidos, wishlist y reseñas.
- 2026-08-18: Se añadió dashboard administrativo mock con métricas, pedidos, productos, stock y ranking.
- 2026-08-18: Se creó contrato TypeScript de catálogo y servicio mock para aislar datos de vistas.
- 2026-08-18: Se extrajo `ProductCard` como componente reutilizable para catálogo.
- 2026-08-18: Se añadieron ordenamiento y paginación mock al catálogo.
- 2026-08-18: Se corrigió filtrado y se añadieron filtros interactivos por precio y calificación.
- 2026-08-18: Se añadieron filtros por ocasión y familia olfativa al catálogo.
- 2026-08-18: Se creó backend Laravel 12 con Sanctum, configuración PostgreSQL y endpoint `/api/health`.
- 2026-08-18: Se mejoró visibilidad de stock y se asignaron colores de estado para pedidos según paleta Grasse.
- 2026-08-18: Se definió el contrato JSON del catálogo y se añadieron endpoints paginados de productos.
- 2026-08-18: Se añadieron migración, modelo, factory, seeder y pruebas del catálogo Laravel.
- 2026-08-18: Se conectaron el catálogo y el detalle de producto del frontend con la API Laravel mediante servicios HTTP.
- 2026-08-18: Se implementaron registro, login, usuario actual y logout con tokens de Laravel Sanctum.
- 2026-08-18: Se añadieron verificación de correo, recuperación y restablecimiento de contraseña con notificaciones Laravel.
- 2026-08-18: Se añadió rol administrativo, middleware de autorización y rate limiting para autenticación.
- 2026-08-18: Se implementaron endpoints protegidos de carrito y wishlist y sincronización del carrito autenticado en Vue.
- 2026-08-18: Se añadieron domicilios persistidos, pedidos desde carrito y pruebas de aislamiento por usuario.
- 2026-08-18: Se añadieron reseñas verificadas, limitadas a productos de pedidos entregados.
- 2026-08-18: Se conectó la lectura y publicación de reseñas del detalle con servicios HTTP del frontend.
- 2026-08-18: Se conectaron checkout autenticado e historial de pedidos con domicilios y pedidos Laravel.
- 2026-08-18: Se integró Stripe Checkout y se añadió webhook con validación de firma.
