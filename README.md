### Abdel Eb - FullStack Developer

# _API Clicko - Technical test_

Este README proporciona una guía sencilla para configurar y ejecutar la API. El proyecto lo he desarrollado con Sail Docker en entorno local.

# API Endpoints

| Endpoint              | Method | Info                                                     |
| --------------------- | ------ | -------------------------------------------------------- |
| /api/register         | POST   | Registro de usuario y genera token de acceso             |
| /api/login            | POST   | Autenticación de usuario y genera token de acceso        |
| /api/logout           | GET    | Sale de la sesión y borra los tokens de acceso.          |
| /api/user/            | GET    | Muestra todos los usuarios de la tabla "users"           |
| /api/user/top-domains | GET    | Muestra los top 3 dominios de correo junto a la cantidad |
| /api/user/create      | POST   | Crea un usuario nuevo                                    |
| /api/user/{id}        | GET    | Muestra los datos de un usuario específico               |
| /api/user/edit/{id}   | POST   | Edita los datos de un usuario específico                 |
| /api/user/{id}        | DELETE | Borra todos los datos de un usuario específico           |

# Instalación

### Requisitos previos

Antes de comenzar, asegúrate de que tu sistema cumple con los siguientes requisitos:

-   [Docker](https://docs.docker.com/get-docker/)
-   [Composer](https://getcomposer.org/download/)
-   [Postman Agent](https://www.postman.com/downloads/postman-agent/)
-   PHP 8.0.2

### Clonar el repositorio

Install the dependencies and devDependencies and start the server.

Clona este repositorio en tu máquina local:

```sh
bash
git clone https://github.com/abdeleb/api-clicko.git api-clicko-technical-test
```

Accede al directorio del proyecto:

```sh
bash
cd api-clicko-technical-test
```

Copia el archivo de entorno de ejemplo:

```sh
bash
cp .env.example .env
```

Edita el archivo .env con la configuración específica de tu proyecto, como las credenciales de la base de datos y cualquier otra variable de entorno necesaria. Por defecto se usará la configuración de Laravel Sail (Docker).

### Iniciar el entorno de desarrollo

Copia el archivo de entorno de ejemplo:

```sh
bash
./vendor/bin/sail up
```

Este paso puede llevar un tiempo en la primera ejecución, ya que Docker descargará las imágenes necesarias.

```sh
bash
./vendor/bin/sail composer install
```

Nota: En caso de requerir una configuración específica de Docker, puede hacerlo en el archivo docker-compose.yml.

### Migraciones y Seeders

Ejecuta la migración de la base de datos

```sh
bash
./vendor/bin/sail artisan migrate
```

Esto generará 20 usuarios de forma aleatoria.

```sh
bash
./vendor/bin/sail artisan db:seed --class=UserSeeder
```

### Testing

Para garantizar el correcto funcionamiento del proyecto, he desarrollado varios test por cada método disponible.

Ejecuta el siguiente comando para iniciar los test

```sh
bash
./vendor/bin/sail test
```

# Instrucciones para Interactuar con la API en Local

Esta guia a través de la interacción con los endpoints de la API en tu entorno local utilizando Postman. Antes de realizar las siguientes solicitudes, asegúrate de que tu proyecto Laravel con Sail Docker esté en ejecución y tener abierto Postman Agent.

Recuerda que para interactuar con la API debes autenticarte siguiendo los siguientes pasos:

### Base URL

La URL base para las solicitudes de autenticación en tu entorno local será: http://localhost/api/
Una vez autentificado, la URL base será http://localhost/api/user/

#### Header

```
{
    "Accept" = "application/json",
    "Content-Type" = "application/json",
}
```

### Registro de usuario y generación de token de acceso

Endpoint: /register
Método: POST
Descripción: Registra un nuevo usuario y genera un token de acceso. Si utilizas el token generado para iniciar sesión, no será necesario hacer login.
Body:

```
{
    "name" = "randomNameTest",
    "email" = "randomNameTest@clicko.es",
    "password" = "randompassword123"
}
```

URL: http://localhost/api/register

### Login y Generación de Token de Acceso

Endpoint: /login
Método: POST
Descripción: Autenticación de usuario y genera un token de acceso.
Body:

```
{
    "email" = "randomNameTest@clicko.es",
    "password" = "randompassword123"
}
```

Nota: Copia el token generado.

URL: http://localhost/api/login

### Autenticación

Una vez copiada el token generado en login/registro. Accede a "Authorization", selecciona Bearer Token en el menú desplegable Type y pega la clave token.

### Cierre de sesión y eliminación de Tokens de Acceso

Endpoint: /logout
Método: GET
Descripción: Cierra la sesión del usuario y automáticamente se eliminan todos los tokens de acceso asociados al usuario.
Body:

```
{
    "email" = "randomNameTest@clicko.es",
    "password" = "randompassword123"
}
```

URL: http://localhost/api/logout

### Mostrar todos los usuarios

Endpoint: /user/
Método: GET
Descripción: Muestra todos los usuarios de la tabla "users".
Solicitud: Envía una solicitud GET para obtener la lista de usuarios registrados.
URL: http://localhost/api/user/

### Mostrar los Top 3 dominios de correo junto a la cantidad de forma descendiente

Endpoint: /user/top-domains
Método: GET
Descripción: Muestra los tres dominios de correo más comunes junto con la cantidad de usuarios que los utilizan.
Solicitud: Envía una solicitud GET para obtener los dominios de correo más comunes.
URL: http://localhost/api/user/top-domains

### Crear un usuario nuevo

Endpoint: /user/create
Método: POST
Descripción: Crea un nuevo usuario en la base de datos.
Solicitud: Envía una solicitud POST con los datos del nuevo usuario (por ejemplo, nombre, correo electrónico y contraseña).
URL: http://localhost/api/user/create

### Mostrarlos datos de un usuario específico

Endpoint: /user/{id}
Método: GET
Descripción: Muestra los datos de un usuario específico según su ID.
Solicitud: Envía una solicitud GET con el ID del usuario como parte de la URL.
URL: http://localhost/api/user/{id} (reemplaza {id} con el ID del usuario deseado).

### Editar los datos de un usuario específico

Endpoint: /user/edit/{id}
Método: POST
Descripción: Edita los datos de un usuario específico según su ID.
Solicitud: Envía una solicitud POST con el ID del usuario como parte de la URL y los datos actualizados del usuario.
URL: http://localhost/api/user/edit/{id} (reemplaza {id} con el ID del usuario a editar).

### Borrar los datos de un usuario específico

Endpoint: /user/{id}
Método: DELETE
Descripción: Borra todos los datos de un usuario específico según su ID.
Solicitud: Envía una solicitud DELETE con el ID del usuario como parte de la URL.
URL: http://localhost/api/user/{id} (reemplaza {id} con el ID del usuario a eliminar).

¡Ahora puedes utilizar Postman para interactuar con la API de tu proyecto Laravel en tu entorno local! Asegúrate de incluir los datos necesarios en las solicitudes, como credenciales de autenticación o información de usuario, según corresponda.
