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

### Tareas programadas

**Boc·ajarro** se apoya en tareas programadas que pueden consultarse con:

```bash
php artisan schedule:list
```

```
0 0 \* \* \* App\Jobs\Boc\DownloadArchives ......... Next Due: 1 hour from now
```

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
