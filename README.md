En el repositorio encontraremos tanto el código del back end como del front end.
Para empezar debemos descargar el archivo ZIP del código fuente y descomprimirlo en un servidor local (apache) en un nivel accesible desde URL.
--------------------------------------------
Creación y configuración de base de datos
1. Iniciar los servicios del servidor apache y base de datos MySQL
2. Abrimos el archivo "Script BD.sql" ubicado en la raiz del repositorio
3. Copiamos y pegamos en el cliente de MySQL (PHPMyAdmin) el contenido del archivo
4. Ejecutamos el script
5. Abrimos el archivo "Settings.php" ubicado en la raiz del repositorio
6. Configuramos las constantes relacionadas con la conexión a base de datos
   * ServerDB
   * UserDB
   * PasswordDB
   * NameDB
--------------------------------------------
Configuración cliente (Angular)
1. Abrimos el archivo "proxyconfig.json" y modificamos la primera linea la cual tiene un valor de '"/Proyectos/PruebaTecnica/*": {'.
   Especificamente el texto 'Proyectos/PruebaTecnica' se debe reemplazar por la ruta donde se descomprimio el ZIP, donde la raiz es la carpeta "htdocs" o "wwww"
   Debe quedar similar a esto '"/ruta-proyecto/*": {'.
2. Abrimos el archivo "api.service.ts" ubicado en "App/Client/prueba-tecnica-fenix/src/services/".
   En la linea 12 nos encontraremos con la URL donde el cliente de angular realizara las peticiones HTTP la ruta tiene la forma '/Proyectos/PruebaTecnica/index.php'
   debemos reemplazar el texto 'Proyectos/PruebaTecnica' por la ruta donde se descomprimio el proyetco ZIP debe quedar similar al paso anterior con la forma
   '/ruta-proyecto/index.php'
3. Instalamos las dependencias del proyecto. Utilizando una de las terminales del sistema nos posicionamos en la ruta "App/Client/prueba-tecnica-fenix" y ejecutamos el comando "npm install".
   Para que este comando funcione el equipo debe tener el entorno de desarrollo de angular correctamente instalado y configurado.
   Ver: https://angular.io/guide/setup-local
 4. En este punto las configuraciones estaran finalizadas, ahora solo resta ejecutar la aplicación.
 5. Utilizando una de las terminales del sistema nos posicionamos en la ruta  "App/Client/prueba-tecnica-fenix" y ejecutamos el comando "ng serve -o". Si todo salio bien la aplicación se ejecutara
    Usuario: admin@yopmail.com
    Clave: 123 
