## Boc·ajarro

**Boc·ajarro** es una aplicación que facilita la gestión de la descarga del Boletín Oficial de Canarias y el procesamiento de los datos descargados para su análisis y estudio.

### Procesos no destructivos

**Boc·ajarro** separa la descarga del procesamiento de los datos. Cuando se descarga contenido de una página, los datos se guardan exactamente como fueron descargados.

Los procesos que se ejecutan más tarde sobre los datos descargados, crean nuevos registros en tablas específicas para datos procesados, sin modificar en ningún momento lo descargado.

#### Reejecuciones parciales

Esto facilita que en caso de fallo, o de mejora de los algoritmos de procesamiento, no sea necesaria una nueva ejecución entera del proceso, pudiendo ejecutarse solo las partes que hayan cambiado.

#### Auditorías a posterior

Además de facilitar las reejecuciones de los procesos en caso de fallo, la no destructividad también facilita la auditoría de los procesos, al poder comprobarse los mismos desde su entrada original de datos.

### Desarrollo

**Boc·ajarro**:

-   es una aplicación [Laravel](https://laravel.com)
-   revisa el estilo del código utilizando [Pint](https://laravel.com/docs/11.x/pint)
-   testea el código utilizando [Pest](https://pestphp.com)

### Instalación

**Boc·ajarro** se instala como una aplicación **Laravel** estándar:

```bash
git clone https://github.com/estudio-hawara/boc-ajarro
cd boc-ajarro

composer install
```

Para iniciar una sesión de desarrollo, lanza:

```bash
composer run dev
```

### Tareas programadas

**Boc·ajarro** se apoya en tareas programadas que pueden consultarse con:

```bash
php artisan schedule:list
```

```
0 0 \* \* \* App\Jobs\Boc\DownloadArchive ............... Next Due: 1 hour from now
```

#### Descarga de archivos

El [Boletín Oficial de Canarias](https://www.gobiernodecanarias.org/boc/) tiene una página, llamada el [Archivo de boletines](https://www.gobiernodecanarias.org/boc/archivo/) que contiene enlaces a cada uno de los años en los que se ha publicado algún boletín.

El trabajo [DownloadArchive](app/Jobs/Boc/DownloadArchive.php) se encarga de descargar esta página y guardar su contenido sin procesar en la tabla `page`. Los datos que se guardan son:

-   `id` identificador único de la descarga.
-   `name` nombre del recurso descargado (en este caso: **Archive**).
-   `content` el HTML descargado.
-   `created_at` la fecha y hora en la que se realizó la descarga.

Este proceso puede ejecutarse manualmente lanzando:

```php
App\Jobs\Boc\DownloadArchive::dispatch()->handle();
```

#### Comprobación de unicidad

Páginas como el archivo no cambian a menudo por lo que es de esperar que muchas de las veces que la descarguemos, obtengamos los mismos datos.

Para mantener controlado el tamaño de la base de datos, durante la descarga de páginas se comprueba si el contenido descargado es idéntico al último y, de ser así, vincula ambas descargas dejando vacío el campo contenido de la nueva.

> [!NOTE]
> El vínculo entre dos registros se hace únicamente si son contiguos. Es decir, si no hay más registros del mismo tipo entre medias con diferente contenido.

El campo que se utiliza para el vínculo es:

-   `shared_content_with_page_id` que contiene el identificador de la siguiente descarga del mismo tipo en la que se encontró el mismo contenido.

### Tests

**Boc·ajarro** dispone de un comando para lanzar los tests automatizados:

```bash
./vendor/bin/pest --coverage
```

### Estilo de código

**Boc·ajarro** también dispone de un comando para corregir errores de estilo en el código:

```bash
./vendor/bin/pint
```

### Licencia

**Boc·ajarro** es software libre. Puedes descargarlo, copiarlo, modificarlo y distribuirlo tanto con como sin modificaciones. Se distribuye bajo una [licencia MIT](https://opensource.org/licenses/MIT).
