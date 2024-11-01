# Boc·ajarro

## Documentación

### Descarga de índices de boletines

Para cada uno de los boletines que han sido publicados, hay una página con enlaces a cada uno de sus artículos.

![Captura de pantalla del primer boletín de 1980](screenshots/bulletin-index.png)

El trabajo [DownloadBulletinIndex](../app/Jobs/Boc/DownloadBulletinIndex.php) se encarga de descargar el contenido de esos índices y guardarlo sin procesar en la tabla [page](README.md#la-tabla-page).

### Extracción de enlaces

El trabajo [ExtractLinksFromBulletinIndex](../app/Jobs/Boc/ExtractLinksFromBulletinIndex.php) se encarga de analizar las páginas de índices de boletines descargadas y extraer de ellas los enlaces a sus artículos.

Este proceso es ejecutado automáticamente por los trabajos de descarga de índices anuales si durante su descarga se encuentró contenido nuevo.

### Seguimiento de enlaces

Los enlaces extraídos de los índices de boletines contienen enlaces a cada uno de los artículos de los boletines. De seguir esos enlaces y programar las siguientes descargas, se encarga el trabajo [FollowLinksFoundInBulletinIndex](../app/Jobs/Boc/FollowLinksFoundInBulletinIndex.php).

A cada uno de estos enlaces le corresponde un [artículo de boletín](4-bulletin-article.md).
