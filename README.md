# Boc·ajarro

**Boc·ajarro** es una aplicación que facilita la gestión de la descarga del Boletín Oficial de Canarias y el procesamiento de los datos descargados para su análisis y estudio.

## Procesos no destructivos

**Boc·ajarro** separa la descarga del procesamiento de los datos. Cuando se descarga contenido de una página, los datos se guardan exactamente como fueron descargados.

Los procesos que se ejecutan más tarde sobre los datos descargados, crean nuevos registros en tablas específicas para datos procesados, sin modificar en ningún momento lo descargado.

### Reejecuciones parciales

Esto facilita que en caso de fallo, o de mejora de los algoritmos de procesamiento, no sea necesaria una nueva ejecución entera del proceso, pudiendo ejecutarse solo las partes que hayan cambiado.

### Auditorías a posterior

Además de facilitar las reejecuciones de los procesos en caso de fallo, la no destructividad también facilita la auditoría de los procesos, al poder comprobarse los mismos desde su entrada original de datos.

## Instalación

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

## Documentación

### Descarga de archivos

El [Boletín Oficial de Canarias](https://www.gobiernodecanarias.org/boc/) tiene una página, llamada el [Archivo de boletines](https://www.gobiernodecanarias.org/boc/archivo/), que contiene enlaces a cada uno de los años en los que se ha publicado algún boletín.

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

#### Extracción de enlaces

Una vez descargada la página que contiene la lista de años para los que se han publicado boletines, obtenemos la lista con esos enlaces.

El trabajo [ExtractArchiveLinks](app/Jobs/Boc/ExtractArchiveLinks.php) se encarga de leer un registro de la tabla `page`, comprobar que se trata de una descarga del archivo de boletines y, si es así, extraer sus enlaces a las páginas anuales.

Los enlaces descargados se guardan en la tabla `link`, donde se guardan estos campos:

-   `id` identificador único del enlace.
-   `page_id` identificador de la página en la que se encontró el enlace.
-   `url` enlace en versión absoluta.
-   `created_at` fecha y hora a la que se procesó el enlace.

### Descarga de índices anuales

Para cada año en que se ha publicado algún boletín, hay un índice con cada uno de los boletines. El índice empieza en [1980](https://www.gobiernodecanarias.org/boc/archivo/1980/), cuando solo se publicaron cuatro boletines.

El trabajo [DownloadYearIndex](app/Jobs/Boc/DownloadYearIndex.php) se encarga de descargar el contenido de esos índices y guardarlo sin procesar en la tabla `page`. Si durante la descarga encuentra contenido nuevo, dispara la correspondiente extracción de enlaces.

Este proceso puede ejecutarse manualmente lanzando:

```php
App\Jobs\Boc\DownloadYearIndex::dispatch(1980)->handle();
```

#### Extracción de enlaces

El trabajo [ExtractYearIndexLinks](app/Jobs/Boc/ExtractYearIndexLinks.php) se encarga de analizar las páginas de índices anuales descargadas y extraer de ellas los enlaces a boletines que contengan.

## Desarrollo

**Boc·ajarro**:

-   Es una aplicación [Laravel](https://laravel.com).
-   Revisa el estilo del código utilizando [Pint](https://laravel.com/docs/11.x/pint).
-   Testea el código utilizando [Pest](https://pestphp.com).

## Tareas programadas

**Boc·ajarro** se apoya en tareas programadas que pueden consultarse con:

```bash
php artisan schedule:list
```

```
0 0 \* \* \* App\Jobs\Boc\DownloadArchive ............... Next Due: 1 hour from now
```

Las descargas de páginas del archivo, cuando encuentran contenido nuevo, disparan automáticamente el proceso de sus correspondientes enlaces. Por lo que el proceso de enlaces no necesita ser programado como tarea.

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
