# 📚 CRUD de Disciplinas

Sistema web para gerenciamento de disciplinas acadêmicas desenvolvido em PHP procedural, seguindo as **Heurísticas de Nielsen** para usabilidade.

## 🎯 Funcionalidades

- **Create**: Adicionar novas disciplinas
- **Read**: Visualizar lista de disciplinas
- **Update**: Editar disciplinas existentes  
- **Delete**: Excluir disciplinas com confirmação
- **Estatísticas**: Total de disciplinas e carga horária

## 🏗️ Estrutura do Projeto

```
CRUD-Disciplina/
├── index.php          # Página principal (listagem)
├── adicionar.php      # Formulário para adicionar
├── editar.php         # Formulário para editar
├── excluir.php        # Confirmação de exclusão
├── funcoes.php        # Funções auxiliares
├── estilo.css         # Estilos CSS
├── disciplinas.txt    # Arquivo de dados (criado automaticamente)
└── README.md          # Esta documentação
```

## 🚀 Como Usar

### Pré-requisitos
- Servidor web (Apache, Nginx, XAMPP, etc.)
- PHP 7.4 ou superior
- Permissões de escrita no diretório

### Instalação
1. Copie os arquivos para seu servidor web
2. Acesse `index.php` no navegador
3. O arquivo `disciplinas.txt` será criado automaticamente

### Navegação
- **Página Inicial**: Lista todas as disciplinas
- **Adicionar**: Formulário para nova disciplina
- **Editar**: Modificar disciplina existente
- **Excluir**: Remover disciplina com confirmação

## 📊 Estrutura dos Dados

Cada disciplina contém:
- **ID**: Identificador único (gerado automaticamente)
- **Nome**: Nome completo da disciplina
- **Sigla**: Abreviação (convertida para maiúsculas)
- **Carga Horária**: Total de horas (número inteiro)

### Formato do Arquivo
```
ID|Nome|Sigla|CargaHoraria
1|Programação Web|PW|80
2|Banco de Dados|BD|60
```

## 🎨 Heurísticas de Nielsen Implementadas

### 1. Visibilidade do Status do Sistema ✅
- Mensagens de sucesso/erro claras
- Feedback visual para todas as operações
- Indicadores de carregamento e processamento

### 2. Correspondência entre Sistema e Mundo Real ✅
- Interface intuitiva com ícones familiares
- Terminologia acadêmica clara
- Navegação natural e lógica

### 3. Controle e Liberdade do Usuário ✅
- Botões de cancelar em todas as operações
- Confirmação antes de exclusões
- Navegação fácil entre páginas

### 4. Consistência e Padrões ✅
- Layout uniforme em todas as páginas
- Botões com cores e estilos consistentes
- Formulários padronizados

### 5. Prevenção de Erros ✅
- Validação de dados em tempo real
- Campos obrigatórios marcados
- Confirmações para ações críticas

### 6. Reconhecimento em vez de Recordação ✅
- Interface clara e autoexplicativa
- Dicas e ajuda contextual
- Navegação visual intuitiva

### 7. Flexibilidade e Eficiência de Uso ✅
- Atalhos visuais para ações comuns
- Formulários otimizados
- Responsividade para diferentes dispositivos

### 8. Estética e Design Minimalista ✅
- Interface limpa e moderna
- Cores harmoniosas e gradientes
- Espaçamento adequado entre elementos

### 9. Ajuda aos Usuários ✅
- Mensagens de erro descritivas
- Dicas de preenchimento
- Documentação contextual

### 10. Documentação ✅
- Código bem comentado
- README detalhado
- Instruções claras de uso

## 🔧 Funcionalidades Técnicas

### Validação de Dados
- Nome: obrigatório, máximo 100 caracteres
- Sigla: obrigatória, máximo 10 caracteres
- Carga horária: número positivo obrigatório

### Segurança
- Sanitização de entrada do usuário
- Escape de saída HTML
- Validação de tipos de dados

### Responsividade
- Design mobile-first
- Tabelas com scroll horizontal
- Botões adaptáveis para touch

## 🎯 Casos de Uso

### Professor/Coordenador
- Cadastrar novas disciplinas
- Atualizar informações existentes
- Gerenciar carga horária

### Administrador
- Visualizar estatísticas gerais
- Manter base de dados organizada
- Backup do arquivo de dados

## 🚨 Tratamento de Erros

- **Arquivo não encontrado**: Criação automática
- **Dados inválidos**: Validação e feedback
- **Permissões**: Mensagens claras de erro
- **Redirecionamentos**: Navegação segura

## 🔄 Manutenção

### Backup
- O arquivo `disciplinas.txt` contém todos os dados
- Faça backup regular deste arquivo
- Pode ser editado manualmente se necessário

### Atualizações
- Código modular para fácil manutenção
- Funções bem documentadas
- Separação clara de responsabilidades

## 📱 Compatibilidade

- **Navegadores**: Chrome, Firefox, Safari, Edge
- **Dispositivos**: Desktop, tablet, mobile
- **Sistemas**: Windows, macOS, Linux

## 🎨 Personalização

### Cores
- Gradientes modernos e responsivos
- Esquema de cores consistente
- Contraste adequado para acessibilidade

### Layout
- Grid responsivo
- Cards com sombras
- Animações suaves

## 📈 Melhorias Futuras

- [ ] Sistema de busca e filtros
- [ ] Exportação para Excel/PDF
- [ ] Histórico de alterações
- [ ] Sistema de usuários e permissões
- [ ] API REST para integração
- [ ] Banco de dados MySQL/PostgreSQL

## 🤝 Contribuição

1. Fork do projeto
2. Crie uma branch para sua feature
3. Commit suas mudanças
4. Push para a branch
5. Abra um Pull Request

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo LICENSE para detalhes.

## 👨‍💻 Autor

Desenvolvido com ❤️ seguindo as melhores práticas de usabilidade e desenvolvimento web.

---

**Nota**: Este sistema foi desenvolvido para fins educacionais e demonstra as melhores práticas de desenvolvimento PHP procedural com foco em usabilidade e manutenibilidade.
