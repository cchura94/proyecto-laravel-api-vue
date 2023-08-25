# Comandos GIT
## Descargar e Instalar GIT
- https://git-scm.com/
- instalar 
## Configurar GIT
```bash
git config --global user.name "Cristian"
git config --global user.email "sucorreo@mail.com"
```
## Necesitamos un cuenta en (GitHub) o Bitbucket o GitLab
- https://github.com/
- crear un nuevo repositorio para su proyecto
---
## Crear o Clonar el repositorio (LOCAL)
---
- Si ya existe el repositorio (CLONAR)
```
git clone direccion_del_repo
```
- Si no existe el repositorio (INICIAR)
```
git init
```
## referenciar el repo local(mi pc) con remoto (Github)
- buscar la direccion del repositorio en github
```
git remote add origin https://github.com/cchura94/proyecto-laravel-api-vue.git
```
---
### Para subir los cambios al repositorio remoto
```
git add .
git commit -m "proyecto base Laravel"
git push origin master
```