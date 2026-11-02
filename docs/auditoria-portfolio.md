# Auditoria de portfólio — Augebit Web

Revisão em 02/11/2026. Base: `014f1531ead4be37175ae2e9c4996f6d474b6323`.

Foram inventariados os arquivos atuais e o histórico alcançável pelas referências disponíveis no clone: 201 objetos Git e 121 versões de arquivos de texto próprios. A busca incluiu padrões de tokens, chaves privadas, URLs de conexão, configurações de segredo, e-mails e hashes de senha. Valores sensíveis não foram reproduzidos neste documento.

A revisão não abrange segredos da hospedagem, bancos pessoais, logs externos, caches, releases ou forks, nem equivale a um pentest. Não houve alteração de visibilidade ou reescrita do histórico. As ilustrações do README representam fluxos e não usam contas ou bancos pessoais.

## Dados encontrados no repositório público

- `semestral_3b.sql` incluía cadastros com nomes, e-mails, telefone, nascimento, biografia, perfil, avatar e hashes de senha, além de contatos, assinaturas de newsletter, inscrições e respostas de avaliações. Não foi assumido que todos fossem fictícios.
- Seis arquivos `user_*` em `curso/usuario/paginaPerfil/uploads/avatars/` foram identificados como uploads de usuários e retirados. O avatar padrão foi mantido.
- Configurações PHP consultadas usavam banco local, usuário root e senha vazia; não foram detectadas credenciais remotas reais pelos padrões verificados.
- Não foi identificado GPS nas 38 imagens PNG/JPG remanescentes verificadas. Isso não certifica direitos autorais, consentimento de imagem ou outros tipos de metadados.

## Alterações realizadas

- Retirados os 10 blocos de INSERT de dados do dump. Estrutura de 13 tabelas, índices e relações preservada; contadores do dump normalizados. Importação isolada confirmou zero registros.
- Removidos os seis avatares e criado `.gitignore` para configurações locais, logs, backups e novos uploads nos caminhos conhecidos.
- Layouts, logos e imagens referenciados pelas páginas foram preservados. Duplicatas de assets que ainda são usadas por caminhos relativos não foram removidas indiscriminadamente.
- README e quatro SVGs de documentação adaptados ao modelo StoreAPI; pagamento descrito como simulação, sem integração financeira real.

## Segurança e limites de funcionamento

Há configurações de banco repetidas e uso de MySQLi/PDO em diferentes módulos. O código de cadastro aceita perfis diferentes; autorização, CSRF e uploads exigem revisão antes de processar dados reais. A limpeza do repositório não equivale à correção dessas áreas.

Todos os 40 arquivos PHP passaram no lint. Os formulários públicos responderam HTTP 200 e o schema foi importado em instância temporária, separada dos bancos da máquina. Os fluxos completos de login com banco, alunos, instrutores, administração, avaliações e pagamento não foram exercitados.

## Próximo passo de privacidade

O projeto já era público. Dados e avatares removidos ainda podem ser recuperados em commits anteriores ou cópias externas. É necessário tratar o histórico ou preparar uma exportação limpa antes de ampliar a divulgação. Se houver contas reais nesses registros, revise as credenciais afetadas. Nenhum hash, contato ou conteúdo pessoal foi reproduzido no novo README ou neste relatório.
