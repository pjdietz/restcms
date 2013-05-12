SELECT
    av.articleVersionId,
    av.dateCreated,
    av.title,
    av.content,
    av.excerpt,
    av.notes
FROM
    article a
    JOIN articleVersion av
        ON a.articleId = av.parentArticleId
        AND a.slug = :slug
WHERE 1 = 1
    AND av.articleVersionId = :articleVersionId;