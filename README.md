# LunarMC - Trabajo Práctico N.º 7

## Programación Web

### Descripción del proyecto

LunarMC es una tienda web de Minecraft desarrollada como proyecto para la materia Programación Web.

La página permite visualizar rangos y productos del servidor, consultar sus características y administrar la información de la tienda.

En el Trabajo Práctico N.º 7 se trabajó principalmente con PHP y MySQL para implementar un sistema de gestión de productos mediante operaciones CRUD.

## Tecnologías utilizadas

- **HTML:** estructura de las páginas web.
- **CSS:** diseño y estilos de la tienda.
- **JavaScript:** funciones e interacciones de la página.
- **PHP:** procesamiento de datos del lado del servidor.
- **MySQL:** almacenamiento de información.
- **XAMPP:** ejecución del proyecto en un servidor local.
- **phpMyAdmin:** administración de la base de datos.

## Funcionalidades desarrolladas

### Conexión a la base de datos

Se utiliza PHP para conectar la tienda con una base de datos MySQL, permitiendo consultar y gestionar los registros.

### Sistema CRUD

Se implementaron las cuatro operaciones principales:

- **Crear:** registrar nuevos productos.
- **Leer:** consultar los productos almacenados.
- **Actualizar:** modificar los datos de los productos.
- **Eliminar:** borrar productos de la base de datos.

### Panel de administración

El proyecto cuenta con un apartado de administración desde el cual se pueden gestionar los productos de la tienda.

### Tienda de Minecraft

La página contiene diferentes secciones donde se muestran rangos, extras y sus características.

También cuenta con un diseño personalizado, botones interactivos y un sistema de modo claro y oscuro.

## Estructura del repositorio

### Carpeta `lunarmc`

Contiene los archivos del proyecto web, incluyendo las páginas PHP, los estilos CSS, JavaScript y otros recursos necesarios para la tienda.

### Carpeta `Captura tp7`

Contiene las capturas de pantalla utilizadas como evidencia del funcionamiento del trabajo práctico.

## Cómo ejecutar el proyecto

1. Instalar XAMPP.
2. Iniciar los servicios Apache y MySQL.
3. Copiar la carpeta `lunarmc` dentro de `C:\xampp\htdocs\`.
4. Entrar a `http://localhost/phpmyadmin/`.
5. Crear o importar la base de datos del proyecto.
6. Verificar los datos de conexión de PHP con MySQL.
7. Abrir `http://localhost/lunarmc/` desde el navegador.

**Nota:** para ejecutar las funciones PHP y MySQL se necesita un servidor compatible. GitHub Pages no ejecuta PHP directamente.

## Evidencias del trabajo

Las capturas del funcionamiento del TP7 se encuentran en la carpeta `Captura tp7` del repositorio.

Estas imágenes permiten mostrar el funcionamiento de la tienda y las funcionalidades implementadas.

## Objetivo del TP7

Aplicar los conocimientos de programación del lado del servidor utilizando PHP y MySQL, desarrollando un sistema capaz de almacenar, consultar, modificar y eliminar información de una base de datos.

## Conclusión

Con este trabajo práctico se pudo avanzar en el desarrollo de LunarMC, incorporando funciones dinámicas mediante PHP y MySQL.

También se trabajó con la administración de productos y la conexión con la base de datos, comprendiendo mejor cómo funciona una página web que utiliza información almacenada en un servidor.




## Capturas del Trabajo Práctico N.º 7

A continuación se muestran las capturas de las funcionalidades implementadas en la tienda LunarMC.

### 1. Crear producto (CREATE)

Permite registrar un nuevo producto en la tienda mediante el panel de administración.

![Crear producto](Captura%20tp7/01-crear-producto.png)

### 2. Listar productos (READ)

Muestra los productos registrados en la base de datos.

![Listar productos](Captura%20tp7/02-listar-productos.png)

### 3. Editar producto (UPDATE)

Permite modificar la información de un producto existente.

![Editar producto](Captura%20tp7/03-editar-producto.png)

### 4. Eliminar producto (DELETE)

Permite eliminar un producto registrado.

![Eliminar producto](Captura%20tp7/04-eliminar-producto.png)

### 5. Base de datos MySQL

Se utiliza MySQL para almacenar los productos y sus datos.

![Base de datos MySQL](Captura%20tp7/05-mysql.png)

### 6. Conexión PDO

La conexión entre PHP y MySQL se realiza mediante PDO.

![Conexión PDO](Captura%20tp7/conexion%20pdo%20db.png)

### 7. Protección contra XSS

Se utiliza htmlspecialchars() para evitar que el navegador interprete código HTML ingresado por los usuarios.

![Protección XSS](Captura%20tp7/proteccion%20xss.png)
