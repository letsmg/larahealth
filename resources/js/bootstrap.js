import axios from 'axios';

// Configuração de segurança: token armazenado em cookie HttpOnly (inacessível via JS)
// O cookie é enviado automaticamente em todas as requisições para o mesmo domínio
axios.defaults.baseURL = '/api';
axios.defaults.headers.common['Accept'] = 'application/json';
axios.defaults.headers.common['Content-Type'] = 'application/json';
axios.defaults.withCredentials = true; // Envia cookies HttpOnly nas requisições

// =============================================
// Timer de Inatividade (5 minutos)
// =============================================
// Se o usuário ficar 5 minutos sem interagir (mouse, teclado, clique, toque),
// a sessão é encerrada automaticamente por segurança.
const SESSION_TIMEOUT = 5 * 60 * 1000; // 5 minutos em ms
let inactivityTimer = null;

function resetInactivityTimer() {
  if (inactivityTimer) clearTimeout(inactivityTimer);

  // Só inicia o timer se o usuário estiver logado
  if (!localStorage.getItem('user')) return;

  inactivityTimer = setTimeout(async () => {
    // Tenta fazer logout no backend
    try {
      await axios.post('/logout');
    } catch {
      // Ignora erro se o token já expirou
    }

    // Limpa dados locais e redireciona
    localStorage.removeItem('user');
    window.location.href = '/login';
  }, SESSION_TIMEOUT);
}

// Eventos de atividade do usuário
const activityEvents = ['mousedown', 'keydown', 'mousemove', 'touchstart', 'scroll', 'click'];

function attachActivityListeners() {
  activityEvents.forEach(event => {
    window.addEventListener(event, resetInactivityTimer, { passive: true });
  });
}

function detachActivityListeners() {
  activityEvents.forEach(event => {
    window.removeEventListener(event, resetInactivityTimer);
  });
}

// Inicia o monitoramento quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', () => {
  if (localStorage.getItem('user')) {
    attachActivityListeners();
    resetInactivityTimer();
  }
});

// Monitora mudanças no localStorage (login/logout em outras abas)
window.addEventListener('storage', (e) => {
  if (e.key === 'user') {
    if (e.newValue) {
      attachActivityListeners();
      resetInactivityTimer();
    } else {
      detachActivityListeners();
      if (inactivityTimer) clearTimeout(inactivityTimer);
    }
  }
});

// =============================================
// Interceptor de Resposta (401 - Token expirado)
// =============================================
axios.interceptors.response.use(
  response => response,
  error => {
    if (error.response?.status === 401) {
      // Token expirou ou foi revogado - limpa dados do usuário e redireciona
      localStorage.removeItem('user');
      detachActivityListeners();
      if (inactivityTimer) clearTimeout(inactivityTimer);
      window.location.href = '/login';
    }
    return Promise.reject(error);
  }
);

export default axios;
