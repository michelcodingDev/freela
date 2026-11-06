# FreelaJob — Marketplace freelancer com escrow

Clone funcional estilo 99Freelas: cliente publica projeto, freelancer propõe, cliente paga em custódia, freelancer executa e entrega, cliente libera, plataforma repassa (saque).

## Stack

- PHP ^8.3, Laravel ^13, Vite, Tailwind (CDN/Alpine via layout), MySQL
- PagSeguro SDK (`pagseguro/pagseguro-php-sdk`) instalado, estratégias simuladas por padrão

## Setup local

```powershell
cd C:\laragon\www\freelajob
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force
npm install --ignore-scripts
npm run dev    # em outro terminal
php artisan serve --port=82
```

Para Laragon/Apache, `APP_URL=http://localhost:82` e VirtualHost apontando para `public/` (veja `C:/laragon/etc/apache2/sites-enabled/freelajob-82.conf`).

## Logins demo (após seed)

| E-mail | Senha | Papel |
|---|---|---|
| `admin@freelajob.com` | `admin123` | Admin |
| `cliente@demo.test` | `password` | Cliente |
| `freelancer@demo.test` | `password` | Freelancer |

## Fluxo escrow (8 etapas)

Publicação → Aprovação (admin) → Propostas → Seleção → Pagamento (custódia) → Execução (chat) → Liberação → Repasse (saque Pix).

Regras aplicadas:
- Proposta: 1 por freelancer/projeto, limite mensal do plano (Free 10, Pro 50, Premium ilimitado).
- Pagamento: idempotente (não duplica ao recarregar), webhook idempotente.
- Saque: saldo = liberado − saques ativos, taxa Pix 2%, mínimo R$ 10.
- Avaliação: 1–5★, 1 por lado, 14 dias após conclusão.
- Chat: bloqueio de contato externo.
- Portfólio: limite do plano (Free 3).
- Disputa: abre disputa → projeto `disputa`, admin resolve cliente/freelancer/parcial.

## Testes

```powershell
php artisan test --compact
```

Banco de teste: `freelajob_test` (MySQL, ver `phpunit.xml`).

## PagSeguro

Configure no `.env` (`PAGSEGURO_EMAIL`, `PAGSEGURO_TOKEN`, `PAGSEGURO_ENVIRONMENT`, `PAGSEGURO_WEBHOOK_SECRET`). Estratégias em `app/Services/Payment/*Strategy.php` são simuladas até conectar o SDK real.
