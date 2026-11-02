# Validação da documentação — Augebit Web

Revisão em 02/11/2026, Windows, PHP CLI 8.2.12 e MariaDB 10.4.32.

| Verificação | Resultado |
| :-- | :-- |
| `php -l` em todos os PHP | 40 de 40 passaram |
| Importação de `semestral_3b.sql` | 13 tabelas, com zero registros |
| Formulários públicos | Login e cadastro responderam HTTP 200 no servidor PHP local |
| Avatar padrão | HTTP 200 |
| Avatares pessoais | Seis arquivos `user_*` retirados |
| Metadados de imagens | Sem GPS identificado nas 38 imagens PNG/JPG remanescentes verificadas |
| README e assets | Seções comparadas ao StoreAPI, links e SVGs conferidos e prévia visual revisada |

O banco foi importado em uma instância temporária, com datadir e porta próprios, sem tocar nos bancos da máquina. O servidor PHP não foi conectado aos cadastros reais. Os processos temporários foram encerrados.

Não há suíte de testes versionada. Fluxos autenticados completos, permissões, inscrição, avaliações, certificado, upload e pagamento simulado não foram testados de ponta a ponta. As conexões PHP hardcoded também precisam de configuração consistente para uma instalação real. Linux/macOS e hospedagem não foram exercitados.
