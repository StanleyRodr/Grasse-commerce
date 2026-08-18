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
