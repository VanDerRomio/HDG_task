# docker_compose_service

## 1. clone git repository

## 2. copy docker_compose_service in repository

## 3. in file docker-compose.yml need replace `{...}` on your project_name:
    - {...}_app
    - {...}_webserver
    - {...}_db + in laravel/.env->DB_HOST={...}_db
    - {...}_app-network

## 4. in file nginx/conf.d/app.conf need replace on your domain: 
    - __site_domain__

## 5. in file Dockerfile replace php version (if need)

## 6. in file Dockerfile replace php version (if need)


    - __site_domain__