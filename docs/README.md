# Boc·ajarro

## Documentación

1. [Archivo de boletines](1-archive.md)
2. [Índices anuales](2-year-index.md)
3. [Índices de boletines](3-bulletin-index.md)
4. [Artículos de boletines](4-bulletin-article.md)

## Estructura interna

### La tabla `page`

La tabla `page` guarda contenidos de páginas descargadas del boletín, sin importar de qué tipo de página se trata. Los datos que se guardan de cada página son:

- `id` identificador único de la descarga.
- `name` nombre del recurso descargado.
- `url` dirección en la que se encontró el contenido descargado.
- `content` el contenido (HTML) descargado.
- `created_at` la fecha y hora en la que se realizó la descarga.

#### Comprobación de unicidad

Las páginas del Boletín Oficial de Canarias, en general, no cambian una vez son publicadas. Por lo que es de esperar que si descargamos una de ellas más de una vez, obtengamos los mismos datos.

Para mantener controlado el tamaño de la base de datos, durante la descarga de páginas se comprueba si el contenido descargado es idéntico al último contenido que se descargó de esa misma página y, de ser así, vincula ambas descargas dejando vacío el campo contenido de la nueva.

> [!NOTE]
> El vínculo entre dos registros se hace únicamente si son contiguos. Es decir, si no hay más registros del mismo tipo entre medias con diferente contenido.

El campo que se utiliza para el vínculo es:

- `shared_content_with_page_id` que contiene el identificador de la siguiente descarga del mismo tipo en la que se encontró el mismo contenido.

### La tabla `link`

Muchas de las páginas que descargamos del Boletín, las descargamos para extraer enlaces de ellas para, más tarde, seguir esos enlaces hasta llegar a los artículos; que es lo que realmente queremos descargar.

Los enlaces descargados se guardan en la tabla `link`, donde se guardan estos campos:

- `id` identificador único del enlace.
- `type` tipo de enlace.
- `page_id` identificador de la página en la que se encontró el enlace.
- `url` enlace en versión absoluta.
- `created_at` fecha y hora a la que se procesó el enlace.

El tipo de enlace puede tener uno de estos valores:

1. `Root`: raíz del sitio web del Boletín Oficial de Canarias.
2. `Robots`: fichero robots.txt con detalle de páginas bloqueadas para buscadores.
3. `Archive`: artchivo de boletines anuales publicados.
4. `YearIndex`: índices de cada uno de los boletines publicados cada año.
5. `BulletinIndex`: índece con el contenido de cada uno de los boletines.
6. `BulletinArticle`: artículos publicados.