# Boc·ajarro

## Documentación

### Descarga de archivos

El [Boletín Oficial de Canarias](https://www.gobiernodecanarias.org/boc/) tiene una página, llamada el [Archivo de boletines](https://www.gobiernodecanarias.org/boc/archivo/), que contiene enlaces a cada uno de los años en los que se ha publicado algún boletín.

![Captura de pantalla del archivo de boletines](screenshots/archive.png)

El trabajo [DownloadArchive](../app/Jobs/Boc/DownloadArchive.php) se encarga de descargar esta página y guardar su contenido sin procesar en la tabla [page](README.md#la-tabla-page).

Este proceso puede ejecutarse manualmente lanzando:

```php
App\Jobs\Boc\DownloadArchive::dispatch()->handle();
```

### Extracción de enlaces

Una vez descargada la página que contiene la lista de años para los que se han publicado boletines, obtenemos la lista con esos enlaces.

El trabajo [ExtractLinksFromArchive](../app/Jobs/Boc/ExtractLinksFromArchive.php) se encarga de leer un registro tipo archivo de boletines de la tabla **page** y extraer los enlaces que contenga a páginas anuales, guardándolos en la tabla [link](README.md#la-tabla-link).

Este proceso es ejecutado automáticamente por los trabajos de descarga de artchivos si durante su descarga se encuentró contenido nuevo.

### Seguimiento de enlaces

Los enlaces extraídos del archivo de boletines contienen enlaces a los índices anuales de boletines. De seguir esos enlaces y programar las siguientes descargas, se encarga el trabajo [FollowLinksFoundInArchive](../app/Jobs/Boc/FollowLinksFoundInArchive.php).

A cada uno de estos enlaces le corresponde un [índice anual](2-year-index.md).
