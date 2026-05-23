/**
 * Visitor ID utility for LGPD compliance.
 *
 * Gera um UUID v4 exclusivo para identificar visitantes anônimos.
 * O ID é armazenado primariamente em cookie persistente (SameSite=Lax, Secure).
 * Se o navegador rejeitar cookies (modo anônimo, Brave, bloqueadores),
 * usa sessionStorage como fallback imediato.
 *
 * NENHUMA coleta de dados (IP, geolocalização, tracking) é disparada
 * antes do aceite explícito dos termos pelo visitante.
 */

const COOKIE_NAME = 'visitor_uuid'
const STORAGE_KEY = 'visitor_uuid'
const COOKIE_EXPIRY_DAYS = 365

/**
 * Gera um UUID v4.
 */
function generateUUID(): string {
  // crypto.randomUUID() está disponível em navegadores modernos (HTTPS)
  if (typeof crypto !== 'undefined' && typeof crypto.randomUUID === 'function') {
    return crypto.randomUUID()
  }
  // Fallback para ambientes sem crypto.randomUUID
  return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, (c) => {
    const r = (Math.random() * 16) | 0
    const v = c === 'x' ? r : (r & 0x3) | 0x8
    return v.toString(16)
  })
}

/**
 * Tenta ler um cookie pelo nome.
 */
function getCookie(name: string): string | null {
  const match = document.cookie.match(new RegExp(`(?:^|;\\s*)${name}=([^;]*)`))
  return match ? decodeURIComponent(match[1]) : null
}

/**
 * Tenta salvar um cookie persistente.
 */
function setCookie(name: string, value: string, days: number): boolean {
  try {
    const expires = new Date()
    expires.setDate(expires.getDate() + days)
    document.cookie = `${name}=${encodeURIComponent(value)}; expires=${expires.toUTCString()}; path=/; SameSite=Lax; Secure`
    return true
  } catch {
    return false
  }
}

/**
 * Obtém o visitor_uuid atual ou cria um novo.
 *
 * Estratégia de persistência resiliente:
 * 1. Tenta ler de cookie existente
 * 2. Se não existe cookie, tenta sessionStorage
 * 3. Se não existe em nenhum lugar, gera novo UUID
 * 4. Tenta salvar em cookie (primário)
 * 5. Se cookie falhar (navegador restritivo), salva em sessionStorage (fallback)
 */
export function getVisitorId(): string {
  // 1. Tenta ler de cookie existente
  let id = getCookie(COOKIE_NAME)

  // 2. Se não existe cookie, tenta sessionStorage
  if (!id) {
    try {
      id = sessionStorage.getItem(STORAGE_KEY)
    } catch {
      // sessionStorage pode não estar disponível
    }
  }

  // 3. Se não existe em lugar nenhum, gera novo UUID
  if (!id) {
    id = generateUUID()
  }

  // 4. Tenta salvar em cookie (primário)
  const cookieSaved = setCookie(COOKIE_NAME, id, COOKIE_EXPIRY_DAYS)

  // 5. Se cookie falhou, salva em sessionStorage (fallback)
  if (!cookieSaved) {
    try {
      sessionStorage.setItem(STORAGE_KEY, id)
    } catch {
      // Se ambos falharem, o ID ainda existe na memória para esta sessão
    }
  }

  return id
}
