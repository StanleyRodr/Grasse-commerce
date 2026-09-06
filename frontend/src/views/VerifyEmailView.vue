<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { ArrowLeft, Check, Mail, RefreshCw } from '@lucide/vue'
import { RouterLink } from 'vue-router'
import { useRoute } from 'vue-router'
import { getAuthUser, resendVerificationEmail } from '../services/authService'

const user = getAuthUser()
const route = useRoute()
const sent = ref(false)
const verified = ref(false)
const loading = ref(false)
const errorMessage = ref('')

const resend = async () => {
  loading.value = true
  errorMessage.value = ''
  try {
    await resendVerificationEmail()
    sent.value = true
  } catch (error) {
    errorMessage.value = error instanceof Error ? error.message : 'No pudimos enviar el correo.'
  } finally {
    loading.value = false
  }
}

onMounted(() => { if (user?.email_verified_at || route.query.verified === '1') verified.value = true })
</script>

<template>
  <div class="auth-page verification-page">
    <header class="auth-header"><RouterLink to="/" class="back-link"><ArrowLeft :size="16" /> Volver a Grasse</RouterLink><RouterLink class="wordmark" to="/">grasse<span>.</span></RouterLink><span class="detail-meta">ÁREA SEGURA</span></header>
    <main class="verification-content">
      <Mail v-if="!verified" :size="32" /><Check v-else :size="32" />
      <p class="eyebrow">Cuenta Grasse</p>
      <h1>{{ verified ? 'Correo verificado.' : 'Confirma tu correo.' }}</h1>
      <p v-if="verified">Tu cuenta ya está lista para guardar favoritos y seguir tus pedidos.</p>
      <p v-else>Enviamos un enlace de verificación a <strong>{{ user?.email ?? 'tu correo electrónico' }}</strong>. Revisa también la carpeta de spam.</p>
      <p v-if="sent" class="verification-success"><Check :size="15" /> Correo enviado. Revisa tu bandeja de entrada.</p>
      <p v-if="errorMessage" class="auth-error" role="alert">{{ errorMessage }}</p>
      <div class="verification-actions">
        <button v-if="!verified" class="primary-button" :disabled="loading" @click="resend"><RefreshCw :size="16" /> {{ loading ? 'Enviando...' : 'Reenviar correo' }}</button>
        <RouterLink class="text-link" :to="verified ? '/cuenta' : '/'">{{ verified ? 'Ir a mi cuenta' : 'Continuar explorando' }}</RouterLink>
      </div>
    </main>
  </div>
</template>
