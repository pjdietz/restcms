SELECT
    a.articleId,
    a.slug,
    a.contentType,
    a.status,
    av.title,
    av.content,
    av.excerpt,
    av.notes
FROM
    article a
    JOIN articleVersion av
        ON a.currentArticleVersionId = av.articleVersionId
WHERE 1 = 1
    AND a.articleId = ?
LIMIT 1;