# Blink 👁️

Sistema de gestão de saúde com arquitetura baseada em APIs RESTful, construído com Laravel.

> [English version](#english-version)

## Sobre o Projeto

Blink é um sistema de gestão clínica/hospitalar que oferece:

- Cadastro e gerenciamento de pacientes
- Agendamento de consultas com controle de pagamento e retorno
- Gestão de profissionais de saúde
- Diagnósticos e histórico clínico
- Sistema de mensageria interna
- **Gestão de indisponibilidade de profissionais** (períodos em que não atendem)
- Relatórios consolidados

### Arquitetura

- **Backend**: Laravel (API RESTful)
- **Banco de Dados**: PostgreSQL
- **Frontend**: Vue.js (SPA)
- **Mobile**: Flutter (futuro)

### Padrão de Projeto

MVC expandido com:

- **Controllers**: Pontos de entrada da API
- **Services**: Regras de negócio
- **Repositories**: Consultas e persistência
- **Requests**: Validação de formulários
- **Policies**: Autorização de acesso

### Segurança

- Hash Argon2id para senhas
- CPF criptografado no banco de dados
- Sanitização de entrada (middleware)
- Validação dupla (front-end + back-end)
- Controle de acesso por níveis (Patient, Admin, Operational)
- Aceite obrigatório de Termos de Uso e Políticas de Privacidade

### Funcionalidades Implementadas

#### ✅ Registro e Autenticação

- Registro de pacientes com validação de CPF
- Login com geração de token
- Logout e perfil do usuário
- Aceite de termos de uso
- **Facilitador de Login**: Lista de usuários de teste na tela de login com preenchimento automático ao clicar

#### ✅ Gestão de Pacientes (Staff)

- Listagem paginada
- Visualização detalhada (com consultas e diagnósticos)
- Atualização de perfil

#### ✅ Sistema de Mensageria (Staff)

- Envio de mensagens internas
- Listagem de mensagens recebidas
- Marcação de leitura
- **Indicador dinâmico de mensagens não lidas** no menu lateral (polling a cada 30s)
- Botão "Preencher Teste" no formulário de nova mensagem

#### ✅ Gestão de Indisponibilidade de Profissionais

- Cadastro de períodos em que o profissional não atenderá
- Validação de sobreposição de períodos
- Listagem de períodos futuros para calendário
- CRUD completo (Staff apenas)
- Botão "Preencher Teste" no formulário

#### ✅ Auxiliares de Desenvolvimento (Front-end)

- **`formHelpers.ts`**: Arquivo TypeScript com funções helper para preenchimento automático de formulários
- Botão **"Preencher Teste"** em todas as páginas de cadastro (Register, Messages, Unavailability)
- Botão **"Limpar"** no formulário de cadastro de paciente
- Geração de dados realistas: CPF, nomes, endereços, datas, mensagens

### Rotas da API

#### Públicas

| Método | Rota | Descrição |
|--------|------|-----------|
| POST | `/api/register` | Registro de paciente |
| POST | `/api/login` | Login |

#### Autenticadas

| Método | Rota | Descrição |
|--------|------|-----------|
| POST | `/api/logout` | Logout |
| GET | `/api/me` | Perfil do usuário |
| POST | `/api/accept-terms` | Aceitar termos |

#### Staff (Admin + Operational)

| Método | Rota | Descrição |
|--------|------|-----------|
| GET | `/api/staff/messages` | Listar mensagens |
| POST | `/api/staff/messages` | Enviar mensagem |
| GET | `/api/staff/messages/unread-count` | Contagem de não lidas |
| PATCH | `/api/staff/messages/{message}/read` | Marcar como lida |
| GET | `/api/staff/patients` | Listar pacientes |
| GET | `/api/staff/patients/{patient}` | Ver paciente |
| PUT | `/api/staff/patients/{patient}` | Atualizar paciente |
| GET | `/api/staff/professionals/{professional}/unavailability` | Listar indisponibilidades |
| GET | `/api/staff/professionals/{professional}/unavailability/future` | Listar futuras |
| POST | `/api/staff/professionals/{professional}/unavailability` | Criar indisponibilidade |
| PUT | `/api/staff/professionals/{professional}/unavailability/{period}` | Atualizar |
| DELETE | `/api/staff/professionals/{professional}/unavailability/{period}` | Remover |

### Testes

```bash
php artisan test
```

**49 testes • 102 asserções** — todos passando ✅

---

## English Version

# Blink 👁️

Healthcare management system with RESTful API architecture, built with Laravel.

### About

Blink is a clinical/hospital management system featuring patient registration, appointment scheduling, professional management, diagnostics, internal messaging, **professional unavailability management**, and consolidated reports.

### Architecture

- **Backend**: Laravel (RESTful API)
- **Database**: PostgreSQL
- **Frontend**: Vue.js (SPA)
- **Mobile**: Flutter (future)

### Professional Unavailability Management

Professionals can register periods when they won't be available for appointments (vacations, external shifts, medical leave). The appointment calendar queries `GET /api/staff/professionals/{professional}/unavailability/future` to block those dates.

### Tests

```bash
php artisan test
```

**49 tests • 102 assertions** — all passing ✅
