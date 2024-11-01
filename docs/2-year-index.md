# Boc·ajarro

## Documentación

### Descarga de índices anuales

Para cada año en que se ha publicado algún boletín, hay un índice con cada uno de los boletines publicados ese año. El índice empieza en [1980](https://www.gobiernodecanarias.org/boc/archivo/1980/), cuando solo se publicaron cuatro boletines.

![Captura de pantalla del índice de boletines de 1980](screenshots/year-index.png)

El trabajo [DownloadYearIndex](../app/Jobs/Boc/DownloadYearIndex.php) se encarga de descargar el contenido de esos índices y guardarlo sin procesar en la tabla [page](README.md#la-tabla-page).

### Extracción de enlaces

El trabajo [ExtractLinksFromYearIndex](../app/Jobs/Boc/ExtractLinksFromYearIndex.php) se encarga de analizar las páginas de índices anuales descargadas y extraer de ellas los enlaces a boletines.

Este proceso es ejecutado automáticamente por los trabajos de descarga de índices anuales si durante su descarga se encuentró contenido nuevo.

### Seguimiento de enlaces

Los enlaces extraídos de los índices anuales contienen enlaces a los índices con el contenido de cada uno de los boletines. De seguir esos enlaces y programar las siguientes descargas, se encarga el trabajo [FollowLinksFoundInYearIndex](../app/Jobs/Boc/FollowLinksFoundInYearIndex.php).

A cada uno de estos enlaces le corresponde un [índice de boletín](3-bulletin-index.md).
