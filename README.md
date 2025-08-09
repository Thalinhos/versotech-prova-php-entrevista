# Teste de PHP

## Rodar o script que inicializa o banco de dados e faz o seeder de alguns dados de teste

```bash
php ./src/connection.php
```

## Rodar servidor local para testar a aplicação

```bash
php -S localhost:3000 -t src
```

---

## Usando Docker (recomendado)

### Build da imagem

```bash
docker build -t versotechprova:v1.0 .
```

### Rodar o container

```bash
docker run --rm -p 3000:3000 versotechprova:v1.0
```

---

Acesse [http://localhost:3000](http://localhost:3000) no navegador para testar.
