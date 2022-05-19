# PruebaCasino
A continuación se enumeran los pasos necesarios para el despliegue en local de la aplicación.

## Las instrucciones de instalación y ejecución  

Para ejecutar el proyecto en un sistema UNIX se neceita realizar los siguientes pasos

### Paso 1 Instalar php
Descargar el repositorio de las versiones de php 

  `sudo add-apt-repository ppa:ondrej/php`

Instalación de php 7.4:
  
  `sudo apt install php7.4`

### Paso 2 Instalar composer

Instalar composer con el siguiente comando:

 `sudo apt install composer`
 
 ### Paso 3 Instalción de SymfonyCLI
 
  `sudo wget https://get.symfony.com/cli/installer -O - | bash`

Mover SymfonyCLI para que funcione de forma global

  `sudo mv /home/steve/.symfony/bin/symfony /usr/local/bin/symfony`

### Paso 4 Varificar requerimientos

  `symfony check:requirements`

instalar mysql:

  `sudo apt install php7.4-mysql`

### Ejecutar el proyecto

Abrir en la terminal de la linea de comandos la ubicación del proyecto o si esta utilizando VisualStudio Code abrir la terminal y ejecutar el siguiente comando para iniciar el servidor local

`symfony server:start`

En el navegador abrir la direccion url http:://127.0.0.1:8000
