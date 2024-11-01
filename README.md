# Boc·ajarro

**Boc·ajarro** es una aplicación que facilita la gestión de la descarga del Boletín Oficial de Canarias y el procesamiento de los datos descargados para su análisis y estudio.

## Documentación

La documentación está disponible en la carpeta [docs/](docs/).

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

## Desarrollo

**Boc·ajarro**:

- Es una aplicación [Laravel](https://laravel.com).
- Revisa el estilo del código utilizando [Pint](https://laravel.com/docs/11.x/pint).
- Testea el código utilizando [Pest](https://pestphp.com).

## Tareas programadas

**Boc·ajarro** se apoya en tareas programadas que pueden consultarse con:

```bash
php artisan schedule:list
```

```
  0   0 * * *  App\Jobs\Boc\DownloadRobots ....................... Next Due: 4 hours from now
  5   0 * * *  App\Jobs\Boc\DownloadArchive ...................... Next Due: 4 hours from now
  *   * * * *  App\Jobs\Boc\FollowLinksFoundInArchive ......... Next Due: 42 seconds from now
  *   * * * *  App\Jobs\Boc\FollowLinksFoundInYearIndex ....... Next Due: 42 seconds from now
  *   * * * *  App\Jobs\Boc\FollowLinksFoundInBulletinIndex ... Next Due: 42 seconds from now
  0   6 * * *  App\Jobs\TakeSnapshot ............................ Next Due: 10 hours from now
  */5 * * * *  php artisan horizon:snapshot ................... Next Due: 42 seconds from now
```

> [!NOTE]
> Las descargas de páginas del archivo, cuando encuentran contenido nuevo, disparan automáticamente el proceso de sus correspondientes enlaces. Por lo que el proceso de enlaces no necesita ser programado como tarea.

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
