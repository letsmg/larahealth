/**
 * Event Bus para comunicação entre componentes Vue.
 * Usado para notificar o AuthHeader quando o contador de mensagens
 * não lidas precisa ser atualizado instantaneamente.
 */
import mitt from 'mitt'

const emitter = mitt()

export default emitter

// Eventos disponíveis:
// 'messages:read-status-changed' - Disparado quando uma mensagem é marcada como lida/não lida
