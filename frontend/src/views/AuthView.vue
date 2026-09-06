<script setup lang="ts">
import { computed, ref } from 'vue'
import { ArrowLeft, ArrowRight, Check, LockKeyhole, ShieldCheck } from '@lucide/vue'
import { RouterLink, useRoute, useRouter } from 'vue-router'
import { forgotPassword, login, register, resetPassword, saveAuthSession } from '../services/authService'

const route = useRoute()
const router = useRouter()
const email = ref('')
const password = ref('')
const name = ref('')
const submitted = ref(false)
const isSubmitting = ref(false)
const errorMessage = ref('')
const mode = computed(() => route.name as string)
const isLogin = computed(() => mode.value === 'login')
const isRegister = computed(() => mode.value === 'register')
const isForgot = computed(() => mode.value === 'forgot-password')
const title = computed(() => isLogin.value ? 'Vuelve a tu mundo.' : isRegister.value ? 'Forma parte de Grasse.' : isForgot.value ? 'Recupera tu acceso.' : 'Crea una nueva contraseña.')
const eyebrow = computed(() => isLogin.value ? 'Bienvenido de vuelta' : isRegister.value ? 'Únete a la comunidad' : 'Acceso seguro')
const submitLabel = computed(() => isLogin.value ? 'Iniciar sesión' : isRegister.value ? 'Crear cuenta' : isForgot.value ? 'Enviar instrucciones' : 'Guardar contraseña')
const submit = async () => {
  errorMessage.value = ''
  isSubmitting.value = true

  try {
    if (isForgot.value) {
      await forgotPassword(email.value)
      submitted.value = true
      return
    }

    if (mode.value === 'reset-password') {
      await resetPassword(String(route.query.token ?? ''), email.value, password.value, password.value)
      submitted.value = true
      return
    }

    const response = isRegister.value
      ? await register(name.value, email.value, password.value, password.value)
      : await login(email.value, password.value)

    saveAuthSession(response)
    submitted.value = true
    await router.push({ name: isLogin.value ? 'account' : 'home' })
  } catch (error) {
    errorMessage.value = error instanceof Error ? error.message : 'No pudimos completar la solicitud.'
  } finally {
    isSubmitting.value = false
  }
}
</script>

<template>
  <div class="auth-page"><header class="auth-header"><RouterLink to="/" class="back-link"><ArrowLeft :size="16" /> Volver a Grasse</RouterLink><RouterLink class="wordmark" to="/">grasse<span>.</span></RouterLink><span class="detail-meta"><LockKeyhole :size="12" /> ÁREA SEGURA</span></header><main class="auth-layout"><section class="auth-art"><span class="auth-art-number">GRASSE / 01</span><div class="auth-sun"></div><div class="auth-bottle"><div class="auth-bottle-label">GRASSE<br /><small>PARFUM</small></div></div><p>Tu colección.<br /><em>Tu historia.</em></p></section><section class="auth-panel"><div class="auth-content"><p class="eyebrow">{{ eyebrow }}</p><h1>{{ title }}</h1><p class="auth-intro" v-if="isLogin">Guarda tus favoritos, sigue tus pedidos y descubre nuevas esencias.</p><p class="auth-intro" v-else-if="isRegister">Crea tu cuenta para guardar tus favoritos y hacer de cada compra una experiencia personal.</p><p class="auth-intro" v-else>Te enviaremos un enlace para recuperar el acceso a tu cuenta.</p><div v-if="submitted" class="auth-success"><span><Check :size="18" /></span><h2>{{ isForgot ? 'Revisa tu correo.' : isLogin ? 'Sesión iniciada.' : isRegister ? 'Cuenta creada.' : 'Contraseña actualizada.' }}</h2><p>{{ isForgot ? `Si existe una cuenta asociada a ${email || 'este correo'}, recibirás instrucciones en unos minutos.` : 'Esta demostración está lista para conectarse con Laravel Sanctum.' }}</p><RouterLink to="/" class="text-link">Volver a la tienda <ArrowRight :size="15" /></RouterLink></div><form v-else class="auth-form" @submit.prevent="submit"><label v-if="isRegister">Nombre completo<input v-model="name" type="text" placeholder="Tu nombre" required /></label><label>Correo electrónico<input v-model="email" type="email" placeholder="tu@correo.com" required /></label><label v-if="!isForgot">Contraseña<input v-model="password" type="password" placeholder="Mínimo 8 caracteres" minlength="8" required /></label><label v-if="mode === 'reset-password'">Confirmar contraseña<input type="password" placeholder="Repite tu contraseña" minlength="8" required /></label><div v-if="isLogin" class="auth-form-row"><label class="check-label"><input type="checkbox" /> Recordarme</label><RouterLink to="/recuperar-contrasena">¿Olvidaste tu contraseña?</RouterLink></div><button class="primary-button auth-submit" type="submit">{{ submitLabel }} <ArrowRight :size="16" /></button></form><div v-if="!submitted" class="auth-switch"><span v-if="isLogin">¿Aún no tienes cuenta?</span><span v-else-if="isRegister">¿Ya tienes una cuenta?</span><span v-else>¿Recordaste tu contraseña?</span><RouterLink :to="isLogin ? '/registro' : '/login'">{{ isLogin ? 'Crear cuenta' : 'Iniciar sesión' }}</RouterLink></div><div class="auth-security"><ShieldCheck :size="15" /><span>Protegemos tus datos con autenticación segura.</span></div></div></section></main></div>
  <p v-if="errorMessage" class="auth-error" role="alert">{{ errorMessage }}</p>
</template>
