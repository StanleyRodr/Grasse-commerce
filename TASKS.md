# Grasse: Tareas del Proyecto

## Pendientes

### Base Del Frontend

- [x] Crear la aplicación Vue 3 SPA con TypeScript y Vite.
- [x] Instalar Pinia, Vue Router, Tailwind CSS y `@lucide/vue`.
- [x] Definir una primera identidad visual para Grasse.
- [x] Crear layout público, navegación, header y footer.
- [x] Preparar catálogo inicial con datos mock.
- [x] Configurar Vue Router y separar inicio y detalle de producto en vistas.
- [x] Crear componente reutilizable `ProductCard`.
- [x] Crear servicio mock de catálogo con contratos TypeScript.
- [ ] Completar servicios HTTP para reemplazar los datos mock.

### Catálogo Y Compra

- [x] Crear detalle con variantes de presentación mock.
- [x] Implementar búsqueda, categorías por ocasión, familias olfativas, filtros por precio/calificación, ordenamiento y paginación mock.
- [x] Crear tarjetas de producto con wishlist y carrito.
- [x] Crear página de detalle de producto.
- [x] Implementar primera versión del carrito y su contador compartido.
- [x] Crear vista independiente de carrito con cantidades, eliminación, subtotal y total.
- [x] Implementar wishlist visual.
- [x] Crear checkout visual mock para usuarios e invitados.

### Cuenta Del Usuario

- [x] Crear pantallas mock de registro, login y cierre de sesión.
- [x] Crear pantallas mock de recuperación y restablecimiento de contraseña.
- [x] Crear pantalla de verificación de correo.
- [x] Crear perfil mock y edición de datos personales.
- [x] Crear gestión mock de múltiples domicilios y domicilio predeterminado.
- [x] Crear resumen mock, historial de pedidos y estados.
- [x] Crear resumen, comentarios y formulario mock de reseñas verificadas.

### Dashboard Administrativo

- [x] Crear resumen mock de ventas y pedidos.
- [x] Crear gestión mock y cambio de estados de pedidos.
- [x] Crear CRUD visual mock de productos y variantes.
- [x] Crear visualización mock de stock por colores y umbrales definidos.
- [x] Crear ranking mock de productos más vendidos.

### Backend E Integraciones

- [x] Definir contratos y respuestas de la API Laravel.
- [ ] Crear proyecto Laravel API y configurar PostgreSQL.
- [x] Crear proyecto Laravel API y añadir Laravel Sanctum.
- [ ] Configurar PostgreSQL y ejecutar migraciones.
- [x] Implementar autenticación con Sanctum y autorización administrativa.
- [x] Implementar recuperación de contraseña y envío de correos.
- [x] Implementar modelos, migraciones y endpoints del catálogo.
- [ ] Implementar carrito, wishlist, domicilios, pedidos y reseñas.
- [x] Integrar Stripe Checkout en modo prueba.
- [ ] Implementar y validar webhooks de Stripe.
- [ ] Conectar el frontend con la API real.

### Verificación

- [ ] Añadir pruebas funcionales del flujo de compra.
- [ ] Verificar permisos y acceso a información privada.
- [ ] Verificar validaciones, rate limiting y manejo de errores.
- [ ] Ejecutar revisión final responsive y de accesibilidad.
- [ ] Refinar diseño visual de cuenta, admin y formularios CRUD.

## En Progreso

- [x] Planificación y definición inicial del proyecto.
- [x] Implementación inicial del frontend mock.

## Completadas

- [x] Definir Grasse como e-commerce de perfumes para México.
- [x] Elegir Laravel API + Vue SPA.
- [x] Elegir PostgreSQL como base de datos.
- [x] Elegir Stripe en modo prueba.
- [x] Decidir comenzar por el frontend con datos mock.
- [x] Crear la SPA en `frontend/` y verificar la compilación inicial.
- [x] Añadir rutas para inicio, catálogo y detalle de producto.
- [x] Enlazar tarjetas con el detalle de cada producto.
- [x] Añadir store Pinia y confirmación visual al agregar productos al carrito.
- [x] Rediseñar el detalle con información ampliada, stock, notas y productos relacionados.
- [x] Mejorar legibilidad del breadcrumb y datos logísticos del detalle.
- [x] Mostrar estrellas del detalle según la calificación real.
- [x] Corregir la carga visual de la sección de reseñas y comentarios.
- [x] Conectar carrito con checkout y confirmación de pedido mock.
- [x] Conectar navegación de cuenta con rutas de autenticación mock.
- [x] Crear área mock de usuario con perfil, domicilios, pedidos, wishlist y reseñas.
- [x] Crear dashboard administrativo mock.
- [x] Ajustar visibilidad de unidades de stock y colores de estados de pedidos.
- [x] Aislar catálogo mock detrás de servicio frontend.
- [x] Extraer tarjeta de producto reutilizable sin cambiar diseño.
- [x] Crear backend Laravel 12 en `backend/` y endpoint de salud.
- [x] Ampliar catálogo mock para probar múltiples páginas.
- [x] Corregir botón `Filtros` y añadir panel interactivo.
- [x] Añadir filtros por ocasión: Fresco, Diario, Fiesta y Noche.
- [x] Añadir filtros por familia olfativa: Fresca, Floral, Cítrica, Amaderada, Almizclada y Ambarada.
- [x] Definir contrato JSON y endpoints paginados del catálogo Laravel.
- [x] Añadir migración, modelo, factory y seeder inicial de productos.
- [x] Añadir pruebas de filtros, ordenamiento, paginación y detalle de producto.
- [x] Conectar catálogo y detalle de producto del frontend con la API Laravel.
- [x] Implementar registro, login, usuario actual y logout con Sanctum.
- [x] Implementar verificación de correo y restablecimiento de contraseña en Laravel.
- [x] Añadir rate limiting a los endpoints sensibles de autenticación.
- [x] Implementar API protegida de carrito y wishlist con sincronización básica del frontend.
- [x] Implementar API persistida de domicilios y pedidos con vaciado seguro del carrito.
- [x] Implementar reseñas verificadas para compradores con pedido entregado.
- [x] Conectar reseñas del detalle de producto con la API Laravel.
- [x] Conectar checkout autenticado e historial de pedidos con la API Laravel.
- [x] Conectar gestión de domicilios de cuenta con la API Laravel.
- [x] Añadir endpoint Stripe Checkout y webhook con validación de firma.
- [x] Conectar dashboard admin con métricas, pedidos y cambio de estados reales.
- [x] Implementar CRUD admin de productos y stock persistido.
- [x] Implementar variantes reales por presentación, precio y stock.
- [x] Añadir pantalla de verificación de correo con reenvío autenticado.
- [x] Conectar la wishlist de la cuenta con el servicio persistido.
- [x] Validar stock y descontarlo al crear pedidos.
- [x] Conectar edición de perfil y reinicio de verificación al cambiar correo.
- [x] Conectar historial de reseñas de cuenta con la API.
- [x] Reemplazar productos relacionados hardcodeados por catálogo API.
- [x] Probar webhook Stripe firmado e inválido para pedido pendiente.
