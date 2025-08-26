# 📚 CRUD de Disciplinas

Sistema web para gerenciamento de disciplinas acadêmicas desenvolvido em PHP.

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

## 👨‍💻 Autor

Desenvolvido com ❤️ seguindo as melhores práticas de usabilidade e desenvolvimento web.

---

**Nota**: Este sistema foi desenvolvido para fins educacionais e demonstra as melhores práticas de desenvolvimento PHP procedural com foco em usabilidade e manutenibilidade.
