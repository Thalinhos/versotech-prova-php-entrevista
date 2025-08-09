FROM php:8.2-cli

# vou deixar os comentários pra ajudar quem testar, dockerfile é mais chatinho mesmo, eu geralmente uso só o compose pra subir dbs e testar mesmo
# Instalar extensões necessárias (pdo_sqlite e sqlite3)
RUN apt-get update && apt-get install -y libsqlite3-dev \
    && docker-php-ext-install pdo_sqlite \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Criar diretório da aplicação dentro do container docker
WORKDIR /app

# Copiar todos os arquivos da pasta atual para /app no container
COPY . .

# Rodar o seeder para iniciar/popular o banco --ele sempre da um 'truncate' na tabela antes de fazer os seeders em si
RUN php src/seeder.php

# Expõe a porta 3000
EXPOSE 3000

# Comando para iniciar o servidor embutido PHP na pasta src, na porta 3000
CMD ["php", "-S", "0.0.0.0:3000", "-t", "src"]
