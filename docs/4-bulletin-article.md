# Boc·ajarro

## Documentación

### Descarga de artículos

Para cada uno de los artículos que componen un boletín, hay una página con su contenido.

![Captura de pantalla del primer artículo del primer boletín de 1980](screenshots/bulletin-article.png)

El trabajo [DownloadBulletinArticle](../app/Jobs/Boc/DownloadBulletinArticle.php) se encarga de descargar el contenido de esos artículos y guardarlo sin procesar en la tabla [page](README.md#la-tabla-page).

Los artículos son el punto final de la cadena de descarga, en tanto que no contienen más enlaces que deban ser extraídos y seguidos.
