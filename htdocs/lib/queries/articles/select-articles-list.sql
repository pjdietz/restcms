SELECT
    a.articleId,
    a.slug,
    a.contentType,
    a.status,
    av.title,
    av.excerpt
FROM
    article a
    JOIN articleVersion av
        ON a.currentArticleVersionId = av.articleVersionId
ORDER BY
    a.dateCreated DESC;